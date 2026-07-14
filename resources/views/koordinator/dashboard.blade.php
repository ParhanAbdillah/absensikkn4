<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Koordinator') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-lg font-bold text-yellow-600 mb-2">Selamat Datang, {{ Auth::user()->name }}!</p>
                    <p class="text-sm text-gray-600 mb-6">Peran Anda: Koordinator (Ketua Kelompok)</p>

                    <div class="border-t pt-4">
                        <h3 class="font-semibold text-md mb-4 text-gray-700">Tugas Anda:</h3>
                        <ul class="list-disc pl-5 space-y-2 text-gray-600 text-sm">
                            <li>Mengatur titik lokasi koordinat absensi (Posko/Balai Desa).</li>
                            <li>Membuat jadwal kegiatan harian untuk kelompok.</li>
                            <li>Melihat daftar absensi anggota kelompok.</li>
                            <li>Mengirim notifikasi pengingat absensi lewat WhatsApp / Email bagi yang belum absen.</li>
                            <li>Melakukan absensi masuk (check-in) harian.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
