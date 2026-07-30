<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\ActivityReport;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityReportController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua anggota beserta ringkasan laporan masing-masing
        $members = User::members()
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($user) {
                $reports = ActivityReport::where('user_id', $user->id)->get();
                $user->total_reports = $reports->count();
                $user->done_count    = $reports->where('status', 'Done')->count();
                $user->in_progress   = $reports->where('status', 'In Progress')->count();
                $user->todo_count    = $reports->where('status', 'To Do')->count();
                $user->near_deadline = $reports->filter(function ($r) {
                    return $r->status !== 'Done'
                        && $r->deadline >= now()
                        && $r->deadline->diffInDays(now()) <= 3;
                })->count();
                $user->overdue_count = $reports->filter(function ($r) {
                    return $r->status !== 'Done' && $r->deadline < now();
                })->count();
                return $user;
            });

        return view('koordinator.reports.index', compact('members'));
    }

    public function show(Request $request, User $user)
    {
        $query = ActivityReport::where('user_id', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->orderBy('tanggal', 'asc')->get();

        return view('koordinator.reports.show', compact('user', 'reports'));
    }
}
