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
        $user = auth()->user();
        $today = Carbon::today();
        
        // Cek apakah sudah absen hari ini
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('check_in_at', $today)
            ->first();
            
        // Jadwal hari ini
        $todaySchedules = Schedule::with('location')
            ->whereDate('activity_date', $today)
            ->where('is_active', true)
            ->get();
            
        // Riwayat absensi pribadi
        $history = Attendance::with('schedule.location')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('anggota.dashboard', compact('todayAttendance', 'todaySchedules', 'history'));
    }
}
