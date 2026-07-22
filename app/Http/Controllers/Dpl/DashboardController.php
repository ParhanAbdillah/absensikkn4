<?php

namespace App\Http\Controllers\Dpl;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $totalMembers = User::members()->count();
        
        $attendances = Attendance::with(['user', 'schedule'])
            ->latest()
            ->paginate(10);
            
        return view('dpl.dashboard', compact('totalMembers', 'attendances'));
    }

    public function approve(Request $request, Attendance $attendance)
    {
        $attendance->update([
            'status' => 'hadir',
            'approved_by' => auth()->id(),
            'notes' => $attendance->notes . ' (Disetujui manual oleh DPL: ' . $request->input('notes') . ')'
        ]);

        return redirect()->back()->with('success', 'Absensi berhasil disetujui secara manual.');
    }
}
