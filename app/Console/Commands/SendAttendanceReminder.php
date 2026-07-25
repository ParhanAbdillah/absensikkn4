<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Schedule;
use App\Services\FonnteService;
use Carbon\Carbon;

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
    public function handle(FonnteService $fonnteService)
    {
        $today = Carbon::today();

        $schedules = Schedule::whereDate('activity_date', $today)
            ->where('is_active', true)
            ->get();

        if ($schedules->isEmpty()) {
            $this->info('Tidak ada jadwal kegiatan aktif untuk hari ini.');
            return;
        }

        foreach ($schedules as $schedule) {
            $this->info("Memproses pengingat WhatsApp untuk jadwal: {$schedule->title}");
            $result = $fonnteService->sendScheduleReminder($schedule);
            $this->info($result['message']);
        }

        $this->info('Proses pengiriman pengingat WhatsApp selesai.');
    }
}

