<x-app-layout>
<div class="space-y-6" x-data="{ openAddModal: false, openApproveModal: false, openPostModal: false, activeProposal: null, activeProposalName: '', activeProposalRequested: 0 }">
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
                    Laporan Kegiatan KKN & Pengajuan Dana
                </h1>
                <p class="text-xs text-slate-500 mt-1">Kelola proposal kegiatan seminar/acara KKN serta pengajuan alokasi anggaran</p>
            </div>
            <div class="flex items-center gap-2.5 self-start sm:self-center flex-shrink-0">
                <button @click="openAddModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl transition shadow-sm animate-button flex-shrink-0 whitespace-nowrap">
                    <span class="text-sm font-bold">+</span>
                    Ajukan Dana Kegiatan
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

    <!-- Alert Notification -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Card Total Pengajuan -->
        <div style="background: linear-gradient(135deg, #1e293b, #334155); border: 1px solid #475569;" class="rounded-2xl p-5 text-white shadow-md relative overflow-hidden animate-card">
            <div>
                <p class="text-slate-300 text-[10px] font-bold uppercase tracking-wider">Total Anggaran Diajukan</p>
                <h3 class="text-2xl font-extrabold mt-1.5 text-white">Rp {{ number_format($totalRequested, 2, ',', '.') }}</h3>
                <p class="text-slate-400 text-[10px] mt-2.5">Total dari keseluruhan proposal masuk</p>
            </div>
            <div class="absolute right-4 top-1/2 -translate-y-1/2 p-3 bg-white/5 rounded-xl text-slate-400">
                <span class="text-2xl">📝</span>
            </div>
        </div>

        <!-- Card Total Disetujui -->
        <div style="background: linear-gradient(135deg, #ffffff, #f0fdf4); border: 1px solid #bbf7d0;" class="rounded-2xl p-5 shadow-sm flex items-center justify-between relative overflow-hidden animate-card">
            <div>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Total Anggaran Disetujui</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1.5">Rp {{ number_format($totalApproved, 2, ',', '.') }}</h3>
                <p class="text-emerald-600 text-[10px] font-bold mt-2.5 inline-flex items-center gap-1 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                    Anggaran Realisasi
                </p>
            </div>
            <div class="p-3 bg-emerald-100/50 rounded-xl text-emerald-600 border border-emerald-200">
                <span class="text-xl">✅</span>
            </div>
        </div>

        <!-- Card Menunggu Persetujuan -->
        <div style="background: linear-gradient(135deg, #ffffff, #fffbeb); border: 1px solid #fde68a;" class="rounded-2xl p-5 shadow-sm flex items-center justify-between relative overflow-hidden animate-card">
            <div>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Menunggu Persetujuan</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1.5">{{ $pendingCount }} Proposal</h3>
                <p class="text-amber-600 text-[10px] font-bold mt-2.5 inline-flex items-center gap-1 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-100">
                    Butuh Review DPL/Ketua
                </p>
            </div>
            <div class="p-3 bg-amber-100/50 rounded-xl text-amber-600 border border-amber-200">
                <span class="text-xl">⏳</span>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-150 shadow-sm">
        <form action="{{ route('finance.proposals.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="w-full sm:w-48">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Status Pengajuan</label>
                <select name="status" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-white">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved (Disetujui)</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected (Ditolak)</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed (Kas Terposting)</option>
                </select>
            </div>
            <button type="submit" class="inline-flex items-center justify-center px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition shadow-sm h-[38px] text-xs font-semibold">
                Filter
            </button>
            @if(request()->filled('status'))
                <a href="{{ route('finance.proposals.index') }}" class="inline-flex items-center justify-center px-4 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-xl transition h-[38px] text-xs font-semibold">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Proposals Table -->
    <div class="bg-white rounded-2xl border border-slate-150 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Kegiatan</th>
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pengaju</th>
                        <th class="px-5 py-3.5 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">Anggaran Diajukan</th>
                        <th class="px-5 py-3.5 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">Anggaran Disetujui</th>
                        <th class="px-5 py-3.5 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">File Berkas</th>
                        <th class="px-5 py-3.5 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3.5 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($proposals as $p)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-4 text-xs font-bold text-slate-800">
                                {{ $p->activity_name }}
                                <p class="text-[10px] text-slate-400 font-normal mt-0.5 truncate max-w-xs">{{ $p->description }}</p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-500 font-medium">
                                {{ $p->date->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-500">
                                {{ $p->user->name ?? 'Mahasiswa' }}
                                <span class="block text-[9px] text-slate-400">{{ $p->user->divisi ?? '' }}</span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-right font-semibold text-slate-600">
                                Rp {{ number_format($p->budget_requested, 2, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-right font-bold text-slate-800">
                                Rp {{ number_format($p->budget_approved, 2, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-center text-xs">
                                @if($p->file_path)
                                    <a href="{{ asset($p->file_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] text-indigo-600 hover:text-indigo-800 font-semibold bg-indigo-50 px-2.5 py-1.5 rounded-lg border border-indigo-100 transition">
                                        📄 Unduh Berkas
                                    </a>
                                @else
                                    <span class="text-[10px] text-slate-400 italic">Tidak ada berkas</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-center text-xs">
                                @if($p->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-100">
                                        Pending
                                    </span>
                                @elseif($p->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        Approved
                                    </span>
                                @elseif($p->status === 'rejected')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-100">
                                        Rejected
                                    </span>
                                @elseif($p->status === 'completed')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                        Completed (Kas)
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-center text-xs flex items-center justify-center gap-1.5">
                                <!-- 1. Review & Approve (Only Koordinator & DPL) -->
                                @if($p->status === 'pending' && (Auth::user()->isKoordinator() || Auth::user()->isDpl()))
                                    <button @click="activeProposal = '{{ $p->id }}'; activeProposalName = '{{ $p->activity_name }}'; activeProposalRequested = '{{ $p->budget_requested }}'; openApproveModal = true" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded font-semibold text-[10px] transition">
                                        Review
                                    </button>
                                    <form action="{{ route('finance.proposals.reject', $p->id) }}" method="POST" onsubmit="return confirm('Tolak anggaran pengajuan ini?')">
                                        @csrf
                                        <button type="submit" class="px-2 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded font-semibold text-[10px] transition">
                                            Tolak
                                        </button>
                                    </form>
                                @endif

                                <!-- 2. Post to Cash (Only Bendahara & Koordinator) -->
                                @if($p->status === 'approved' && !$p->is_posted_to_cash && (Auth::user()->isBendahara() || Auth::user()->isKoordinator()))
                                    <button @click="activeProposal = '{{ $p->id }}'; openPostModal = true" class="px-2.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-bold text-[10px] shadow-sm transition">
                                        Post ke Kas 💰
                                    </button>
                                @endif

                                <!-- 3. Delete Proposal (Owner or Koordinator) -->
                                @if(Auth::id() === $p->user_id || Auth::user()->isKoordinator())
                                    <form action="{{ route('finance.proposals.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus pengajuan ini?')">
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
                            <td colspan="8" class="px-6 py-12 text-center text-xs text-slate-400 font-medium">
                                <span class="block text-3xl mb-2">📋</span>
                                Belum ada pengajuan anggaran kegiatan seminar/acara.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($proposals->hasPages())
            <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-100">
                {{ $proposals->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: Ajukan Dana Baru -->
    <div x-show="openAddModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" @keydown.escape.window="openAddModal = false">
        <div class="bg-white rounded-2xl max-w-md w-full overflow-hidden shadow-2xl border border-slate-150" @click.away="openAddModal = false" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="px-6 py-4 bg-slate-55 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800">Ajukan Anggaran Kegiatan Baru</h3>
                <button @click="openAddModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('finance.proposals.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nama Kegiatan</label>
                    <input type="text" name="activity_name" required placeholder="Contoh: Seminar Kesehatan & Sanitasi Desa" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tanggal Acara</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full h-[38px] rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Anggaran Diajukan</label>
                        <div class="flex rounded-xl border border-slate-200 overflow-hidden focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 h-[38px]">
                            <span class="inline-flex items-center px-3 bg-slate-50 border-r border-slate-200 text-xs font-bold text-slate-500 flex-shrink-0">Rp</span>
                            <input type="number" step="0.01" min="0.01" name="budget_requested" required placeholder="0.00" class="w-full border-0 px-3 py-2 text-xs text-slate-700 font-bold focus:ring-0 focus:outline-none bg-white">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Deskripsi Kegiatan & Alokasi Dana</label>
                    <textarea name="description" required rows="4" placeholder="Detail tujuan kegiatan seminar/acara serta rincian singkat alokasi dana..." class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"></textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Upload Proposal / Estimasi Biaya (PDF/Word)</label>
                    <input type="file" name="proposal_file" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 file:cursor-pointer">
                    <p class="text-[9px] text-slate-400 mt-1">Maksimal file 4MB, format PDF, Word, atau Zip</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 font-semibold text-[10px] transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold text-[10px] transition shadow-sm animate-button">
                        Ajukan Anggaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Review & Approve Anggaran -->
    <div x-show="openApproveModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" @keydown.escape.window="openApproveModal = false">
        <div class="bg-white rounded-2xl max-w-sm w-full overflow-hidden shadow-2xl border border-slate-150" @click.away="openApproveModal = false" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-800">Setujui Anggaran Kegiatan</h3>
                <button @click="openApproveModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form :action="'{{ url('finance/proposals') }}/' + activeProposal + '/approve'" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <p class="text-xs text-slate-500 leading-relaxed mb-3">
                        Menyetujui anggaran untuk kegiatan <strong class="text-slate-800" x-text="activeProposalName"></strong>.
                        Nominal yang diajukan adalah <strong class="text-indigo-600">Rp <span x-text="parseFloat(activeProposalRequested).toLocaleString('id-ID')"></span></strong>.
                    </p>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Anggaran yang Disetujui (Rupiah)</label>
                    <div class="flex rounded-xl border border-slate-200 overflow-hidden focus-within:border-emerald-500 focus-within:ring-1 focus-within:ring-emerald-500 h-[38px]">
                        <span class="inline-flex items-center px-3.5 bg-slate-50 border-r border-slate-200 text-xs font-bold text-slate-500 flex-shrink-0">Rp</span>
                        <input type="number" step="0.01" min="0" name="budget_approved" :max="activeProposalRequested" required :value="activeProposalRequested" class="w-full border-0 px-3 py-2 text-xs text-slate-700 font-bold focus:ring-0 focus:outline-none bg-white">
                    </div>
                    <p class="text-[9px] text-slate-400 mt-1">Anda bisa mengurangi nominal dana jika dibutuhkan penyesuaian</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="openApproveModal = false" class="px-4 py-2 border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 font-semibold text-[10px] transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold text-[10px] transition shadow-sm animate-button">
                        Setujui Anggaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Post ke Buku Kas -->
    <div x-show="openPostModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" @keydown.escape.window="openPostModal = false">
        <div class="bg-white rounded-2xl max-w-sm w-full overflow-hidden shadow-2xl border border-slate-150" @click.away="openPostModal = false" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="px-6 py-4 bg-slate-55 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-800">Catat Realisasi ke Buku Kas</h3>
                <button @click="openPostModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form :action="'{{ url('finance/proposals') }}/' + activeProposal + '/post-to-cash'" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Perintah ini akan mencatat anggaran yang disetujui sebagai **Pengeluaran** baru di Buku Kas Utama kelompok KKN.
                    </p>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Kategori Pengeluaran Kas</label>
                    <select name="category" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-white">
                        <option value="Konsumsi">Konsumsi</option>
                        <option value="Peralatan / Perlengkapan">Peralatan / Perlengkapan</option>
                        <option value="PDD / Dokumentasi">PDD / Dokumentasi</option>
                        <option value="Transportasi">Transportasi</option>
                        <option value="Print / Jilid / Fotokopi">Print / Jilid / Fotokopi</option>
                        <option value="Lain-lain" selected>Lain-lain</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="openPostModal = false" class="px-4 py-2 border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 font-semibold text-[10px] transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold text-[10px] transition shadow-sm animate-button">
                        Posting Pengeluaran Kas 💰
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
