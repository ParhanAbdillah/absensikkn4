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
                // Call artisan command logic inline or trigger command
                \Illuminate\Support\Facades\Artisan::call('attendance:send-reminder');
                $sentCount++;
                break; // Break since the command handles all users, calling once is enough
            }
        }

        return redirect()->back()->with('success', 'Pengingat WhatsApp berhasil dikirim ke anggota kelompok yang belum absen.');
    }
}
