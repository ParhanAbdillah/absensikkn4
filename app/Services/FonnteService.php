<?php

namespace App\Services;

use App\Models\User;
use App\Models\Schedule;
use App\Models\Attendance;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FonnteService
{
    /**
     * Send WhatsApp reminder to members who haven't attended a schedule.
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
            // Check if member has already checked in for this schedule
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
     * Clean phone number format & send message via Fonnte API
     */
    public function sendReminderMessage(User $user, Schedule $schedule, string $token): bool
    {
        // Clean phone number format (e.g. 08123456789 -> 628123456789)
        $phone = preg_replace('/[^0-9]/', '', $user->phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $startTime = Carbon::parse($schedule->start_time)->format('H:i');
        $locationName = $schedule->location ? $schedule->location->name : 'Posko KKN';
        $appUrl = config('app.url', url('/'));

        $message = "Halo *{$user->name}*,\n\n"
                 . "Diingatkan untuk segera melakukan *Absensi Kehadiran* KKN:\n\n"
                 . "📌 *Kegiatan:* {$schedule->title}\n"
                 . "🕒 *Waktu Mulai:* {$startTime} WIB\n"
                 . "📍 *Lokasi:* {$locationName}\n\n"
                 . "Silakan buka link berikut dari HP Anda untuk melakukan absensi (Wajah & GPS):\n"
                 . "👉 {$appUrl}\n\n"
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
