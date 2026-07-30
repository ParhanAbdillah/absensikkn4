<?php

namespace App\Http\Controllers\Dpl;

use App\Http\Controllers\Controller;
use App\Models\ActivityReport;
use App\Models\Attendance;
use App\Models\Location;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $totalMembers    = User::members()->count();
        $totalLocations  = Location::count();

        $hadirCount      = Attendance::whereDate('check_in_at', $today)->count();
        $hadirPersentase = $totalMembers > 0 ? round(($hadirCount / $totalMembers) * 100) : 0;

        // Data 7 hari terakhir untuk grafik
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $weeklyData[] = [
                'label' => $date->isoFormat('ddd'),
                'count' => Attendance::whereDate('check_in_at', $date)->count(),
                'date'  => $date->toDateString(),
            ];
        }

        // Ringkasan laporan kegiatan
        $totalReports    = ActivityReport::count();
        $doneReports     = ActivityReport::where('status', 'Done')->count();
        $pendingReports  = ActivityReport::where('status', '!=', 'Done')->count();

        return view('dpl.dashboard', compact(
            'totalMembers',
            'totalLocations',
            'hadirCount',
            'hadirPersentase',
            'weeklyData',
            'totalReports',
            'doneReports',
            'pendingReports'
        ));
    }

    public function approve(Request $request, Attendance $attendance)
    {
        $attendance->update([
            'status'      => 'hadir',
            'approved_by' => auth()->id(),
            'notes'       => $attendance->notes . ' (Disetujui manual oleh DPL: ' . $request->input('notes') . ')',
        ]);

        return redirect()->back()->with('success', 'Absensi berhasil disetujui secara manual.');
    }
}
