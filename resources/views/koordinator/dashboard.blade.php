<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-800 tracking-tight">
            Dashboard Koordinator
        </h2>
    </x-slot>

    <div class="py-6" x-data="dashboardCharts({{ $hadirCount }}, {{ $totalMembers }}, {{ json_encode($weeklyData) }})">
        <div class="max-w-7xl mx-auto space-y-8">

            {{-- Welcome Banner --}}
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-6 flex items-center justify-between shadow-lg shadow-emerald-200 relative overflow-hidden animate-card">
                <div class="relative z-10">
                    <p class="text-emerald-100 text-xs font-bold uppercase tracking-wider mb-1">Selamat datang kembali</p>
                    <h2 class="text-white text-2xl font-extrabold tracking-tight">{{ Auth::user()->name }}</h2>
                    <p class="text-emerald-100 text-sm mt-1">{{ Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
                <div class="hidden md:block relative z-10 text-right">
                    <div class="text-emerald-100 text-xs font-bold uppercase tracking-wider mb-1">Kehadiran Hari Ini</div>
                    <div class="text-white text-4xl font-extrabold">{{ $hadirPersentase }}<span class="text-2xl">%</span></div>
                    <div class="text-emerald-100 text-xs font-semibold">{{ $hadirCount }} / {{ $totalMembers }} anggota hadir</div>
                </div>
                {{-- Decorative circles --}}
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full"></div>
                <div class="absolute -right-4 -bottom-10 w-28 h-28 bg-white/10 rounded-full"></div>
            </div>

            {{-- Stat Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                {{-- Card 1 --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm animate-card group cursor-default">
                    <div class="flex items-start justify-between mb-3">
                        <div class="p-2.5 bg-yellow-50 text-yellow-500 rounded-xl group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <span class="text-[9px] font-bold text-yellow-500 bg-yellow-50 px-2 py-0.5 rounded-full uppercase tracking-wider">Anggota</span>
                    </div>
                    <div class="text-2xl font-extrabold text-slate-800 counter" data-target="{{ $totalMembers }}">0</div>
                    <div class="text-xs text-slate-400 font-semibold mt-1">Total Anggota Kelompok</div>
                </div>

                {{-- Card 2 --}}
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

                {{-- Card 3 --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm animate-card group cursor-default">
                    <div class="flex items-start justify-between mb-3">
                        <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <span class="text-[9px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full uppercase tracking-wider">GPS</span>
                    </div>
                    <div class="text-2xl font-extrabold text-slate-800 counter" data-target="{{ $totalLocations }}">0</div>
                    <div class="text-xs text-slate-400 font-semibold mt-1">Titik Lokasi GPS Aktif</div>
                </div>

                {{-- Card 4 --}}
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

                {{-- Bar Chart 7 hari terakhir --}}
                <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm animate-card">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="font-extrabold text-sm text-slate-700 uppercase tracking-wider">Grafik Kehadiran 7 Hari Terakhir</h3>
                        <a href="{{ route('koordinator.attendance.rekap') }}" class="text-xs font-bold text-emerald-600 hover:underline">Lihat Rekap →</a>
                    </div>
                    <div class="h-44">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Today Attendance List --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm animate-card overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-extrabold text-base text-slate-800 tracking-tight">Kehadiran Hari Ini</h3>
                    <a href="{{ route('koordinator.attendance.rekap') }}" class="text-xs font-bold text-emerald-600 hover:underline">Lihat Rekap Lengkap &rarr;</a>
                </div>

                @if($todayAttendances->isEmpty())
                    <div class="text-center py-14 text-slate-300">
                        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <p class="text-sm font-semibold text-slate-400">Belum ada anggota kelompok yang absen masuk hari ini.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                        @foreach($todayAttendances as $attendance)
                            <div class="flex items-center justify-between p-4 bg-slate-50 hover:bg-emerald-50/50 border border-slate-100 hover:border-emerald-200 rounded-2xl transition-all duration-200 animate-card">
                                <div class="flex items-center gap-3">
                                    @if($attendance->photo_path)
                                        <img src="{{ Storage::url($attendance->photo_path) }}" alt="Foto" class="w-10 h-10 object-cover rounded-full border-2 border-emerald-200 shadow-sm">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center font-extrabold text-sm flex-shrink-0">
                                            {{ strtoupper(substr($attendance->user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-bold text-slate-800 leading-tight mb-1">{{ $attendance->user->name }}</div>
                                        <div class="text-[10px] text-slate-400 font-semibold">
                                            Pukul: {{ $attendance->check_in_at->format('H:i') }} WIB &bull;
                                            {{ $attendance->location->name ?? 'GPS Posko' }}
                                        </div>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full {{ $attendance->status === 'hadir' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-yellow-50 text-yellow-700' }}">
                                    {{ $attendance->status }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script>
        function dashboardCharts(hadir, total, weeklyData) {
            return {
                init() {
                    // Counter animation
                    document.querySelectorAll('.counter').forEach(el => {
                        const target = parseInt(el.dataset.target) || 0;
                        const suffix = el.dataset.suffix || '';
                        let count = 0;
                        const step = Math.max(1, Math.ceil(target / 40));
                        const timer = setInterval(() => {
                            count = Math.min(count + step, target);
                            el.textContent = count + suffix;
                            if (count >= target) clearInterval(timer);
                        }, 25);
                    });

                    // Donut Chart
                    const donutCtx = document.getElementById('donutChart').getContext('2d');
                    new Chart(donutCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Hadir', 'Belum Absen'],
                            datasets: [{
                                data: [hadir || 0, Math.max(0, total - hadir)],
                                backgroundColor: ['#10b981', '#e2e8f0'],
                                borderColor: ['#059669', '#cbd5e1'],
                                borderWidth: 2,
                                hoverOffset: 6
                            }]
                        },
                        options: {
                            cutout: '72%',
                            plugins: { legend: { display: false }, tooltip: { callbacks: {
                                label: ctx => ` ${ctx.label}: ${ctx.raw} orang`
                            }}},
                            animation: { animateRotate: true, duration: 1200 }
                        }
                    });

                    // Bar Chart
                    const barCtx = document.getElementById('barChart').getContext('2d');
                    new Chart(barCtx, {
                        type: 'bar',
                        data: {
                            labels: weeklyData.map(d => d.label),
                            datasets: [{
                                label: 'Jumlah Hadir',
                                data: weeklyData.map(d => d.count),
                                backgroundColor: weeklyData.map((d, i) =>
                                    i === weeklyData.length - 1 ? '#059669' : '#a7f3d0'
                                ),
                                borderRadius: 8,
                                borderSkipped: false,
                                hoverBackgroundColor: '#10b981',
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: { font: { size: 11, weight: '600' }, color: '#94a3b8' }
                                },
                                y: {
                                    beginAtZero: true,
                                    max: Math.max(total, 1),
                                    ticks: {
                                        precision: 0,
                                        font: { size: 11, weight: '600' },
                                        color: '#94a3b8'
                                    },
                                    grid: { color: '#f1f5f9' }
                                }
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: { callbacks: {
                                    label: ctx => ` ${ctx.raw} orang hadir`
                                }}
                            },
                            animation: { duration: 900 }
                        }
                    });
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
