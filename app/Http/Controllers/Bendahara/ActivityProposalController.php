<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\ActivityProposal;
use App\Models\FinanceTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ActivityProposalController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityProposal::with('user')->orderBy('date', 'desc')->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $proposals = $query->paginate(15)->withQueryString();

        // Calculate stats
        $totalRequested = ActivityProposal::sum('budget_requested');
        $totalApproved = ActivityProposal::whereIn('status', ['approved', 'completed'])->sum('budget_approved');
        $pendingCount = ActivityProposal::where('status', 'pending')->count();

        return view('bendahara.finance.proposals', compact('proposals', 'totalRequested', 'totalApproved', 'pendingCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_name' => 'required|string|max:255',
            'date' => 'required|date',
            'budget_requested' => 'required|numeric|min:0.01',
            'description' => 'required|string',
            'proposal_file' => 'nullable|file|mimes:pdf,docx,doc,zip,jpg,png|max:4096',
        ]);

        $filePath = null;
        if ($request->hasFile('proposal_file')) {
            $file = $request->file('proposal_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/proposals'), $filename);
            $filePath = 'uploads/proposals/' . $filename;
        }

        ActivityProposal::create([
            'activity_name' => $validated['activity_name'],
            'date' => $validated['date'],
            'budget_requested' => $validated['budget_requested'],
            'description' => $validated['description'],
            'file_path' => $filePath,
            'status' => 'pending',
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('finance.proposals.index')->with('success', 'Pengajuan anggaran kegiatan berhasil dikirim!');
    }

    public function approve(Request $request, ActivityProposal $proposal)
    {
        $user = Auth::user();
        if (!$user->isKoordinator() && !$user->isDpl()) {
            abort(403, 'Hanya DPL atau Koordinator yang dapat menyetujui anggaran.');
        }

        $validated = $request->validate([
            'budget_approved' => 'required|numeric|min:0|max:' . $proposal->budget_requested,
        ]);

        $proposal->update([
            'budget_approved' => $validated['budget_approved'],
            'status' => 'approved',
        ]);

        return redirect()->route('finance.proposals.index')->with('success', 'Pengajuan anggaran berhasil disetujui!');
    }

    public function reject(ActivityProposal $proposal)
    {
        $user = Auth::user();
        if (!$user->isKoordinator() && !$user->isDpl()) {
            abort(403, 'Hanya DPL atau Koordinator yang dapat menolak anggaran.');
        }

        $proposal->update([
            'status' => 'rejected',
        ]);

        return redirect()->route('finance.proposals.index')->with('success', 'Pengajuan anggaran telah ditolak.');
    }

    public function postToCash(Request $request, ActivityProposal $proposal)
    {
        $user = Auth::user();
        if (!$user->isBendahara() && !$user->isKoordinator()) {
            abort(403, 'Hanya Bendahara atau Koordinator yang dapat mencatat ke Buku Kas.');
        }

        if ($proposal->status !== 'approved') {
            return redirect()->back()->with('error', 'Hanya pengajuan dengan status "Approved" yang dapat dicatat ke Buku Kas.');
        }

        if ($proposal->is_posted_to_cash) {
            return redirect()->back()->with('error', 'Anggaran ini sudah pernah dicatat ke Buku Kas.');
        }

        $validated = $request->validate([
            'category' => 'required|string',
        ]);

        // Create transaction in General Cash Book
        FinanceTransaction::create([
            'date' => now(),
            'type' => 'expense',
            'category' => $validated['category'],
            'amount' => $proposal->budget_approved,
            'description' => 'Realisasi Anggaran: ' . $proposal->activity_name . ' (Diajukan oleh: ' . $proposal->user->name . ')',
            'receipt_path' => $proposal->file_path, // proposal file as initial attachment
            'created_by' => $user->id,
        ]);

        $proposal->update([
            'is_posted_to_cash' => true,
            'status' => 'completed',
        ]);

        return redirect()->route('finance.proposals.index')->with('success', 'Realisasi anggaran berhasil dicatat ke Buku Kas Utama!');
    }

    public function destroy(ActivityProposal $proposal)
    {
        if (Auth::id() !== $proposal->user_id && !Auth::user()->isKoordinator()) {
            abort(403, 'Anda tidak berhak menghapus pengajuan ini.');
        }

        if ($proposal->file_path && file_exists(public_path($proposal->file_path))) {
            unlink(public_path($proposal->file_path));
        }

        $proposal->delete();

        return redirect()->route('finance.proposals.index')->with('success', 'Pengajuan anggaran berhasil dihapus.');
    }
}
