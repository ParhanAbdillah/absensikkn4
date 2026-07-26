<x-app-layout>
<div class="space-y-6" x-data="{ openItemModal: false, activeType: 'income' }">
    <!-- Top Action Header -->
    <div class="bg-white rounded-2xl border border-slate-150 p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-sm">
        <div>
            <a href="{{ route('finance.activities.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-700 transition mb-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Daftar Laporan
            </a>
            <h1 class="text-lg font-bold text-slate-800 tracking-tight">
                {{ $report->activity_name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Rincian rencana pemasukan dan rencana pengeluaran dana</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5 self-start sm:self-center flex-shrink-0">
            <a href="{{ route('finance.activities.export', $report->id) }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-xl transition shadow-sm bg-white flex-shrink-0 whitespace-nowrap">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Ekspor Excel Berformat
            </a>
            @if(Auth::id() === $report->user_id)
                <button @click="activeType = 'income'; openItemModal = true" style="background-color: #059669;" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 hover:bg-emerald-750 text-white font-semibold text-xs rounded-xl transition shadow-sm whitespace-nowrap flex-shrink-0">
                    + Pemasukan
                </button>
                <button @click="activeType = 'expense'; openItemModal = true" style="background-color: #dc2626;" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 hover:bg-rose-750 text-white font-semibold text-xs rounded-xl transition shadow-sm whitespace-nowrap flex-shrink-0">
                    + Pengeluaran
                </button>
            @endif
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

    <!-- Main Sheets (Designed in Spreadsheet Style) -->
    <div class="bg-white rounded-2xl border border-slate-150 p-6 shadow-sm space-y-8">
        
        <!-- Header Kegiatan -->
        <div class="text-center pb-4 border-b border-slate-100">
            <h2 class="text-md font-extrabold text-slate-800 uppercase tracking-wide">Laporan Rencana & Realisasi Keuangan</h2>
            <p class="text-xs text-slate-500 mt-1 font-medium">Kegiatan: {{ $report->activity_name }}</p>
        </div>

        <!-- 1. RENCANA PEMASUKAN TABLE -->
        <div class="space-y-3">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">1. Rencana Pemasukan</h3>
            <div class="overflow-x-auto border border-slate-200 rounded-xl">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-emerald-600 text-white">
                        <tr>
                            <th class="px-4 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider w-16">No</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-bold uppercase tracking-wider">Uraian Pemasukan</th>
                            <th class="px-4 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider w-24">Qty</th>
                            <th class="px-4 py-2.5 text-right text-[10px] font-bold uppercase tracking-wider w-36">Harga</th>
                            <th class="px-4 py-2.5 text-right text-[10px] font-bold uppercase tracking-wider w-36">Total</th>
                            @if(Auth::id() === $report->user_id)
                                <th class="px-4 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider w-16"></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-150 bg-white">
                        @forelse($incomeItems as $index => $item)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3 text-center text-xs font-semibold text-slate-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-xs font-bold text-slate-800">{{ $item->description }}</td>
                                <td class="px-4 py-3 text-center text-xs font-semibold text-slate-700">{{ $item->qty }}</td>
                                <td class="px-4 py-3 text-right text-xs font-bold text-slate-700">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-xs font-extrabold text-slate-800">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                @if(Auth::id() === $report->user_id)
                                    <td class="px-4 py-3 text-center">
                                        <form action="{{ route('finance.activities.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus item ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-500 hover:text-rose-700 font-bold text-xs p-1">✕</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ Auth::id() === $report->user_id ? 6 : 5 }}" class="px-4 py-6 text-center text-xs text-slate-400 italic">Belum ada rencana pemasukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50 border-t border-slate-200">
                        <tr class="font-extrabold text-slate-800">
                            <td colspan="4" class="px-4 py-3 text-center text-xs">Total Pemasukan</td>
                            <td class="px-4 py-3 text-right text-xs text-emerald-600">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                            @if(Auth::id() === $report->user_id)
                                <td></td>
                            @endif
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- 2. RENCANA PENGELUARAN TABLE -->
        <div class="space-y-3">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">2. Rencana Pengeluaran</h3>
            <div class="overflow-x-auto border border-slate-200 rounded-xl">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-indigo-600 text-white">
                        <tr>
                            <th class="px-4 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider w-16">No</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-bold uppercase tracking-wider">Uraian Pengeluaran</th>
                            <th class="px-4 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider w-24">Qty</th>
                            <th class="px-4 py-2.5 text-right text-[10px] font-bold uppercase tracking-wider w-36">Harga</th>
                            <th class="px-4 py-2.5 text-right text-[10px] font-bold uppercase tracking-wider w-36">Total</th>
                            @if(Auth::id() === $report->user_id)
                                <th class="px-4 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider w-16"></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-150 bg-white">
                        @forelse($expenseItems as $index => $item)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3 text-center text-xs font-semibold text-slate-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-xs font-bold text-slate-800">{{ $item->description }}</td>
                                <td class="px-4 py-3 text-center text-xs font-semibold text-slate-700">{{ $item->qty }}</td>
                                <td class="px-4 py-3 text-right text-xs font-bold text-slate-700">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-xs font-extrabold text-slate-800">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                @if(Auth::id() === $report->user_id)
                                    <td class="px-4 py-3 text-center">
                                        <form action="{{ route('finance.activities.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus item ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-500 hover:text-rose-700 font-bold text-xs p-1">✕</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ Auth::id() === $report->user_id ? 6 : 5 }}" class="px-4 py-6 text-center text-xs text-slate-400 italic">Belum ada rencana pengeluaran</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50 border-t border-slate-200">
                        <tr class="font-extrabold text-slate-800">
                            <td colspan="4" class="px-4 py-3 text-center text-xs">Total Pengeluaran</td>
                            <td class="px-4 py-3 text-right text-xs text-rose-600">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                            @if(Auth::id() === $report->user_id)
                                <td></td>
                            @endif
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- SUMMARY TABLE -->
        <div class="max-w-md ml-auto border border-slate-200 rounded-xl overflow-hidden bg-slate-50/50">
            <table class="min-w-full divide-y divide-slate-100">
                <tbody class="text-xs font-bold text-slate-700">
                    <tr>
                        <td class="px-4 py-2.5">Total Pemasukan</td>
                        <td class="px-4 py-2.5 text-right text-emerald-600 font-extrabold">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2.5">Total Pengeluaran</td>
                        <td class="px-4 py-2.5 text-right text-rose-600 font-extrabold">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="bg-slate-100/80 text-slate-800 font-extrabold border-t border-slate-200">
                        <td class="px-4 py-3">Sisa Akhir</td>
                        <td class="px-4 py-3 text-right text-indigo-600">Rp {{ number_format($balance, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- SIGNATURE BLOCK -->
        <div class="flex justify-end pt-12">
            <div class="text-center w-64 space-y-16">
                <div>
                    <p class="text-xs text-slate-500 font-medium italic">
                        {{ $report->city }}, {{ $report->date->translatedFormat('d F Y') }}
                    </p>
                </div>
                <div class="space-y-1">
                    <p class="text-xs font-extrabold text-slate-800 underline">
                        {{ $report->user->name ?? 'Mahasiswa' }}
                    </p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                        Bendahara Kelompok
                    </p>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal: Tambah Item Laporan -->
    <div x-show="openItemModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" @keydown.escape.window="openItemModal = false">
        <div class="bg-white rounded-2xl max-w-sm w-full overflow-hidden shadow-2xl border border-slate-150" @click.away="openItemModal = false" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="px-6 py-4 bg-slate-55 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-800">
                    Tambah Rincian <span class="capitalize" x-text="activeType"></span>
                </h3>
                <button @click="openItemModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('finance.activities.items.store', $report->id) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="type" :value="activeType">
                
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Uraian / Deskripsi Item</label>
                    <input type="text" name="description" required placeholder="Contoh: Kertas Karton, Konsumsi Pemateri, dst..." class="w-full h-[38px] rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-white">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Kuantitas (Qty)</label>
                        <input type="number" name="qty" required value="1" min="1" class="w-full h-[38px] rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Harga Satuan</label>
                        <div class="flex rounded-xl border border-slate-200 overflow-hidden focus-within:border-emerald-500 focus-within:ring-1 focus-within:ring-emerald-500 h-[38px]">
                            <span class="inline-flex items-center px-3 bg-slate-50 border-r border-slate-200 text-xs font-bold text-slate-500 flex-shrink-0">Rp</span>
                            <input type="number" name="price" required placeholder="0" class="w-full border-0 px-3 py-2 text-xs text-slate-700 font-bold focus:ring-0 focus:outline-none bg-white">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="openItemModal = false" class="px-4 py-2 border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 font-semibold text-[10px] transition">
                        Batal
                    </button>
                    <button type="submit" style="background-color: #059669;" class="px-5 py-2 hover:bg-emerald-700 text-white rounded-xl font-semibold text-[10px] transition shadow-sm animate-button">
                        Tambah Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
