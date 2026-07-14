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
        
        $totalMembers = User::where('role', 'anggota')->count();
        $totalSchedules = Schedule::count();
        $totalLocations = Location::count();
        
        // Ambil jadwal hari ini
        $todaySchedules = Schedule::with('location')
            ->whereDate('activity_date', $today)
            ->get();
            
        // Kehadiran hari ini
        $todayAttendances = Attendance::with('user')
            ->whereDate('check_in_at', $today)
            ->latest()
            ->get();

        return view('koordinator.dashboard', compact(
            'totalMembers', 
            'totalSchedules', 
            'totalLocations', 
            'todaySchedules', 
            'todayAttendances'
        ));
    }
}
