<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Data anggota KKN (termasuk Koordinator)
        $members = [
            ['name' => 'NAUFAL QURROTA A\'YUN', 'nim' => '202406031', 'divisi' => 'Ketua', 'class' => 'MKP', 'role' => 'koordinator'],
            ['name' => 'DINI SRI RAHMA ALIA', 'nim' => '202401031', 'divisi' => 'Wakil Ketua', 'class' => 'AB', 'role' => 'anggota'],
            ['name' => 'SOPHIE', 'nim' => '202401028', 'divisi' => 'Sekertaris I', 'class' => 'AB', 'role' => 'sekretaris'],
            ['name' => 'TYARA DAMAYANTI', 'nim' => '202406039', 'divisi' => 'Sekertaris II', 'class' => 'MKP', 'role' => 'sekretaris'],
            ['name' => 'ATENA RENATIA', 'nim' => '202406013', 'divisi' => 'Bendahara I', 'class' => 'MKP', 'role' => 'anggota'],
            ['name' => 'HALWA AINUN HULIYAH', 'nim' => '202405027', 'divisi' => 'Bendahara II', 'class' => 'MP', 'role' => 'anggota'],
            ['name' => 'RIKI MUHAMAD ROJALI', 'nim' => '202402017', 'divisi' => 'Humas I', 'class' => 'MI', 'role' => 'anggota'],
            ['name' => 'MUHAMAD PARHAN ABDILLAH', 'nim' => '202402047', 'divisi' => 'Humas II', 'class' => 'MI', 'role' => 'anggota'],
            ['name' => 'HEZA LUMAYA', 'nim' => '202406008', 'divisi' => 'Humas III', 'class' => 'MKP', 'role' => 'anggota'],
            ['name' => 'DINDA NOVIYANTI', 'nim' => '202406020', 'divisi' => 'Pendidikan I', 'class' => 'MKP', 'role' => 'anggota'],
            ['name' => 'FACHRUL IRWANDINATA', 'nim' => '202401064', 'divisi' => 'Pendidikan II', 'class' => 'AB', 'role' => 'anggota'],
            ['name' => 'ESA PERMANA', 'nim' => '202402010', 'divisi' => 'Pendidikan III', 'class' => 'MI', 'role' => 'anggota'],
            ['name' => 'RIANTY NUGROHO PUTRI', 'nim' => '202405011', 'divisi' => 'Pendidikan IV', 'class' => 'MP', 'role' => 'anggota'],
            ['name' => 'TIAN DZIQRI FAUZAN', 'nim' => '202405023', 'divisi' => 'PDD I', 'class' => 'MP', 'role' => 'anggota'],
            ['name' => 'AHMAD NANDA PUTRA ARIFIN', 'nim' => '202405005', 'divisi' => 'PDD II', 'class' => 'MP', 'role' => 'anggota'],
            ['name' => 'PADLAN TAOPIKURROHAMAN', 'nim' => '202402028', 'divisi' => 'PDD III', 'class' => 'MI', 'role' => 'anggota'],
            ['name' => 'ALDA NURSYABAN HIDAYAT', 'nim' => '202401030', 'divisi' => 'PDD IV', 'class' => 'AB', 'role' => 'anggota'],
        ];

        // 1. Buat DPL (Dosen Pembimbing Lapangan)
        User::create([
            'name' => 'Dr. Budi Santoso, M.T.',
            'email' => 'dpl@kkn.local', // DPL tetap menggunakan email agar bisa dibedakan, atau bisa kosong jika perlu, tapi biarkan ada defaultnya
            'password' => Hash::make('password'),
            'role' => 'dpl',
            'nim' => '198503112010121003', // NIP Dosen
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        // 2. Buat Koordinator Tambahan (Sesuai Permintaan)
        User::create([
            'name' => 'Koordinator KKN',
            'email' => null,
            'password' => Hash::make('202604'),
            'role' => 'koordinator',
            'nim' => '202604',
            'divisi' => 'Koordinator Tambahan',
            'class' => null,
            'phone' => null,
            'is_active' => true,
        ]);

        // 3. Buat Semua Anggota KKN
        foreach ($members as $member) {
            User::updateOrCreate(
                ['nim' => $member['nim']],
                [
                    'name' => $member['name'],
                    'email' => null,
                    'password' => Hash::make($member['nim']),
                    'role' => $member['role'],
                    'divisi' => $member['divisi'],
                    'class' => $member['class'] ?? null,
                    'is_active' => true,
                ]
            );
        }

        // 4. Buat Lokasi Default jika belum ada
        $location = \App\Models\Location::firstOrCreate(
            ['name' => 'Lokasi Posko KKN'],
            [
                'address' => 'Desa Sirnaraja',
                'latitude' => -7.3274,
                'longitude' => 108.2207,
                'radius_meters' => 100,
                'is_active' => true,
            ]
        );

        // 5. Buat Absensi untuk Tanggal 20, 21, dan 22 Juli 2026
        $dates = ['2026-07-20 08:00:00', '2026-07-21 08:00:00', '2026-07-22 08:00:00'];
        $allUsers = User::whereIn('role', ['koordinator', 'sekretaris', 'anggota'])->get();

        foreach ($dates as $dateTime) {
            foreach ($allUsers as $u) {
                \App\Models\Attendance::firstOrCreate([
                    'user_id' => $u->id,
                    'check_in_at' => $dateTime,
                ], [
                    'location_id' => $location->id,
                    'status' => 'hadir',
                    'notes' => 'Hadir KKN',
                ]);
            }
        }
    }
}
