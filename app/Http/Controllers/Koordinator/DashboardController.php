<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\Location;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        $totalMembers   = User::members()->count();
        $totalLocations = Location::count();
        
        // Hitung total absen masuk hari ini
        $hadirCount = Attendance::whereDate('check_in_at', $today)->count();
        $hadirPersentase = $totalMembers > 0 ? round(($hadirCount / $totalMembers) * 100) : 0;
        $todayAttendancesCount = $hadirCount;
            
        // Kehadiran hari ini
        $todayAttendances = Attendance::with(['user', 'location'])
            ->whereDate('check_in_at', $today)
            ->latest()
            ->get();

        // Data 7 hari terakhir untuk bar chart
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = Attendance::whereDate('check_in_at', $date)->count();
            $weeklyData[] = [
                'label' => $date->isoFormat('ddd'),
                'count' => $count,
                'date'  => $date->toDateString(),
            ];
        }

        return view('koordinator.dashboard', compact(
            'totalMembers', 
            'totalLocations',
            'hadirCount',
            'hadirPersentase',
            'todayAttendancesCount',
            'todayAttendances',
            'weeklyData'
        ));
    }

    public function sendReminder(Schedule $schedule, \App\Services\FonnteService $fonnteService)
    {
        $result = $fonnteService->sendScheduleReminder($schedule);

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->back()->with('success', $result['message']);
    }
}
