<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendAttendanceReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:send-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi pengingat WhatsApp bagi anggota kelompok yang belum melakukan absensi harian';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $now = Carbon::now();

        // 1. Ambil jadwal terdekat hari ini yang sedang/akan berlangsung
        $schedules = Schedule::whereDate('activity_date', $today)
            ->where('is_active', true)
            ->get();

        if ($schedules->isEmpty()) {
            $this->info('Tidak ada jadwal kegiatan untuk hari ini.');
            return;
        }

        foreach ($schedules as $schedule) {
            $startTime = Carbon::parse($schedule->start_time);
            
            // Pengingat dikirim dalam rentang 15 menit sebelum kegiatan dimulai
            $diffInMinutes = $now->diffInMinutes($startTime, false);

            // Validasi: hanya kirim pengingat jika waktu saat ini mendekati waktu mulai kegiatan (misal ±15 menit)
            // Atau bisa dijalankan manual via dashboard Koordinator
            
            // Ambil semua anggota kelompok
            $members = User::members()->where('is_active', true)->get();

            foreach ($members as $member) {
                // Cek apakah anggota ini sudah melakukan absensi untuk jadwal terkait
                $hasAttended = Attendance::where('user_id', $member->id)
                    ->where('schedule_id', $schedule->id)
                    ->exists();

                if (!$hasAttended) {
                    $this->sendWhatsAppReminder($member, $schedule);
                }
            }
        }

        $this->info('Notifikasi pengingat absensi berhasil diproses.');
    }

    /**
     * Kirim notifikasi menggunakan Fonnte API Gateway (WhatsApp gratis/berbayar lokal)
     */
    private function sendWhatsAppReminder(User $user, Schedule $schedule)
    {
        // Pastikan nomor WhatsApp terisi dan valid
        if (!$user->phone) {
            Log::warning("Gagal mengirim WA ke {$user->name}: Nomor telepon kosong.");
            return;
        }

        $token = env('FONNTE_TOKEN', 'YOUR_FONNTE_TOKEN_HERE');
        
        $message = "Halo *{$user->name}*,\n\n"
                 . "Diingatkan untuk segera melakukan absensi kehadiran kegiatan KKN:\n"
                 . "📌 *Kegiatan:* {$schedule->title}\n"
                 . "🕒 *Waktu Mulai:* " . Carbon::parse($schedule->start_time)->format('H:i') . " WIB\n"
                 . "📍 *Lokasi:* {$schedule->location->name}\n\n"
                 . "Silakan buka link website absensi KKN berikut dari handphone Anda untuk absen menggunakan Wajah & GPS:\n"
                 . url('/login') . "\n\n"
                 . "Terima kasih.";

        try {
            // Fonnte API Endpoint
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $user->phone,
                'message' => $message,
                'countryCode' => '62', // Default Indonesia
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp reminder sent to {$user->name} ({$user->phone})");
            } else {
                Log::error("Fonnte API Error: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Error sending WhatsApp: " . $e->getMessage());
        }
    }
}
