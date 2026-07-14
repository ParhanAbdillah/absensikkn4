<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard DPL') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-lg font-bold text-blue-600 mb-2">Selamat Datang, {{ Auth::user()->name }}!</p>
                    <p class="text-sm text-gray-600 mb-6">Peran Anda: Dosen Pembimbing Lapangan (DPL)</p>
                    
                    <div class="border-t pt-4">
                        <h3 class="font-semibold text-md mb-4 text-gray-700">Tugas Anda:</h3>
                        <ul class="list-disc pl-5 space-y-2 text-gray-600 text-sm">
                            <li>Memantau kehadiran seluruh anggota kelompok KKN secara real-time.</li>
                            <li>Melakukan approval (persetujuan) absensi manual jika ada kendala sistem/GPS.</li>
                            <li>Mengunduh laporan statistik kehadiran harian dan mingguan.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
