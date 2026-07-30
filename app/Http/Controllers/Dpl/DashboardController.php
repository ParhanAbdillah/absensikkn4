<?php

namespace App\Http\Controllers\Dpl;

use App\Http\Controllers\Controller;
use App\Models\ActivityReport;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $totalMembers    = User::members()->count();
        $hadirCount      = Attendance::whereDate('check_in_at', $today)->count();
        $hadirPersentase = $totalMembers > 0 ? round(($hadirCount / $totalMembers) * 100) : 0;

        $doneReports     = ActivityReport::where('status', 'Done')->count();

        // Kegiatan mendekati deadline (belum selesai, deadline <= 3 hari ke depan)
        $nearDeadlineReports = ActivityReport::where('status', '!=', 'Done')
            ->whereDate('deadline', '>=', now()->toDateString())
            ->whereDate('deadline', '<=', now()->addDays(3)->toDateString())
            ->count();

        // Kegiatan sudah lewat deadline
        $overdueReports = ActivityReport::where('status', '!=', 'Done')
            ->whereDate('deadline', '<', now()->toDateString())
            ->count();

        return view('dpl.dashboard', compact(
            'totalMembers',
            'hadirCount',
            'hadirPersentase',
            'doneReports',
            'nearDeadlineReports',
            'overdueReports'
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
