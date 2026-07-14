<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\FaceData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FaceRegistrationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $faceData = FaceData::where('user_id', $user->id)->first();
        
        return view('anggota.face.register', compact('faceData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'descriptor' => 'required|json',
            'image' => 'required|string', // Base64 image
        ]);

        $user = auth()->user();

        // Decode image base64
        $imageData = $request->input('image');
        $image = str_replace('data:image/jpeg;base64,', '', $imageData);
        $image = str_replace(' ', '+', $image);
        $imageName = 'faces/' . $user->id . '_' . time() . '.jpg';

        // Simpan foto referensi wajah di storage/app/public/faces
        Storage::disk('public')->put($imageName, base64_decode($image));

        // Hapus data wajah lama jika ada
        FaceData::where('user_id', $user->id)->delete();

        // Simpan data wajah baru
        FaceData::create([
            'user_id' => $user->id,
            'descriptor' => json_decode($request->input('descriptor')),
            'reference_photo' => $imageName,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi wajah berhasil disimpan.'
        ]);
    }

    public function destroy()
    {
        $user = auth()->user();
        $faceData = FaceData::where('user_id', $user->id)->first();

        if ($faceData) {
            // Hapus file fisik foto referensi
            Storage::disk('public')->delete($faceData->reference_photo);
            $faceData->delete();
        }

        return redirect()->route('anggota.dashboard')
            ->with('success', 'Data wajah berhasil dihapus.');
    }
}
