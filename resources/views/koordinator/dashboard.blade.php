<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Koordinator') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Stat Cards -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Anggota Kelompok</div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalMembers }} Orang</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Jadwal Kegiatan</div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalSchedules }} Jadwal</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Titik Lokasi GPS</div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalLocations }} Titik</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Jadwal Hari Ini & Kirim Pengingat -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-bold text-lg text-gray-800 mb-4">Jadwal Kegiatan Hari Ini</h3>
                    @if($todaySchedules->isEmpty())
                        <p class="text-sm text-gray-500">Tidak ada kegiatan hari ini.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($todaySchedules as $schedule)
                                <div class="p-4 border rounded-lg flex justify-between items-center">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $schedule->title }}</h4>
                                        <p class="text-xs text-gray-500">Mulai: {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} WIB</p>
                                        <p class="text-xs text-gray-500">Lokasi: {{ $schedule->location->name }}</p>
                                    </div>
                                    <form action="{{ route('koordinator.schedules.send-reminder', $schedule) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-semibold uppercase tracking-wider">
                                            Kirim WA Reminder
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Absen Hari Ini -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-bold text-lg text-gray-800 mb-4">Kehadiran Anggota (Hari Ini)</h3>
                    @if($todayAttendances->isEmpty())
                        <p class="text-sm text-gray-500">Belum ada anggota yang absen hari ini.</p>
                    @else
                        <div class="space-y-3 max-h-80 overflow-y-auto">
                            @foreach($todayAttendances as $attendance)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded border">
                                    <div class="flex items-center">
                                        @if($attendance->photo_path)
                                            <img src="{{ Storage::url($attendance->photo_path) }}" alt="Absen" class="w-10 h-10 object-cover rounded-full mr-3 border">
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $attendance->user->name }}</div>
                                            <div class="text-xs text-gray-500">Pukul: {{ $attendance->check_in_at->format('H:i') }} WIB | Jarak: {{ round($attendance->distance_meters, 1) }}m</div>
                                        </div>
                                    </div>
                                    <span class="text-xs px-2 py-1 font-semibold rounded-full {{ $attendance->status === 'hadir' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
