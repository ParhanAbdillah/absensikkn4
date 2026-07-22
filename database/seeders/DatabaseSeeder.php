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
            ['name' => 'NAUFAL QURROTA A\'YUN', 'nim' => '202406031', 'divisi' => 'Ketua', 'role' => 'koordinator'],
            ['name' => 'DINI SRI RAHMA ALIA', 'nim' => '202401031', 'divisi' => 'Wakil Ketua', 'role' => 'anggota'],
            ['name' => 'SOPHIE', 'nim' => '202401028', 'divisi' => 'Sekertaris I', 'role' => 'sekretaris'],
            ['name' => 'TYARA DAMAYANTI', 'nim' => '202406039', 'divisi' => 'Sekertaris II', 'role' => 'sekretaris'],
            ['name' => 'ATENA RENATIA', 'nim' => '202406013', 'divisi' => 'Bendahara I', 'role' => 'anggota'],
            ['name' => 'HALWA AINUN HULIYAH', 'nim' => '202405027', 'divisi' => 'Bendahara II', 'role' => 'anggota'],
            ['name' => 'RIKI MUHAMAD ROJALI', 'nim' => '202402017', 'divisi' => 'Humas I', 'role' => 'anggota'],
            ['name' => 'MUHAMAD PARHAN ABDILLAH', 'nim' => '202402042', 'divisi' => 'Humas II', 'role' => 'anggota'],
            ['name' => 'HEZA LUMAYA', 'nim' => '202406008', 'divisi' => 'Humas III', 'role' => 'anggota'],
            ['name' => 'DINDA NOVIYANTI', 'nim' => '202406020', 'divisi' => 'Pendidikan I', 'role' => 'anggota'],
            ['name' => 'FACHRUL IRWANDINATA', 'nim' => '202401064', 'divisi' => 'Pendidikan II', 'role' => 'anggota'],
            ['name' => 'ESA PERMANA', 'nim' => '202402010', 'divisi' => 'Pendidikan III', 'role' => 'anggota'],
            ['name' => 'RIANTY NUGROHO PUTRI', 'nim' => '202405011', 'divisi' => 'Pendidikan IV', 'role' => 'anggota'],
            ['name' => 'TIAN DZIQRI FAUZAN', 'nim' => '202405023', 'divisi' => 'PDD I', 'role' => 'anggota'],
            ['name' => 'AHMAD NANDA PUTRA ARIFIN', 'nim' => '202405005', 'divisi' => 'PDD II', 'role' => 'anggota'],
            ['name' => 'PADLAN TAOPIKURROHAMAN', 'nim' => '202402028', 'divisi' => 'PDD III', 'role' => 'anggota'],
            ['name' => 'ALDA NURSYABAN HIDAYAT', 'nim' => '202401030', 'divisi' => 'PDD IV', 'role' => 'anggota'],
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
            'phone' => null,
            'is_active' => true,
        ]);

        // 3. Buat Semua Anggota KKN
        foreach ($members as $member) {
            User::create([
                'name' => $member['name'],
                'email' => null, // Email kosong
                'password' => Hash::make($member['nim']), // Password pakai nim
                'role' => $member['role'],
                'nim' => $member['nim'],
                'divisi' => $member['divisi'],
                'phone' => null,
                'is_active' => true,
            ]);
        }
    }
}
