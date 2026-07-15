<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function rekap(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $selectedDate = Carbon::parse($date);

        $totalMembers = User::where('role', 'anggota')->count();

        $attendances = Attendance::with(['user', 'location'])
            ->whereDate('check_in_at', $selectedDate)
            ->latest('check_in_at')
            ->get();

        $hadirCount  = $attendances->where('status', 'hadir')->count();
        $tidakHadirCount = $totalMembers - $hadirCount;
        $persentase  = $totalMembers > 0 ? round(($hadirCount / $totalMembers) * 100) : 0;

        // Semua anggota yang belum absen hari ini
        $absentUserIds = $attendances->pluck('user_id')->toArray();
        $belumAbsen = User::where('role', 'anggota')
            ->whereNotIn('id', $absentUserIds)
            ->get();

        return view('koordinator.attendance.rekap', compact(
            'attendances',
            'selectedDate',
            'totalMembers',
            'hadirCount',
            'tidakHadirCount',
            'persentase',
            'belumAbsen'
        ));
    }

    public function print(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $selectedDate = Carbon::parse($date);

        $totalMembers = User::where('role', 'anggota')->count();

        $attendances = Attendance::with(['user', 'location'])
            ->whereDate('check_in_at', $selectedDate)
            ->latest('check_in_at')
            ->get();

        $hadirCount      = $attendances->where('status', 'hadir')->count();
        $tidakHadirCount = $totalMembers - $hadirCount;
        $persentase      = $totalMembers > 0 ? round(($hadirCount / $totalMembers) * 100) : 0;

        $absentUserIds = $attendances->pluck('user_id')->toArray();
        $belumAbsen = User::where('role', 'anggota')
            ->whereNotIn('id', $absentUserIds)
            ->get();

        return view('koordinator.attendance.print', compact(
            'attendances',
            'selectedDate',
            'totalMembers',
            'hadirCount',
            'tidakHadirCount',
            'persentase',
            'belumAbsen'
        ));
    }
}
