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
        // 1. Buat DPL (Dosen Pembimbing Lapangan)
        User::create([
            'name' => 'Dr. Budi Santoso, M.T.',
            'email' => 'dpl@kkn.local',
            'password' => Hash::make('password'),
            'role' => 'dpl',
            'nim' => '198503112010121003', // NIP Dosen
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        // 2. Buat Koordinator (Ketua Kelompok KKN)
        User::create([
            'name' => 'Faisal Rahman',
            'email' => 'koordinator@kkn.local',
            'password' => Hash::make('password'),
            'role' => 'koordinator',
            'nim' => '2201010001',
            'phone' => '082345678901',
            'is_active' => true,
        ]);

        // 3. Buat 16 Anggota Kelompok Lainnya (Total Kelompok = 17 Orang termasuk Ketua)
        $members = [
            ['name' => 'Andi Wijaya', 'nim' => '2201010002'],
            ['name' => 'Bambang Triyono', 'nim' => '2201010003'],
            ['name' => 'Citra Lestari', 'nim' => '2201010004'],
            ['name' => 'Dewi Anggraeni', 'nim' => '2201010005'],
            ['name' => 'Eko Prasetyo', 'nim' => '2201010006'],
            ['name' => 'Fitri Handayani', 'nim' => '2201010007'],
            ['name' => 'Gilang Ramadhan', 'nim' => '2201010008'],
            ['name' => 'Hendra Kurniawan', 'nim' => '2201010009'],
            ['name' => 'Indah Permata', 'nim' => '2201010010'],
            ['name' => 'Joko Susilo', 'nim' => '2201010011'],
            ['name' => 'Kartika Sari', 'nim' => '2201010012'],
            ['name' => 'Lukman Hakim', 'nim' => '2201010013'],
            ['name' => 'Mega Utami', 'nim' => '2201010014'],
            ['name' => 'Novianti', 'nim' => '2201010015'],
            ['name' => 'Oki Setiawan', 'nim' => '2201010016'],
            ['name' => 'Putri Wulandari', 'nim' => '2201010017'],
        ];

        foreach ($members as $index => $member) {
            User::create([
                'name' => $member['name'],
                'email' => "anggota" . ($index + 1) . "@kkn.local",
                'password' => Hash::make('password'),
                'role' => 'anggota',
                'nim' => $member['nim'],
                'phone' => '0857123456' . sprintf('%02d', $index),
                'is_active' => true,
            ]);
        }
    }
}
