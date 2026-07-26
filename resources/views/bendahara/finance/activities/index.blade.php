<x-app-layout>
<div class="space-y-6" x-data="{ openAddModal: false }">
    <!-- Top Header -->
    <div class="bg-white rounded-2xl border border-slate-150 shadow-sm overflow-hidden">
        <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100">
            <div>
                <h1 class="text-xl font-bold text-slate-800 tracking-tight flex items-center gap-2">
                    <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </span>
                    Laporan Rencana & Realisasi Kegiatan KKN
                </h1>
                <p class="text-xs text-slate-500 mt-1">Kelola perincian anggaran pemasukan dan pengeluaran tiap program kerja / seminar KKN</p>
            </div>
            <div class="flex items-center gap-2.5 self-start sm:self-center flex-shrink-0">
                <button @click="openAddModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl transition shadow-sm animate-button flex-shrink-0 whitespace-nowrap">
                    <span class="text-sm font-bold">+</span>
                    Buat Laporan Baru
                </button>
            </div>
        </div>
        <!-- Tabs Navigation -->
        <div class="flex bg-slate-50/50 px-4 border-t border-slate-50">
            <a href="{{ route('finance.index') }}" class="px-4 py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-semibold text-xs transition flex items-center gap-1.5">
                <span>💰</span> Buku Kas Keuangan
            </a>
            <a href="#" class="px-4 py-3 border-b-2 border-emerald-600 text-emerald-600 font-bold text-xs flex items-center gap-1.5">
                <span>📋</span> Laporan Kegiatan KKN
            </a>
        </div>
    </div>

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

    <!-- Reports List Table -->
    <div class="bg-white rounded-2xl border border-slate-150 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Kegiatan Program Kerja</th>
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Laporan</th>
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kota Tanda Tangan</th>
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Dibuat Oleh</th>
                        <th class="px-5 py-3.5 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($reports as $r)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-4 text-xs font-bold text-slate-800">
                                {{ $r->activity_name }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-500 font-medium">
                                {{ $r->date->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-500">
                                {{ $r->city }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-500">
                                {{ $r->user->name ?? 'Mahasiswa' }}
                                <span class="block text-[9px] text-slate-400">{{ $r->user->divisi ?? '' }}</span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-center text-xs flex items-center justify-center gap-2">
                                <a href="{{ route('finance.activities.show', $r->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 font-bold rounded-lg text-[10px] transition">
                                    📂 Buka Rincian
                                </a>
                                @if(Auth::id() === $r->user_id || Auth::user()->isKoordinator())
                                    <form action="{{ route('finance.activities.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Hapus laporan kegiatan ini beserta rinciannya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 p-1.5 bg-rose-50 rounded-lg hover:bg-rose-100 transition border border-rose-100">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-xs text-slate-400 font-medium">
                                <span class="block text-3xl mb-2">📋</span>
                                Belum ada laporan keuangan kegiatan yang dibuat. Klik tombol di atas untuk membuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reports->hasPages())
            <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-100">
                {{ $reports->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: Buat Laporan Baru -->
    <div x-show="openAddModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" @keydown.escape.window="openAddModal = false">
        <div class="bg-white rounded-2xl max-w-md w-full overflow-hidden shadow-2xl border border-slate-150" @click.away="openAddModal = false" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="px-6 py-4 bg-slate-55 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800">Buat Laporan Kegiatan Baru</h3>
                <button @click="openAddModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('finance.activities.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nama Program Kerja / Seminar</label>
                    <input type="text" name="activity_name" required placeholder="Contoh: Seminar Digital Marketing & Pemasaran UMKM" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Alokasi Dana dari Kas Utama (Rupiah)</label>
                    <div class="flex rounded-xl border border-slate-200 overflow-hidden focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 h-[38px]">
                        <span class="inline-flex items-center px-3.5 bg-slate-50 border-r border-slate-200 text-xs font-bold text-slate-500 flex-shrink-0">Rp</span>
                        <input type="number" name="cash_allocation" placeholder="0" class="w-full border-0 px-3 py-2 text-xs text-slate-700 font-bold focus:ring-0 focus:outline-none bg-white">
                    </div>
                    <p class="text-[9px] text-slate-400 mt-1">Isi jika kegiatan mengambil dana dari Kas Utama, sistem akan otomatis mencatat pengeluaran di Buku Kas</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tanggal Kegiatan</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full h-[38px] rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Kota Penandatanganan</label>
                        <input type="text" name="city" required value="Tasikmalaya" placeholder="Kota" class="w-full h-[38px] rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 font-semibold text-[10px] transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold text-[10px] transition shadow-sm animate-button">
                        Buat Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
