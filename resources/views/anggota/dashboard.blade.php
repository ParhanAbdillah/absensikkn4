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

                    <div class="mb-6">
                        @if(Auth::user()->faceData)
                            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative" role="alert">
                                <span class="block sm:inline font-semibold">Wajah Anda Terdaftar:</span>
                                <span class="block sm:inline">Anda siap melakukan absensi.</span>
                                <a href="{{ route('anggota.face.register') }}" class="ml-4 font-bold underline hover:text-green-800">Lihat Foto Wajah</a>
                            </div>
                        @else
                            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
                                <span class="block sm:inline font-semibold">Perhatian:</span>
                                <span class="block sm:inline">Anda belum mendaftarkan wajah Anda. Silakan daftarkan wajah terlebih dahulu untuk absensi.</span>
                                <a href="{{ route('anggota.face.register') }}" class="ml-4 font-bold underline hover:text-red-800">Daftar Sekarang &rarr;</a>
                            </div>
                        @endif
                    </div>

                    <div class="border-t pt-4">
                        <h3 class="font-semibold text-md mb-4 text-gray-700">Aktivitas Absensi:</h3>
                        <ul class="list-disc pl-5 space-y-2 text-gray-600 text-sm mb-6">
                            <li>Melakukan pendaftaran wajah (Face Registration) untuk sistem absensi.</li>
                            <li>Melakukan absensi masuk (check-in) saat kegiatan dimulai dalam radius 30 meter.</li>
                            <li>Melihat riwayat absensi pribadi.</li>
                        </ul>

                        @if(Auth::user()->faceData)
                            <a href="{{ route('anggota.attendance.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-950 focus:ring ring-green-300 transition ease-in-out duration-150">
                                Mulai Absensi Sekarang
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
