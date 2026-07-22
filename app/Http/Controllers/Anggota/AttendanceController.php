<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\Location;
use App\Models\FaceData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Pastikan user sudah registrasi wajah
        if (!$user->faceData) {
            return redirect()->route('anggota.face.register')
                ->with('error', 'Silakan daftarkan wajah Anda terlebih dahulu sebelum melakukan absensi.');
        }

        // Ambil lokasi gps yang aktif untuk dicocokkan
        $locations = Location::where('is_active', true)->get();

        return view('anggota.attendance.index', compact('locations'));
    }

    public function checkLocation(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $location = Location::find($request->location_id);

        $distance = $this->calculateHaversine(
            $request->latitude,
            $request->longitude,
            $location->latitude,
            $location->longitude
        );

        $isValid = $distance <= $location->radius_meters;

        return response()->json([
            'success' => true,
            'is_valid' => $isValid,
            'distance' => round($distance, 2),
            'radius' => $location->radius_meters,
            'location_name' => $location->name
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'descriptor' => 'required|json', // Client-side face descriptor
            'image' => 'required|string', // Base64 capture saat absen
            'is_manual' => 'nullable|boolean',
        ]);

        $user = auth()->user();
        $today = Carbon::today();

        // 1. Cek apakah sudah absen hari ini
        $existing = Attendance::where('user_id', $user->id)
            ->whereDate('check_in_at', $today)
            ->first();

        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan absensi hari ini.']);
        }

        $location = Location::find($request->location_id);

        // 2. Hitung jarak GPS (Server-side) jika ada koordinat
        $distance = null;
        $notes = null;

        if ($request->filled('latitude') && $request->filled('longitude')) {
            $distance = $this->calculateHaversine(
                $request->latitude,
                $request->longitude,
                $location->latitude,
                $location->longitude
            );

            if ($distance > $location->radius_meters) {
                $notes = 'Absen di luar radius lokasi (' . round($distance, 2) . ' meter)';
            }
        } else {
            $notes = 'Absen tanpa GPS (dilewati)';
        }

        // 3. Validasi Face Matching (Euclidean Distance)
        $inputDescriptor = json_decode($request->descriptor);
        $storedFace = FaceData::where('user_id', $user->id)->first();
 
        if (!$storedFace) {
            return response()->json(['success' => false, 'message' => 'Data wajah referensi tidak ditemukan.']);
        }
 
        $isManual = $request->boolean('is_manual', false);
        $matchScore = 0.0;
 
        if (!$isManual) {
            $storedDescriptor = $storedFace->descriptor;
            $matchScore = $this->euclideanDistance($inputDescriptor, $storedDescriptor);
 
            // Threshold kecocokan wajah (biasanya <= 0.6)
            if ($matchScore > 0.6) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verifikasi wajah gagal. Wajah tidak cocok dengan referensi.'
                ]);
            }
        }
 
        if ($isManual) {
            $notes = $notes ? $notes . ' (Bypass verifikasi wajah)' : 'Bypass verifikasi wajah';
        }

        // 4. Decode dan simpan foto saat absen
        $imageData = $request->input('image');
        $image = str_replace('data:image/jpeg;base64,', '', $imageData);
        $image = str_replace(' ', '+', $image);
        $imagePath = 'attendances/' . $user->id . '_' . time() . '.jpg';
        Storage::disk('public')->put($imagePath, base64_decode($image));

        // 5. Simpan Record Kehadiran
        $now = Carbon::now();
        Attendance::create([
            'user_id' => $user->id,
            'location_id' => $location->id,
            'check_in_at' => $now,
            'check_in_lat' => $request->latitude,
            'check_in_lng' => $request->longitude,
            'face_match_score' => $matchScore,
            'photo_path' => $imagePath,
            'distance_meters' => $distance,
            'status' => 'hadir',
            'notes' => $notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat! Status: Hadir'
        ]);
    }

    public function history()
    {
        $user = auth()->user();
        $attendances = Attendance::with(['location', 'approvedBy'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('anggota.attendance.history', compact('attendances'));
    }

    private function calculateHaversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Meter
    }

    private function euclideanDistance($a, $b)
    {
        $sum = 0;
        for ($i = 0; $i < count($a); $i++) {
            $sum += pow($a[$i] - $b[$i], 2);
        }
        return sqrt($sum);
    }
}
