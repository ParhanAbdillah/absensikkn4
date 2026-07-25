<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FonnteService;

class SendAttendanceReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:send-reminder {session? : Sesi pengingat (pagi, siang, sore)}';

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
        $session = $this->argument('session');

        $this->info("Memproses pengingat WhatsApp harian" . ($session ? " (Sesi {$session})" : "") . "...");

        $result = $fonnteService->sendDailyAttendanceReminder($session);

        if ($result['success']) {
            $this->info($result['message']);
        } else {
            $this->error($result['message']);
        }
    }
}


