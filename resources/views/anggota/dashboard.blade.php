<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Anggota') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-lg font-bold text-green-600 mb-2">Selamat Datang, {{ Auth::user()->name }}!</p>
                    <p class="text-sm text-gray-600 mb-6">Peran Anda: Anggota Kelompok KKN</p>

                    <div class="border-t pt-4">
                        <h3 class="font-semibold text-md mb-4 text-gray-700">Aktivitas Absensi:</h3>
                        <ul class="list-disc pl-5 space-y-2 text-gray-600 text-sm">
                            <li>Melakukan pendaftaran wajah (Face Registration) untuk sistem absensi.</li>
                            <li>Melakukan absensi masuk (check-in) saat kegiatan dimulai dalam radius 30 meter.</li>
                            <li>Melihat riwayat riwayat absensi pribadi.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
