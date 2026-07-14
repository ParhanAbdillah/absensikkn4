<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::latest()->get();
        return view('koordinator.locations.index', compact('locations'));
    }

    public function create()
    {
        return view('koordinator.locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:5|max:1000',
        ]);

        Location::create($request->all());

        return redirect()->route('koordinator.locations.index')
            ->with('success', 'Lokasi absensi berhasil ditambahkan.');
    }

    public function edit(Location $location)
    {
        return view('koordinator.locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:5|max:1000',
        ]);

        $location->update($request->all());

        return redirect()->route('koordinator.locations.index')
            ->with('success', 'Lokasi absensi berhasil diperbarui.');
    }

    public function destroy(Location $location)
    {
        $location->delete();

        return redirect()->route('koordinator.locations.index')
            ->with('success', 'Lokasi absensi berhasil dihapus.');
    }
}
