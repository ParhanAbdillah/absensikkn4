<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-800 tracking-tight">
            Dashboard DPL
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-8">

            {{-- Welcome Banner --}}
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-6 flex items-center justify-between shadow-lg shadow-emerald-200 relative overflow-hidden">
                <div class="relative z-10 flex items-center gap-4">
                    @if (Auth::user()->avatar && file_exists(public_path(Auth::user()->avatar)))
                        <img src="{{ asset(Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-14 h-14 sm:w-16 sm:h-16 rounded-full object-cover border-2 border-white/40 shadow-md flex-shrink-0">
                    @else
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-white/20 text-white flex items-center justify-center font-extrabold text-xl sm:text-2xl border-2 border-white/40 shadow-md flex-shrink-0">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <p class="text-emerald-100 text-xs font-bold uppercase tracking-wider mb-1">Selamat datang kembali</p>
                        <h2 class="text-white text-2xl font-extrabold tracking-tight">{{ Auth::user()->name }}</h2>
                        <p class="text-emerald-100 text-sm mt-1">Dosen Pembimbing Lapangan &bull; {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</p>
                    </div>
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
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm group cursor-default hover:-translate-y-1 transition-transform">
                    <div class="flex items-start justify-between mb-3">
                        <div class="p-2.5 bg-yellow-50 text-yellow-500 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <span class="text-[9px] font-bold text-yellow-500 bg-yellow-50 px-2 py-0.5 rounded-full uppercase tracking-wider">Anggota</span>
                    </div>
                    <div class="text-3xl font-extrabold text-slate-800">{{ $totalMembers }}</div>
                    <div class="text-xs text-slate-400 font-semibold mt-1">Total Anggota Kelompok</div>
                </div>

                {{-- Hadir Hari Ini --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm group cursor-default hover:-translate-y-1 transition-transform">
                    <div class="flex items-start justify-between mb-3">
                        <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-[9px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full uppercase tracking-wider">Hari Ini</span>
                    </div>
                    <div class="text-3xl font-extrabold text-slate-800">{{ $hadirPersentase }}<span class="text-lg font-bold text-slate-500">%</span></div>
                    <div class="text-xs text-slate-400 font-semibold mt-1">Kehadiran Hari Ini ({{ $hadirCount }}/{{ $totalMembers }})</div>
                </div>

                {{-- Kegiatan Selesai --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm group cursor-default hover:-translate-y-1 transition-transform">
                    <div class="flex items-start justify-between mb-3">
                        <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <span class="text-[9px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full uppercase tracking-wider">Selesai</span>
                    </div>
                    <div class="text-3xl font-extrabold text-slate-800">{{ $doneReports }}</div>
                    <div class="text-xs text-slate-400 font-semibold mt-1">Kegiatan Selesai (Done)</div>
                </div>

                {{-- Mendekati Deadline --}}
                <div class="bg-white p-5 rounded-2xl border {{ ($nearDeadlineReports + $overdueReports) > 0 ? 'border-red-200 bg-red-50/30' : 'border-slate-200' }} shadow-sm group cursor-default hover:-translate-y-1 transition-transform">
                    <div class="flex items-start justify-between mb-3">
                        <div class="p-2.5 {{ ($nearDeadlineReports + $overdueReports) > 0 ? 'bg-red-50 text-red-500' : 'bg-slate-50 text-slate-400' }} rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-[9px] font-bold {{ ($nearDeadlineReports + $overdueReports) > 0 ? 'text-red-500 bg-red-50' : 'text-slate-400 bg-slate-50' }} px-2 py-0.5 rounded-full uppercase tracking-wider">Deadline</span>
                    </div>
                    <div class="text-3xl font-extrabold {{ ($nearDeadlineReports + $overdueReports) > 0 ? 'text-red-600' : 'text-slate-800' }}">
                        {{ $nearDeadlineReports + $overdueReports }}
                    </div>
                    <div class="text-xs text-slate-400 font-semibold mt-1">
                        Kegiatan Perlu Perhatian
                        @if($overdueReports > 0)
                            <span class="text-red-500">({{ $overdueReports }} lewat)</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Warning Banner jika ada yang mendekati/lewat deadline --}}
            @if(($nearDeadlineReports + $overdueReports) > 0)
            <div class="bg-red-50 border border-red-200 rounded-2xl p-4 flex items-start gap-3">
                <div class="p-2 bg-red-100 text-red-600 rounded-xl flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-red-700 text-sm">Perhatian!</p>
                    <p class="text-xs text-red-600 mt-0.5">
                        Terdapat <strong>{{ $nearDeadlineReports }}</strong> kegiatan mendekati deadline (&le;3 hari)
                        @if($overdueReports > 0)
                            dan <strong>{{ $overdueReports }}</strong> kegiatan yang sudah melewati deadline.
                        @endif
                        <a href="{{ route('koordinator.reports.index') }}" class="underline font-bold ml-1">Lihat Rekap Laporan →</a>
                    </p>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
