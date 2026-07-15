<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-800 tracking-tight">
            Dashboard Anggota
        </h2>
    </x-slot>

    <div class="py-6" x-data="anggotaDashboard({{ json_encode($weeklyData) }})">
        <div class="max-w-7xl mx-auto space-y-8">

            {{-- Welcome Banner --}}
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-6 flex items-center justify-between shadow-lg shadow-emerald-200 relative overflow-hidden animate-card">
                <div class="relative z-10">
                    <p class="text-emerald-100 text-xs font-bold uppercase tracking-wider mb-1">Selamat datang kembali</p>
                    <h2 class="text-white text-2xl font-extrabold tracking-tight">{{ Auth::user()->name }}</h2>
                    <p class="text-emerald-100 text-sm mt-1">{{ Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
                <div class="hidden md:block relative z-10 text-right">
                    @if($todayAttendance)
                        <div class="text-emerald-100 text-xs font-bold uppercase tracking-wider mb-1">Status Hari Ini</div>
                        <div class="text-white text-lg font-extrabold">✅ Sudah Absen</div>
                        <div class="text-emerald-100 text-xs font-semibold">Pukul {{ $todayAttendance->check_in_at->format('H:i') }} WIB</div>
                    @else
                        <div class="text-emerald-100 text-xs font-bold uppercase tracking-wider mb-1">Status Hari Ini</div>
                        <div class="text-white text-lg font-extrabold">⏳ Belum Absen</div>
                        <div class="text-emerald-100 text-xs font-semibold">Segera lakukan absensi</div>
                    @endif
                </div>
                {{-- Decorative circles --}}
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full"></div>
                <div class="absolute -right-4 -bottom-10 w-28 h-28 bg-white/10 rounded-full"></div>
            </div>

            {{-- Face Registration Alert --}}
            @if(!Auth::user()->faceData)
            <div class="bg-red-50 border border-red-200 rounded-2xl p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 md:gap-6">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-red-100 rounded-xl text-red-500 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-red-700">Wajah Belum Terdaftar!</p>
                        <p class="text-xs text-red-500 mt-0.5">Anda perlu mendaftarkan wajah terlebih dahulu sebelum dapat melakukan absensi.</p>
                    </div>
                </div>
                <a href="{{ route('anggota.face.register') }}" class="w-full md:w-auto inline-flex justify-center items-center px-4 py-3 md:py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase tracking-widest rounded-xl transition shadow animate-button flex-shrink-0">
                    Daftar Wajah →
                </a>
            </div>
            @else
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 md:gap-6">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-emerald-100 rounded-xl text-emerald-600 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-emerald-700">Wajah Sudah Terdaftar ✓</p>
                        <p class="text-xs text-emerald-500 mt-0.5">Anda siap melakukan absensi menggunakan sistem pengenalan wajah.</p>
                    </div>
                </div>
                @if(!$todayAttendance)
                <a href="{{ route('anggota.attendance.index') }}" class="w-full md:w-auto inline-flex justify-center items-center px-4 py-3 md:py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-widest rounded-xl transition shadow-md shadow-emerald-200 animate-button flex-shrink-0">
                    Mulai Absensi →
                </a>
                @else
                <span class="w-full md:w-auto inline-flex justify-center items-center px-4 py-3 md:py-2.5 bg-emerald-100 text-emerald-700 font-bold text-xs uppercase tracking-widest rounded-xl border border-emerald-200 flex-shrink-0">
                    ✓ Sudah Absen Hari Ini
                </span>
                @endif
            </div>
            @endif

            {{-- Stat Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-5">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm animate-card group cursor-default">
                    <div class="flex items-start justify-between mb-3">
                        <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span class="text-[9px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full uppercase tracking-wider">30 Hari</span>
                    </div>
                    <div class="text-2xl font-extrabold text-slate-800 counter" data-target="{{ $totalHadir }}">0</div>
                    <div class="text-xs text-slate-400 font-semibold mt-1">Total Kehadiran (30 Hari)</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm animate-card group cursor-default">
                    <div class="flex items-start justify-between mb-3">
                        <div class="p-2.5 bg-violet-50 text-violet-600 rounded-xl group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <span class="text-[9px] font-bold text-violet-600 bg-violet-50 px-2 py-0.5 rounded-full uppercase tracking-wider">Persentase</span>
                    </div>
                    <div class="text-2xl font-extrabold text-slate-800 counter" data-target="{{ $persentase }}" data-suffix="%">0%</div>
                    <div class="text-xs text-slate-400 font-semibold mt-1">Tingkat Kehadiran Bulan Ini</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm animate-card group cursor-default col-span-2 lg:col-span-1">
                    <div class="flex items-start justify-between mb-3">
                        <div class="p-2.5 {{ $todayAttendance ? 'bg-emerald-50 text-emerald-600' : 'bg-yellow-50 text-yellow-500' }} rounded-xl group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <span class="text-[9px] font-bold {{ $todayAttendance ? 'text-emerald-600 bg-emerald-50' : 'text-yellow-500 bg-yellow-50' }} px-2 py-0.5 rounded-full uppercase tracking-wider">Hari Ini</span>
                    </div>
                    <div class="text-2xl font-extrabold {{ $todayAttendance ? 'text-emerald-600' : 'text-yellow-500' }}">
                        {{ $todayAttendance ? 'Hadir' : 'Belum' }}
                    </div>
                    <div class="text-xs text-slate-400 font-semibold mt-1">
                        {{ $todayAttendance ? 'Pukul ' . $todayAttendance->check_in_at->format('H:i') . ' WIB' : 'Belum absen hari ini' }}
                    </div>
                </div>
            </div>

            {{-- Chart 7 hari --}}
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm animate-card">
                <h3 class="font-extrabold text-sm text-slate-700 uppercase tracking-wider mb-5">Kehadiran 7 Hari Terakhir</h3>
                <div class="h-40">
                    <canvas id="barChart"></canvas>
                </div>
            </div>

            {{-- Riwayat Terbaru --}}
            @if($history->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm animate-card overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-extrabold text-sm text-slate-700 uppercase tracking-wider">Riwayat Absensi Terakhir</h3>
                    <a href="{{ route('anggota.attendance.history') }}" class="text-xs font-bold text-emerald-600 hover:underline">Lihat Semua →</a>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach($history as $h)
                    <div class="flex items-center justify-between px-5 py-3.5 hover:bg-slate-50 transition">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-emerald-50 rounded-xl text-emerald-600 flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-800">{{ $h->check_in_at->isoFormat('dddd, D MMMM Y') }}</div>
                                <div class="text-xs text-slate-400 font-semibold">
                                    Pukul {{ $h->check_in_at->format('H:i') }} WIB &bull; {{ $h->location->name ?? 'GPS Posko' }}
                                </div>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                            {{ $h->status }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script>
        function anggotaDashboard(weeklyData) {
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

                    // Bar Chart
                    const barCtx = document.getElementById('barChart').getContext('2d');
                    new Chart(barCtx, {
                        type: 'bar',
                        data: {
                            labels: weeklyData.map(d => d.label),
                            datasets: [{
                                label: 'Kehadiran',
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
                                    max: 1,
                                    ticks: {
                                        precision: 0,
                                        stepSize: 1,
                                        font: { size: 11, weight: '600' },
                                        color: '#94a3b8',
                                        callback: val => val === 1 ? 'Hadir' : val === 0 ? 'Absen' : ''
                                    },
                                    grid: { color: '#f1f5f9' }
                                }
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: { callbacks: {
                                    label: ctx => ctx.raw === 1 ? ' Hadir' : ' Tidak Hadir'
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
