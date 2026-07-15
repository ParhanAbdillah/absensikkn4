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
        
        $totalMembers   = User::where('role', 'anggota')->count();
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

    public function sendReminder(Schedule $schedule)
    {
        $today = Carbon::today();
        $members = User::where('role', 'anggota')->where('is_active', true)->get();
        $sentCount = 0;

        foreach ($members as $member) {
            $hasAttended = Attendance::where('user_id', $member->id)
                ->where('schedule_id', $schedule->id)
                ->exists();

            if (!$hasAttended && $member->phone) {
                \Illuminate\Support\Facades\Artisan::call('attendance:send-reminder');
                $sentCount++;
                break;
            }
        }

        return redirect()->back()->with('success', 'Pengingat WhatsApp berhasil dikirim ke anggota kelompok yang belum absen.');
    }
}
