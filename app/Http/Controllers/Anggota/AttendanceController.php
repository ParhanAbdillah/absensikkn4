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

        // Ambil jadwal hari ini yang aktif
        $schedules = Schedule::with('location')
            ->whereDate('activity_date', Carbon::today())
            ->where('is_active', true)
            ->get();

        return view('anggota.attendance.index', compact('schedules'));
    }

    public function checkLocation(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $schedule = Schedule::with('location')->find($request->schedule_id);
        $location = $schedule->location;

        $distance = $this->calculateHaversine(
            $request->latitude,
            $request->longitude,
            $location->latitude,
            $location->longitude
        );

        // Jika jarak dalam radius (misal 30m dari lokasi jadwal)
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
            'schedule_id' => 'required|exists:schedules,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'descriptor' => 'required|json', // Client-side face descriptor
            'image' => 'required|string', // Base64 capture saat absen
        ]);

        $user = auth()->user();
        $today = Carbon::today();

        // 1. Cek apakah sudah absen hari ini untuk jadwal tersebut
        $existing = Attendance::where('user_id', $user->id)
            ->where('schedule_id', $request->schedule_id)
            ->first();

        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan absensi untuk kegiatan ini.']);
        }

        $schedule = Schedule::with('location')->find($request->schedule_id);
        $location = $schedule->location;

        // 2. Validasi ulang GPS (Server-side)
        $distance = $this->calculateHaversine(
            $request->latitude,
            $request->longitude,
            $location->latitude,
            $location->longitude
        );

        if ($distance > $location->radius_meters) {
            return response()->json([
                'success' => false,
                'message' => 'Absensi ditolak. Anda berada di luar radius lokasi absensi (' . round($distance, 2) . ' meter).'
            ]);
        }

        // 3. Validasi Face Matching (Euclidean Distance)
        $inputDescriptor = json_decode($request->descriptor);
        $storedFace = FaceData::where('user_id', $user->id)->first();

        if (!$storedFace) {
            return response()->json(['success' => false, 'message' => 'Data wajah referensi tidak ditemukan.']);
        }

        $storedDescriptor = $storedFace->descriptor;
        $matchScore = $this->euclideanDistance($inputDescriptor, $storedDescriptor);

        // Threshold kecocokan wajah (biasanya <= 0.6)
        if ($matchScore > 0.6) {
            return response()->json([
                'success' => false,
                'message' => 'Verifikasi wajah gagal. Wajah tidak cocok dengan referensi.'
            ]);
        }

        // 4. Decode dan simpan foto saat absen
        $imageData = $request->input('image');
        $image = str_replace('data:image/jpeg;base64,', '', $imageData);
        $image = str_replace(' ', '+', $image);
        $imagePath = 'attendances/' . $user->id . '_' . time() . '.jpg';
        Storage::disk('public')->put($imagePath, base64_decode($image));

        // 5. Cek Status Kehadiran (Hadir vs Terlambat)
        $status = 'hadir';
        $now = Carbon::now();
        $startTime = Carbon::createFromFormat('H:i:s', $schedule->start_time);
        
        if ($schedule->tolerance_time) {
            $toleranceTime = Carbon::createFromFormat('H:i:s', $schedule->tolerance_time);
            if ($now->format('H:i:s') > $toleranceTime->format('H:i:s')) {
                $status = 'terlambat';
            }
        } elseif ($now->format('H:i:s') > $startTime->format('H:i:s')) {
            $status = 'terlambat';
        }

        // 6. Simpan Record Kehadiran
        Attendance::create([
            'user_id' => $user->id,
            'schedule_id' => $request->schedule_id,
            'location_id' => $location->id,
            'check_in_at' => $now,
            'check_in_lat' => $request->latitude,
            'check_in_lng' => $request->longitude,
            'face_match_score' => $matchScore,
            'photo_path' => $imagePath,
            'distance_meters' => $distance,
            'status' => $status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat! Status: ' . ucfirst($status)
        ]);
    }

    public function history()
    {
        $user = auth()->user();
        $attendances = Attendance::with(['schedule.location', 'approvedBy'])
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
