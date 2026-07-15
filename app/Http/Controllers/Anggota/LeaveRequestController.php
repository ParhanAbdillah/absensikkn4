<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $requests = LeaveRequest::where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('anggota.leave.index', compact('requests'));
    }

    public function create()
    {
        return view('anggota.leave.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'   => 'required|in:izin,sakit',
            'date'   => 'required|date',
            'reason' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Cek duplikat pada tanggal yang sama
        $existing = LeaveRequest::where('user_id', Auth::id())
            ->whereDate('date', $validated['date'])
            ->first();

        if ($existing) {
            return back()->withErrors(['date' => 'Anda sudah mengajukan ' . $existing->type_label . ' untuk tanggal ini.'])->withInput();
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave-attachments', 'public');
        }

        LeaveRequest::create([
            'user_id'    => Auth::id(),
            'type'       => $validated['type'],
            'date'       => $validated['date'],
            'reason'     => $validated['reason'],
            'attachment' => $attachmentPath,
        ]);

        return redirect()->route('anggota.leave.index')
            ->with('success', 'Permohonan ' . ($validated['type'] === 'izin' ? 'izin' : 'sakit') . ' berhasil diajukan dan menunggu persetujuan koordinator.');
    }
}
