<x-app-layout>
<div class="space-y-6" x-data="{ openAddModal: false, selectedReceipt: null }">
    <!-- Floating Toast Notification System (Disappears after 3 seconds) -->
    <div class="fixed top-5 right-5 z-[9999] flex flex-col gap-3"
         x-data="{ showSuccess: {{ session('success') ? 'true' : 'false' }}, showError: {{ session('error') ? 'true' : 'false' }} }"
         x-init="
            if(showSuccess) { setTimeout(() => showSuccess = false, 3000); }
            if(showError) { setTimeout(() => showError = false, 3000); }
         ">
        @if(session('success'))
        <div x-show="showSuccess" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100" x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="translate-x-full opacity-0" class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-lg max-w-sm">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-xs font-bold">{{ session('success') }}</span>
            <button type="button" @click="showSuccess = false" class="text-emerald-400 hover:text-emerald-600 font-bold ml-auto text-xs">✕</button>
        </div>
        @endif
        @if(session('error'))
        <div x-show="showError" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100" x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="translate-x-full opacity-0" class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-lg max-w-sm">
            <svg class="w-5 h-5 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-xs font-bold">{{ session('error') }}</span>
            <button type="button" @click="showError = false" class="text-rose-400 hover:text-rose-600 font-bold ml-auto text-xs">✕</button>
        </div>
        @endif
    </div>
    <!-- Top Header -->
    <div class="bg-white rounded-2xl border border-slate-150 shadow-sm overflow-hidden">
        <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100">
            <div>
                <h1 class="text-xl font-bold text-slate-800 tracking-tight flex items-center gap-2">
                    <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                    Buku Kas & Keuangan KKN
                </h1>
                <p class="text-xs text-slate-500 mt-1">Transparansi pemasukan & pengeluaran kas KKN Kelompok 4 Desa Sirnaraja</p>
            </div>
            <div class="flex items-center gap-2.5 self-start sm:self-center flex-shrink-0">
                <a href="{{ route('finance.export', request()->all()) }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-xl transition shadow-sm bg-white flex-shrink-0 whitespace-nowrap">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>
                        @if(request()->filled('week'))
                            Ekspor Laporan Minggu {{ request('week') + 1 }}
                        @else
                            Ekspor Kas Umum
                        @endif
                    </span>
                </a>
                @if (Auth::user()->isBendahara() || Auth::user()->isKoordinator())
                    <button @click="openAddModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl transition shadow-sm animate-button flex-shrink-0 whitespace-nowrap">
                        <span class="text-sm font-bold">+</span>
                        Catat Transaksi
                    </button>
                @endif
            </div>
        </div>
        <!-- Tabs Navigation -->
        <div class="flex bg-slate-50/50 px-4 border-t border-slate-50">
            <a href="#" class="px-4 py-3 border-b-2 border-emerald-600 text-emerald-600 font-bold text-xs flex items-center gap-1.5">
                <span>💰</span> Buku Kas Keuangan
            </a>
            <a href="{{ route('finance.activities.index') }}" class="px-4 py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-semibold text-xs transition flex items-center gap-1.5">
                <span>📋</span> Laporan Kegiatan KKN
            </a>
        </div>
    </div>

    <!-- Summary Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Card Saldo -->
        <div style="background: linear-gradient(135deg, #0f172a, #1e293b); border: 1px solid #334155;" class="rounded-2xl p-5 text-white shadow-md relative overflow-hidden animate-card">
            <div class="z-10 relative">
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Sisa Saldo Kas</p>
                <h3 class="text-2xl font-extrabold mt-1.5 text-emerald-400">Rp {{ number_format($balance, 2, ',', '.') }}</h3>
                <div class="flex items-center gap-1.5 mt-2.5">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-slate-300 text-[10px] font-medium">Sistem Keuangan Aktif</span>
                </div>
            </div>
            <div class="absolute right-4 top-1/2 -translate-y-1/2 p-3 bg-white/5 rounded-xl z-0 text-slate-500">
                <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>
        </div>

        <!-- Card Pemasukan -->
        <div style="background: linear-gradient(135deg, #ffffff, #f0fdf4); border: 1px solid #bbf7d0;" class="rounded-2xl p-5 shadow-sm flex items-center justify-between relative overflow-hidden animate-card">
            <div>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Total Pemasukan</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1.5">Rp {{ number_format($totalIncome, 2, ',', '.') }}</h3>
                <p class="text-emerald-600 text-[10px] font-bold mt-2.5 inline-flex items-center gap-1 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                    </svg>
                    Uang Masuk
                </p>
            </div>
            <div class="p-3 bg-emerald-100/50 rounded-xl text-emerald-600 border border-emerald-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
            </div>
        </div>

        <!-- Card Pengeluaran -->
        <div style="background: linear-gradient(135deg, #ffffff, #fff5f5); border: 1px solid #fecdd3;" class="rounded-2xl p-5 shadow-sm flex items-center justify-between relative overflow-hidden animate-card">
            <div>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Total Pengeluaran</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1.5">Rp {{ number_format($totalExpense, 2, ',', '.') }}</h3>
                <p class="text-rose-600 text-[10px] font-bold mt-2.5 inline-flex items-center gap-1 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-100">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                    Uang Keluar
                </p>
            </div>
            <div class="p-3 bg-rose-100/50 rounded-xl text-rose-600 border border-rose-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Charts Section (Only shown if data exists to avoid weird empty state charts) -->
    @if(!$transactions->isEmpty())
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Line Chart (Trends) -->
            <div class="bg-white rounded-2xl p-5 border border-slate-150 shadow-sm lg:col-span-2">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Tren Arus Kas Keuangan</h4>
                <div class="h-60 relative">
                    <canvas id="cashflowTrendChart"></canvas>
                </div>
            </div>

            <!-- Pie Chart (Expenses by Category) -->
            <div class="bg-white rounded-2xl p-5 border border-slate-150 shadow-sm flex flex-col justify-between">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Proporsi Pengeluaran</h4>
                <div class="h-48 relative flex items-center justify-center">
                    @if($expenseByCategory->isEmpty())
                        <div class="text-center p-6 text-slate-400">
                            <span class="block text-2xl mb-1">📊</span>
                            <p class="text-xs font-semibold">Belum ada pengeluaran dicatat</p>
                        </div>
                    @else
                        <canvas id="expenseByCategoryChart"></canvas>
                    @endif
                </div>
                <div class="text-[10px] text-slate-400 text-center mt-3">Persentase pengeluaran berdasarkan kategori utama</div>
            </div>
        </div>
    @endif

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-150 shadow-sm">
        <form action="{{ route('finance.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 items-end">
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Pilih Minggu KKN</label>
                <select name="week" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-white">
                    <option value="">-- Semua Minggu --</option>
                    @foreach($weeks as $index => $w)
                        <option value="{{ $index }}" {{ request('week') == $index ? 'selected' : '' }}>{{ $w['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Jenis</label>
                <select name="type" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-white">
                    <option value="">Semua Jenis</option>
                    <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Pemasukan (Uang Masuk)</option>
                    <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Pengeluaran (Uang Keluar)</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Kategori</label>
                <select name="category" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-white">
                    <option value="">Semua Kategori</option>
                    <option value="Kas Anggota" {{ request('category') === 'Kas Anggota' ? 'selected' : '' }}>Kas Anggota</option>
                    <option value="Donasi / Sponsor" {{ request('category') === 'Donasi / Sponsor' ? 'selected' : '' }}>Donasi / Sponsor</option>
                    <option value="Konsumsi" {{ request('category') === 'Konsumsi' ? 'selected' : '' }}>Konsumsi</option>
                    <option value="Peralatan / Perlengkapan" {{ request('category') === 'Peralatan / Perlengkapan' ? 'selected' : '' }}>Peralatan / Perlengkapan</option>
                    <option value="PDD / Dokumentasi" {{ request('category') === 'PDD / Dokumentasi' ? 'selected' : '' }}>PDD / Dokumentasi</option>
                    <option value="Transportasi" {{ request('category') === 'Transportasi' ? 'selected' : '' }}>Transportasi</option>
                    <option value="Print / Jilid / Fotokopi" {{ request('category') === 'Print / Jilid / Fotokopi' ? 'selected' : '' }}>Print / Jilid / Fotokopi</option>
                    <option value="Lain-lain" {{ request('category') === 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full h-[38px] rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
                <div class="flex items-center gap-2">
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full h-[38px] rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <button type="submit" class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition shadow-sm h-[38px] w-[38px] flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    @if(request()->anyFilled(['type', 'category', 'start_date', 'end_date', 'week']))
                        <a href="{{ route('finance.index') }}" class="inline-flex items-center justify-center p-2 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-xl transition h-[38px] w-[38px] flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Ledger Table -->
    <div class="bg-white rounded-2xl border border-slate-150 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-55">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jenis</th>
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kategori</th>
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Keterangan</th>
                        <th class="px-5 py-3.5 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nominal</th>
                        <th class="px-5 py-3.5 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">Bukti</th>
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pencatat</th>
                        @if (Auth::user()->isBendahara() || Auth::user()->isKoordinator())
                            <th class="px-5 py-3.5 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($transactions as $t)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-600 font-medium">
                                {{ $t->date->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-xs">
                                @if($t->type === 'income')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                        Pemasukan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-100">
                                        <span class="w-1 h-1 rounded-full bg-rose-500"></span>
                                        Pengeluaran
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-700 font-semibold">
                                {{ $t->category }}
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-600 max-w-xs truncate" title="{{ $t->description }}">
                                {{ $t->description }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-right font-bold {{ $t->type === 'income' ? 'text-emerald-600' : 'text-slate-800' }}">
                                Rp {{ number_format($t->amount, 2, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-center text-xs">
                                @if($t->receipt_path)
                                    <button @click="selectedReceipt = '{{ asset($t->receipt_path) }}'" class="inline-flex items-center gap-1 text-[10px] text-indigo-600 hover:text-indigo-800 font-semibold bg-indigo-50 px-2 py-1 rounded-lg border border-indigo-100 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Lihat
                                    </button>
                                @else
                                    <span class="text-[10px] text-slate-400 italic">Tidak ada</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-500 font-medium">
                                {{ $t->creator->name ?? 'Sistem' }}
                            </td>
                            @if (Auth::user()->isBendahara() || Auth::user()->isKoordinator())
                                <td class="px-5 py-4 whitespace-nowrap text-center text-xs">
                                    <form action="{{ route('finance.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 p-1 bg-rose-50 hover:bg-rose-100 rounded-lg transition border border-rose-100">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-xs text-slate-400 font-medium">
                                <span class="block text-3xl mb-2">📁</span>
                                Belum ada catatan transaksi keuangan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        @if($transactions->hasPages())
            <div class="px-5 py-3.5 bg-slate-55 border-t border-slate-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Receipt Image Viewer -->
    <div x-show="selectedReceipt !== null" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" @keydown.escape.window="selectedReceipt = null">
        <div class="bg-white rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl relative border border-slate-100" @click.away="selectedReceipt = null">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-800">Bukti Transaksi</h3>
                <button @click="selectedReceipt = null" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 bg-slate-50 flex items-center justify-center min-h-[300px]">
                <img :src="selectedReceipt" class="max-h-[450px] max-w-full rounded-lg object-contain shadow-md" alt="Bukti Transaksi">
            </div>
        </div>
    </div>

    <!-- Modal Catat Transaksi Baru (Alpine.js Controlled) -->
    <div x-show="openAddModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" @keydown.escape.window="openAddModal = false">
        <div class="bg-white rounded-2xl max-w-md w-full overflow-hidden shadow-2xl border border-slate-100" @click.away="openAddModal = false" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800">Catat Transaksi Baru</h3>
                <button @click="openAddModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('finance.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tanggal</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Jenis</label>
                        <select name="type" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-white">
                            <option value="income">Pemasukan (Masuk)</option>
                            <option value="expense">Pengeluaran (Keluar)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Kategori</label>
                    <select name="category" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-white">
                        <option value="Kas Anggota">Kas Anggota</option>
                        <option value="Donasi / Sponsor">Donasi / Sponsor</option>
                        <option value="Konsumsi">Konsumsi</option>
                        <option value="Peralatan / Perlengkapan">Peralatan / Perlengkapan</option>
                        <option value="PDD / Dokumentasi">PDD / Dokumentasi</option>
                        <option value="Transportasi">Transportasi</option>
                        <option value="Print / Jilid / Fotokopi">Print / Jilid / Fotokopi</option>
                        <option value="Lain-lain">Lain-lain</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nominal (Rupiah)</label>
                    <div class="flex rounded-xl border border-slate-200 overflow-hidden focus-within:border-emerald-500 focus-within:ring-1 focus-within:ring-emerald-500 h-[38px]">
                        <span class="inline-flex items-center px-3.5 bg-slate-50 border-r border-slate-200 text-xs font-bold text-slate-500 flex-shrink-0">Rp</span>
                        <input type="number" step="0.01" min="0.01" name="amount" required placeholder="0.00" class="w-full border-0 px-3 py-2 text-xs text-slate-700 font-bold focus:ring-0 focus:outline-none bg-white">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Keterangan Transaksi</label>
                    <textarea name="description" required rows="3" placeholder="Masukkan detail peruntukan atau sumber dana kas..." class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"></textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Unggah Bukti Nota / Kwitansi (Gambar)</label>
                    <input type="file" name="receipt" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 file:cursor-pointer">
                    <p class="text-[9px] text-slate-400 mt-1">Maksimal 2MB, format JPG/PNG/GIF</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 font-semibold text-[10px] transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold text-[10px] transition shadow-sm animate-button">
                        Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Chart.js scripts integration -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const trendData = {!! json_encode($monthlyTrends) !!};
        if (trendData.length > 0) {
            const months = [...new Set(trendData.map(item => item.month))].sort();
            
            const incomeMonthly = months.map(m => {
                const record = trendData.find(item => item.month === m && item.type === 'income');
                return record ? parseFloat(record.total) : 0;
            });

            const expenseMonthly = months.map(m => {
                const record = trendData.find(item => item.month === m && item.type === 'expense');
                return record ? parseFloat(record.total) : 0;
            });

            const ctxTrend = document.getElementById('cashflowTrendChart').getContext('2d');
            new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: months.map(m => {
                        const date = new Date(m + '-01');
                        return date.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
                    }),
                    datasets: [
                        {
                            label: 'Pemasukan',
                            data: incomeMonthly,
                            borderColor: '#10B981', // Emerald 500
                            backgroundColor: '#10B98115',
                            borderWidth: 2.5,
                            tension: 0.35,
                            fill: true
                        },
                        {
                            label: 'Pengeluaran',
                            data: expenseMonthly,
                            borderColor: '#F43F5E', // Rose 500
                            backgroundColor: '#F43F5E15',
                            borderWidth: 2.5,
                            tension: 0.35,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: { family: 'Inter', weight: 'bold', size: 10 }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        }

        const expenseCategories = {!! json_encode($expenseByCategory) !!};
        if (expenseCategories.length > 0) {
            const ctxExpense = document.getElementById('expenseByCategoryChart').getContext('2d');
            new Chart(ctxExpense, {
                type: 'doughnut',
                data: {
                    labels: expenseCategories.map(item => item.category),
                    datasets: [{
                        data: expenseCategories.map(item => parseFloat(item.total)),
                        backgroundColor: [
                            '#F43F5E', // Rose
                            '#F59E0B', // Amber
                            '#3B82F6', // Blue
                            '#8B5CF6', // Purple
                            '#EC4899', // Pink
                            '#06B6D4', // Cyan
                            '#10B981', // Emerald
                            '#64748B'  // Slate
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 10,
                                padding: 10,
                                font: { family: 'Inter', size: 9, weight: 'semibold' }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
</x-app-layout>
