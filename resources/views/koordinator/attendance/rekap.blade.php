<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center w-full gap-3">
            <h2 class="font-extrabold text-xl text-slate-800 tracking-tight">Rekap Absensi Harian</h2>
            <div class="flex items-center gap-3">
                {{-- Date Filter --}}
                <form method="GET" action="{{ route('koordinator.attendance.rekap') }}" class="flex items-center gap-2">
                    <input type="date"
                           name="date"
                           value="{{ $selectedDate->toDateString() }}"
                           max="{{ now()->toDateString() }}"
                           class="border border-slate-200 rounded-xl px-3 py-2 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none"
                           onchange="this.form.submit()">
                </form>
                {{-- Print Button --}}
                <a href="{{ route('koordinator.attendance.rekap.print', ['date' => $selectedDate->toDateString()]) }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-widest rounded-xl transition shadow-md shadow-emerald-200 animate-button">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print PDF
                </a>
                {{-- Word Docx Button with Dropdown --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-widest rounded-xl transition shadow-md shadow-blue-200 animate-button">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Ekspor Word
                    </button>
                    
                    <div x-show="open" @click.away="open = false" style="display: none;"
                         class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-slate-100 z-50 p-4">
                        <h4 class="text-sm font-bold text-slate-800 mb-3">Ekspor Rentang Tanggal</h4>
                        <form method="GET" action="{{ route('koordinator.attendance.rekap.export-word') }}">
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Dari Tanggal</label>
                                    <input type="date" name="start_date" value="{{ $selectedDate->toDateString() }}" max="{{ now()->toDateString() }}" class="w-full border border-slate-200 rounded-lg px-3 py-1.5 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Sampai Tanggal</label>
                                    <input type="date" name="end_date" value="{{ $selectedDate->toDateString() }}" max="{{ now()->toDateString() }}" class="w-full border border-slate-200 rounded-lg px-3 py-1.5 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                                </div>
                                <button type="submit" class="w-full mt-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-2 rounded-lg transition">
                                    Download Docx
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="rekapChart({{ $hadirCount }}, {{ $tidakHadirCount }}, {{ $totalMembers }})">
        <div class="max-w-7xl mx-auto space-y-6 mb-8">

            {{-- Selected date label --}}
            <div class="flex items-center gap-2 text-sm text-slate-500 font-semibold">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Tanggal: <span class="text-slate-800 font-bold">{{ $selectedDate->isoFormat('dddd, D MMMM Y') }}</span>
            </div>

            {{-- Stat Cards Row --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                {{-- Total Anggota --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm animate-card flex items-center gap-4">
                    <div class="p-3 bg-slate-100 text-slate-600 rounded-2xl flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Anggota</div>
                        <div class="text-2xl font-extrabold text-slate-800 counter" data-target="{{ $totalMembers }}">0</div>
                    </div>
                </div>
                {{-- Hadir --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm animate-card flex items-center gap-4">
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Hadir</div>
                        <div class="text-2xl font-extrabold text-emerald-600 counter" data-target="{{ $hadirCount }}">0</div>
                    </div>
                </div>
                {{-- Tidak Hadir --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm animate-card flex items-center gap-4">
                    <div class="p-3 bg-red-50 text-red-500 rounded-2xl flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tidak Hadir</div>
                        <div class="text-2xl font-extrabold text-red-500 counter" data-target="{{ $tidakHadirCount }}">0</div>
                    </div>
                </div>
                {{-- Persentase --}}
                <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 p-5 rounded-2xl shadow-md shadow-emerald-200 animate-card flex items-center gap-4">
                    <div class="p-3 bg-white/20 text-white rounded-2xl flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-emerald-100 uppercase tracking-wider">Persentase Hadir</div>
                        <div class="text-2xl font-extrabold text-white counter" data-target="{{ $persentase }}" data-suffix="%">0%</div>
                    </div>
                </div>
            </div>

            {{-- Charts + Attendance Table Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Donut Chart --}}
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm animate-card flex flex-col items-center justify-center">
                    <h3 class="font-extrabold text-sm text-slate-700 uppercase tracking-wider mb-5">Kehadiran Hari Ini</h3>
                    <div class="relative w-44 h-44">
                        <canvas id="donutChart"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-3xl font-extrabold text-slate-800">{{ $persentase }}%</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Hadir</span>
                        </div>
                    </div>
                    <div class="mt-5 flex gap-5 text-xs font-semibold">
                        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span> Hadir ({{ $hadirCount }})</div>
                        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-red-400 inline-block"></span> Tidak ({{ $tidakHadirCount }})</div>
                    </div>
                </div>

                {{-- Attendance Table --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm animate-card overflow-hidden">
                    <div class="p-5 border-b border-slate-100">
                        <h3 class="font-extrabold text-sm text-slate-700 uppercase tracking-wider">Daftar Hadir – {{ $selectedDate->format('d/m/Y') }}</h3>
                    </div>
                    @if($attendances->isEmpty())
                        <div class="flex flex-col items-center justify-center py-16 text-slate-300">
                            <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            <p class="text-sm font-semibold text-slate-400">Belum ada absensi pada tanggal ini.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                                        <th class="px-5 py-3 text-left">No</th>
                                        <th class="px-5 py-3 text-left">Foto</th>
                                        <th class="px-5 py-3 text-left">Nama Anggota</th>
                                        <th class="px-5 py-3 text-left">NIM</th>
                                        <th class="px-5 py-3 text-left">Jam Masuk</th>
                                        <th class="px-5 py-3 text-left">Lokasi</th>
                                        <th class="px-5 py-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 text-sm text-slate-700">
                                    @foreach($attendances as $i => $a)
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="px-5 py-3 text-slate-400 font-bold">{{ $i + 1 }}</td>
                                            <td class="px-5 py-3">
                                                @if($a->photo_path)
                                                    <img src="{{ Storage::url($a->photo_path) }}" 
                                                         alt="Foto Absen" 
                                                         @click="previewUrl = '{{ Storage::url($a->photo_path) }}'; showPreview = true"
                                                         class="w-10 h-10 rounded-xl object-cover border border-slate-200 shadow-sm cursor-zoom-in hover:scale-105 active:scale-95 transition duration-150">
                                                @else
                                                    <span class="text-xs text-slate-400 font-semibold">-</span>
                                                @endif
                                            </td>
                                            <td class="px-5 py-3 font-bold text-slate-800">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-extrabold text-xs flex-shrink-0">
                                                        {{ strtoupper(substr($a->user->name, 0, 1)) }}
                                                    </div>
                                                    {{ $a->user->name }}
                                                </div>
                                            </td>
                                            <td class="px-5 py-3 font-mono text-xs text-slate-500">{{ $a->user->nim ?? '-' }}</td>
                                            <td class="px-5 py-3 font-semibold">{{ $a->check_in_at->format('H:i') }} WIB</td>
                                            <td class="px-5 py-3 text-slate-500 text-xs">{{ $a->location->name ?? 'GPS Posko' }}</td>
                                            <td class="px-5 py-3 text-center">
                                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full
                                                    {{ $a->status === 'hadir' ? 'bg-emerald-50 text-emerald-700' : 'bg-yellow-50 text-yellow-700' }}">
                                                    {{ $a->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Belum Absen Section --}}
            @if($belumAbsen->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm animate-card overflow-hidden">
                <div class="p-5 border-b border-red-50 bg-red-50/30">
                    <h3 class="font-extrabold text-sm text-red-600 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Anggota Belum Absen ({{ $belumAbsen->count() }} orang)
                    </h3>
                </div>
                <div class="p-5 flex flex-wrap gap-3">
                    @foreach($belumAbsen as $u)
                        <div class="flex items-center gap-2 px-3 py-2 bg-red-50 rounded-xl border border-red-100">
                            <div class="w-7 h-7 rounded-full bg-red-400 text-white flex items-center justify-center font-extrabold text-xs flex-shrink-0">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">{{ $u->name }}</div>
                                <div class="text-[9px] text-slate-400 font-semibold">{{ $u->nim ?? 'NIM -' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Image Preview Modal --}}
            <div x-show="showPreview" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-transition x-cloak>
                <div class="bg-white rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-100" @click.away="showPreview = false">
                    <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                        <h3 class="font-extrabold text-sm text-slate-700 uppercase tracking-wider">Foto Bukti Absensi</h3>
                        <button type="button" @click="showPreview = false" class="text-slate-400 hover:text-slate-600 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="p-6 flex justify-center items-center bg-black">
                        <img :src="previewUrl" class="max-h-[70vh] max-w-full object-contain rounded-lg">
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script>
        function rekapChart(hadir, tidakHadir, total) {
            return {
                showPreview: false,
                previewUrl: '',
                init() {
                    // Counter animation
                    document.querySelectorAll('.counter').forEach(el => {
                        const target = parseInt(el.dataset.target);
                        const suffix = el.dataset.suffix || '';
                        let count = 0;
                        const step = Math.max(1, Math.ceil(target / 30));
                        const timer = setInterval(() => {
                            count = Math.min(count + step, target);
                            el.textContent = count + suffix;
                            if (count >= target) clearInterval(timer);
                        }, 30);
                    });

                    // Donut Chart
                    const ctx = document.getElementById('donutChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Hadir', 'Tidak Hadir'],
                            datasets: [{
                                data: [hadir || 0, Math.max(0, total - hadir)],
                                backgroundColor: ['#10b981', '#fca5a5'],
                                borderColor: ['#059669', '#ef4444'],
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
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
