<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-800 tracking-tight">
            Dashboard DPL
        </h2>
    </x-slot>

    <div class="py-6" x-data="dashboardCharts({{ $hadirCount }}, {{ $totalMembers }}, {{ json_encode($weeklyData) }})">
        <div class="max-w-7xl mx-auto space-y-8">

            {{-- Welcome Banner --}}
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-6 flex items-center justify-between shadow-lg shadow-emerald-200 relative overflow-hidden animate-card">
                <div class="relative z-10">
                    <p class="text-emerald-100 text-xs font-bold uppercase tracking-wider mb-1">Selamat datang kembali</p>
                    <h2 class="text-white text-2xl font-extrabold tracking-tight">{{ Auth::user()->name }}</h2>
                    <p class="text-emerald-100 text-sm mt-1">Dosen Pembimbing Lapangan &bull; {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
                <div class="hidden md:block relative z-10 text-right">
                    <div class="text-emerald-100 text-xs font-bold uppercase tracking-wider mb-1">Kehadiran Hari Ini</div>
                    <div class="text-white text-4xl font-extrabold">{{ $hadirPersentase }}<span class="text-2xl">%</span></div>
                    <div class="text-emerald-100 text-xs font-semibold">{{ $hadirCount }} / {{ $totalMembers }} anggota hadir</div>
                </div>
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full"></div>
                <div class="absolute -right-4 -bottom-10 w-28 h-28 bg-white/10 rounded-full"></div>
            </div>

            {{-- Stat Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                {{-- Total Anggota --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm animate-card group cursor-default">
                    <div class="flex items-start justify-between mb-3">
                        <div class="p-2.5 bg-yellow-50 text-yellow-500 rounded-xl group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <span class="text-[9px] font-bold text-yellow-500 bg-yellow-50 px-2 py-0.5 rounded-full uppercase tracking-wider">Mahasiswa</span>
                    </div>
                    <div class="text-2xl font-extrabold text-slate-800 counter" data-target="{{ $totalMembers }}">0</div>
                    <div class="text-xs text-slate-400 font-semibold mt-1">Total Anggota Kelompok</div>
                </div>

                {{-- Hadir Hari Ini --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm animate-card group cursor-default">
                    <div class="flex items-start justify-between mb-3">
                        <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span class="text-[9px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full uppercase tracking-wider">Hari Ini</span>
                    </div>
                    <div class="text-2xl font-extrabold text-slate-800 counter" data-target="{{ $hadirCount }}">0</div>
                    <div class="text-xs text-slate-400 font-semibold mt-1">Hadir Hari Ini</div>
                </div>

                {{-- Total Laporan --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm animate-card group cursor-default">
                    <div class="flex items-start justify-between mb-3">
                        <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <span class="text-[9px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full uppercase tracking-wider">Laporan</span>
                    </div>
                    <div class="text-2xl font-extrabold text-slate-800 counter" data-target="{{ $doneReports }}">0</div>
                    <div class="text-xs text-slate-400 font-semibold mt-1">Laporan Selesai (Done)</div>
                </div>

                {{-- Tingkat Kehadiran --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm animate-card group cursor-default">
                    <div class="flex items-start justify-between mb-3">
                        <div class="p-2.5 bg-violet-50 text-violet-600 rounded-xl group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <span class="text-[9px] font-bold text-violet-600 bg-violet-50 px-2 py-0.5 rounded-full uppercase tracking-wider">Persentase</span>
                    </div>
                    <div class="text-2xl font-extrabold text-slate-800 counter" data-target="{{ $hadirPersentase }}" data-suffix="%">0%</div>
                    <div class="text-xs text-slate-400 font-semibold mt-1">Tingkat Kehadiran Hari Ini</div>
                </div>
            </div>

            {{-- Charts Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Donut Chart --}}
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm animate-card flex flex-col items-center">
                    <h3 class="font-extrabold text-sm text-slate-700 uppercase tracking-wider mb-5 self-start">Komposisi Kehadiran</h3>
                    <div class="relative w-40 h-40">
                        <canvas id="donutChart"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-3xl font-extrabold text-slate-800">{{ $hadirPersentase }}<span class="text-lg">%</span></span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Hadir</span>
                        </div>
                    </div>
                    <div class="mt-5 flex gap-5 text-xs font-semibold">
                        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>Hadir ({{ $hadirCount }})</div>
                        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-slate-200 inline-block"></span>Belum ({{ $totalMembers - $hadirCount }})</div>
                    </div>
                </div>

                {{-- Bar Chart --}}
                <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm animate-card">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="font-extrabold text-sm text-slate-700 uppercase tracking-wider">Grafik Kehadiran 7 Hari Terakhir</h3>
                    </div>
                    <div class="h-44">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <a href="{{ route('koordinator.attendance.rekap') }}"
                    class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm animate-card flex items-center gap-4 hover:border-emerald-300 group">
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <div class="font-bold text-slate-800 group-hover:text-emerald-700 transition-colors">Rekap Absensi</div>
                        <div class="text-xs text-slate-400">Lihat rekap kehadiran harian per tanggal</div>
                    </div>
                    <svg class="w-4 h-4 text-slate-300 ml-auto group-hover:text-emerald-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                <a href="{{ route('koordinator.reports.index') }}"
                    class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm animate-card flex items-center gap-4 hover:border-blue-300 group">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-xl group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <div class="font-bold text-slate-800 group-hover:text-blue-700 transition-colors">Rekap Laporan Kegiatan</div>
                        <div class="text-xs text-slate-400">Pantau laporan kegiatan per anggota</div>
                    </div>
                    <svg class="w-4 h-4 text-slate-300 ml-auto group-hover:text-blue-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
