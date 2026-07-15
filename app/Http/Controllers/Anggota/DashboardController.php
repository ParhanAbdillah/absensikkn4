<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Schedule;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $today = Carbon::today();
        
        // Cek apakah sudah absen hari ini
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('check_in_at', $today)
            ->first();

        // Riwayat 30 hari untuk streak dan statistik
        $month_start = Carbon::today()->subDays(29);
        $allAttendances = Attendance::where('user_id', $user->id)
            ->where('check_in_at', '>=', $month_start)
            ->get();

        $totalHadir = $allAttendances->count();
        $daysInMonth = 30;
        $persentase  = $daysInMonth > 0 ? round(($totalHadir / $daysInMonth) * 100) : 0;
        
        // Data 7 hari terakhir untuk bar chart
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $hadirOnDay = Attendance::where('user_id', $user->id)
                ->whereDate('check_in_at', $date)
                ->exists();
            $weeklyData[] = [
                'label' => $date->isoFormat('ddd'),
                'count' => $hadirOnDay ? 1 : 0,
                'date'  => $date->toDateString(),
            ];
        }

        // Riwayat absensi terbaru
        $history = Attendance::with(['location'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('anggota.dashboard', compact(
            'todayAttendance',
            'totalHadir',
            'persentase',
            'weeklyData',
            'history'
        ));
    }
}
