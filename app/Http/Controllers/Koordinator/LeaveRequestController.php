<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $requests = LeaveRequest::with('user')
            ->orderBy('status')
            ->orderBy('date', 'desc')
            ->paginate(20);

        $pendingCount = LeaveRequest::where('status', 'pending')->count();

        return view('koordinator.leave.index', compact('requests', 'pendingCount'));
    }

    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        $leaveRequest->update([
            'status'      => 'approved',
            'notes'       => $request->notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Permohonan ' . $leaveRequest->type_label . ' atas nama ' . $leaveRequest->user->name . ' telah disetujui.');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $request->validate(['notes' => 'required|string|max:500']);

        $leaveRequest->update([
            'status'      => 'rejected',
            'notes'       => $request->notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Permohonan ' . $leaveRequest->type_label . ' atas nama ' . $leaveRequest->user->name . ' telah ditolak.');
    }
}
