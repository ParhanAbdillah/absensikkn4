<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-800 tracking-tight">
            {{ __('Dashboard Koordinator') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            
            <!-- Quick Cards (Google Drive style "Quick Access" look) -->
            <div class="mb-8">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Akses Cepat & Statistik</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Stat Card 1 -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Anggota Kelompok</div>
                            <div class="mt-1 text-2xl font-extrabold text-slate-800">{{ $totalMembers }} Orang</div>
                        </div>
                        <div class="p-3 bg-yellow-50 text-yellow-600 rounded-2xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                    </div>
                    <!-- Stat Card 2 -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Jadwal Kegiatan</div>
                            <div class="mt-1 text-2xl font-extrabold text-slate-800">{{ $totalSchedules }} Jadwal</div>
                        </div>
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                    <!-- Stat Card 3 -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Titik Lokasi GPS</div>
                            <div class="mt-1 text-2xl font-extrabold text-slate-800">{{ $totalLocations }} Titik</div>
                        </div>
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GDrive Style Layout: File Grid & Lists -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left panel: Jadwal & Action -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-extrabold text-base text-slate-800 tracking-tight">Jadwal Kegiatan Hari Ini</h3>
                        <a href="{{ route('koordinator.schedules.index') }}" class="text-xs font-bold text-emerald-600 hover:underline">Semua Jadwal &rarr;</a>
                    </div>
                    @if($todaySchedules->isEmpty())
                        <div class="text-center py-12 text-slate-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="text-xs font-semibold">Tidak ada kegiatan terjadwal hari ini.</p>
                        </div>
                    @else
                        <div class="divide-y divide-slate-100">
                            @foreach($todaySchedules as $schedule)
                                <div class="py-4 flex justify-between items-center first:pt-0 last:pb-0">
                                    <div>
                                        <h4 class="font-bold text-slate-800 text-sm leading-tight mb-1">{{ $schedule->title }}</h4>
                                        <div class="flex items-center gap-2 text-xs text-slate-400 font-semibold">
                                            <span>Mulai: {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} WIB</span>
                                            <span>•</span>
                                            <span>Lokasi: {{ $schedule->location->name }}</span>
                                        </div>
                                    </div>
                                    <form action="{{ route('koordinator.schedules.send-reminder', $schedule) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm shadow-emerald-100">
                                            Kirim WA
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Right panel: Kehadiran List -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="font-extrabold text-base text-slate-800 tracking-tight mb-6">Kehadiran Anggota (Hari Ini)</h3>
                    @if($todayAttendances->isEmpty())
                        <div class="text-center py-12 text-slate-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <p class="text-xs font-semibold">Belum ada anggota kelompok yang absen masuk hari ini.</p>
                        </div>
                    @else
                        <div class="space-y-3.5 max-h-72 overflow-y-auto pr-1">
                            @foreach($todayAttendances as $attendance)
                                <div class="flex items-center justify-between p-3.5 bg-slate-50 hover:bg-slate-100/70 border border-slate-150 rounded-2xl transition">
                                    <div class="flex items-center gap-3">
                                        @if($attendance->photo_path)
                                            <img src="{{ Storage::url($attendance->photo_path) }}" alt="Foto Absen" class="w-10 h-10 object-cover rounded-full border border-slate-200 shadow-sm">
                                        @endif
                                        <div>
                                            <div class="text-sm font-bold text-slate-800 leading-tight mb-1">{{ $attendance->user->name }}</div>
                                            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Pukul: {{ $attendance->check_in_at->format('H:i') }} WIB | Jarak: {{ round($attendance->distance_meters, 1) }}m</div>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full {{ $attendance->status === 'hadir' ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' }}">
                                        {{ $attendance->status }}
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
