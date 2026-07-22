<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\ActivityReport;
use Illuminate\Http\Request;

class ActivityReportController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityReport::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->orderBy('deadline', 'asc')->get();
            
        return view('koordinator.reports.index', compact('reports'));
    }
}
