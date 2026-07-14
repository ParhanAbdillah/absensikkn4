<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with('location')->latest()->get();
        return view('koordinator.schedules.index', compact('schedules'));
    }

    public function create()
    {
        $locations = Location::where('is_active', true)->get();
        return view('koordinator.schedules.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location_id' => 'required|exists:locations,id',
            'activity_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'tolerance_time' => 'nullable|date_format:H:i',
        ]);

        Schedule::create($request->all());

        return redirect()->route('koordinator.schedules.index')
            ->with('success', 'Jadwal kegiatan berhasil ditambahkan.');
    }

    public function edit(Schedule $schedule)
    {
        $locations = Location::where('is_active', true)->get();
        return view('koordinator.schedules.edit', compact('schedule', 'locations'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location_id' => 'required|exists:locations,id',
            'activity_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'tolerance_time' => 'nullable|date_format:H:i',
        ]);

        $schedule->update($request->all());

        return redirect()->route('koordinator.schedules.index')
            ->with('success', 'Jadwal kegiatan berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('koordinator.schedules.index')
            ->with('success', 'Jadwal kegiatan berhasil dihapus.');
    }
}
