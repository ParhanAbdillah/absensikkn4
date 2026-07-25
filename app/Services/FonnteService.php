<?php

namespace App\Services;

use App\Models\User;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FonnteService
{
    /**
     * Send daily attendance reminder at 08:00, 12:00, or 17:00 WIB
     * to active members who haven't checked in today and have no approved leave.
     *
     * @param string|null $session 'pagi' (08:00), 'siang' (12:00), 'sore' (17:00)
     * @return array ['success' => bool, 'message' => string, 'sent' => int, 'failed' => int, 'no_phone' => int]
     */
    public function sendDailyAttendanceReminder(?string $session = null): array
    {
        $token = env('FONNTE_TOKEN');

        if (empty($token) || $token === 'YOUR_FONNTE_TOKEN_HERE') {
            return [
                'success' => false,
                'message' => 'Token Fonnte belum diatur di server. Harap isi FONNTE_TOKEN pada file .env.',
                'sent' => 0,
                'failed' => 0,
                'no_phone' => 0,
            ];
        }

        $today = Carbon::today();
        $now = Carbon::now();

        if (!$session) {
            $hour = (int)$now->format('H');
            if ($hour < 11) {
                $session = 'pagi';
            } elseif ($hour < 15) {
                $session = 'siang';
            } else {
                $session = 'sore';
            }
        }

        $members = User::members()->where('is_active', true)->get();
        $todaySchedule = Schedule::whereDate('activity_date', $today)->where('is_active', true)->first();

        $sentCount = 0;
        $failedCount = 0;
        $noPhoneCount = 0;

        foreach ($members as $member) {
            // Check if member already checked in today
            $hasAttended = Attendance::where('user_id', $member->id)
                ->whereDate('check_in_at', $today)
                ->exists();

            if ($hasAttended) {
                continue;
            }

            // Check if member has an approved leave request today
            $hasLeave = LeaveRequest::where('user_id', $member->id)
                ->whereDate('date', $today)
                ->where('status', 'approved')
                ->exists();

            if ($hasLeave) {
                continue;
            }

            if (empty($member->phone)) {
                $noPhoneCount++;
                Log::warning("Pengingat WA ({$session}) dibatalkan untuk {$member->name}: Nomor telepon belum diisi.");
                continue;
            }

            $success = $this->sendDailyMessage($member, $session, $todaySchedule, $token);
            if ($success) {
                $sentCount++;
            } else {
                $failedCount++;
            }
        }

        if ($sentCount === 0 && $noPhoneCount === 0 && $failedCount === 0) {
            return [
                'success' => true,
                'message' => 'Semua anggota kelompok sudah melakukan absensi atau izin resmi untuk hari ini.',
                'sent' => 0,
                'failed' => 0,
                'no_phone' => 0,
            ];
        }

        $msgParts = [];
        if ($sentCount > 0) {
            $msgParts[] = "terkirim ke {$sentCount} anggota";
        }
        if ($noPhoneCount > 0) {
            $msgParts[] = "{$noPhoneCount} anggota belum ada no HP";
        }
        if ($failedCount > 0) {
            $msgParts[] = "{$failedCount} gagal dikirim";
        }

        return [
            'success' => true,
            'message' => "Pengingat WA Sesi (" . strtoupper($session) . "): " . implode(', ', $msgParts) . '.',
            'sent' => $sentCount,
            'failed' => $failedCount,
            'no_phone' => $noPhoneCount,
        ];
    }

    /**
     * Send WhatsApp reminder to members for a specific schedule.
     *
     * @param Schedule $schedule
     * @param User|null $singleMember
     * @return array ['success' => bool, 'message' => string, 'sent' => int, 'failed' => int, 'no_phone' => int]
     */
    public function sendScheduleReminder(Schedule $schedule, ?User $singleMember = null): array
    {
        $token = env('FONNTE_TOKEN');

        if (empty($token) || $token === 'YOUR_FONNTE_TOKEN_HERE') {
            return [
                'success' => false,
                'message' => 'Token Fonnte belum diatur di server. Harap isi FONNTE_TOKEN pada file .env.',
                'sent' => 0,
                'failed' => 0,
                'no_phone' => 0,
            ];
        }

        $members = $singleMember ? collect([$singleMember]) : User::members()->where('is_active', true)->get();
        
        $sentCount = 0;
        $failedCount = 0;
        $noPhoneCount = 0;

        foreach ($members as $member) {
            $hasAttended = Attendance::where('user_id', $member->id)
                ->where('schedule_id', $schedule->id)
                ->exists();

            if ($hasAttended) {
                continue;
            }

            if (empty($member->phone)) {
                $noPhoneCount++;
                Log::warning("Pengingat WA dibatalkan untuk {$member->name}: Nomor telepon belum diisi.");
                continue;
            }

            $success = $this->sendReminderMessage($member, $schedule, $token);
            if ($success) {
                $sentCount++;
            } else {
                $failedCount++;
            }
        }

        if ($sentCount === 0 && $noPhoneCount === 0 && $failedCount === 0) {
            return [
                'success' => true,
                'message' => 'Semua anggota kelompok sudah melakukan absensi untuk jadwal ini.',
                'sent' => 0,
                'failed' => 0,
                'no_phone' => 0,
            ];
        }

        $msgParts = [];
        if ($sentCount > 0) {
            $msgParts[] = "terkirim ke {$sentCount} anggota";
        }
        if ($noPhoneCount > 0) {
            $msgParts[] = "{$noPhoneCount} anggota belum ada no HP";
        }
        if ($failedCount > 0) {
            $msgParts[] = "{$failedCount} gagal dikirim";
        }

        return [
            'success' => true,
            'message' => 'Notifikasi WA: ' . implode(', ', $msgParts) . '.',
            'sent' => $sentCount,
            'failed' => $failedCount,
            'no_phone' => $noPhoneCount,
        ];
    }

    /**
     * Send individual session message via Fonnte API
     */
    private function sendDailyMessage(User $user, string $session, ?Schedule $schedule, string $token): bool
    {
        $phone = preg_replace('/[^0-9]/', '', $user->phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $appUrl = config('app.url', url('/'));
        $kegiatanInfo = $schedule ? "📌 *Kegiatan:* {$schedule->title}\n" : '';

        if ($session === 'pagi') {
            $titleHeader = "PENGINGAT ABSENSI KKN";
            $bodyText = "Diingatkan untuk segera melakukan *Absensi Kehadiran KKN* hari ini.";
        } elseif ($session === 'siang') {
            $titleHeader = "PENGINGAT ABSENSI KKN (Pukul 12:00 WIB)";
            $bodyText = "Saat ini pukul 12:00 WIB dan Anda *belum melakukan absensi* KKN hari ini. Mohon segera melakukan presensi.";
        } else {
            $titleHeader = "⚠️ PERINGATAN TERAKHIR ABSENSI KKN";
            $bodyText = "Hari ini (pukul 17:00 WIB) Anda *masih belum melakukan absensi* KKN. Segera lakukan presensi kehadiran sebelum hari berakhir!";
        }

        $message = "Halo *{$user->name}*,\n\n"
                 . "🔔 *{$titleHeader}*\n"
                 . "{$bodyText}\n\n"
                 . "{$kegiatanInfo}"
                 . "👉 https://siabsensikkkn.online\n\n"
                 . "Terima kasih.";

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                Log::info("Fonnte WA Daily ({$session}) Success sent to {$user->name} ({$phone})");
                return true;
            } else {
                Log::error("Fonnte WA API Error for {$user->name} ({$phone}): " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Fonnte WA Exception for {$user->name}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clean phone number format & send schedule message via Fonnte API
     */
    public function sendReminderMessage(User $user, Schedule $schedule, string $token): bool
    {
        $phone = preg_replace('/[^0-9]/', '', $user->phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $startTime = Carbon::parse($schedule->start_time)->format('H:i');
        $locationName = $schedule->location ? $schedule->location->name : 'Posko KKN';

        $message = "Halo *{$user->name}*,\n\n"
                 . "Diingatkan untuk segera melakukan *Absensi Kehadiran KKN*:\n"
                 . "📌 *Kegiatan:* {$schedule->title}\n"
                 . "🕒 *Waktu Mulai:* {$startTime} WIB\n"
                 . "📍 *Lokasi:* {$locationName}\n\n"
                 . "👉 https://siabsensikkkn.online\n\n"
                 . "Terima kasih.";

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                Log::info("Fonnte WA Success sent to {$user->name} ({$phone})");
                return true;
            } else {
                Log::error("Fonnte WA API Error for {$user->name} ({$phone}): " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Fonnte WA Exception for {$user->name}: " . $e->getMessage());
            return false;
        }
    }
}
