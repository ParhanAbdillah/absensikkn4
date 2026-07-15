<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-800 tracking-tight">Riwayat Izin / Sakit</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-sm font-semibold text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-700">Permohonan Saya</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Semua pengajuan izin & sakit Anda</p>
                </div>
                <a href="{{ route('anggota.leave.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-widest rounded-xl transition shadow-md shadow-indigo-200 animate-button">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Ajukan Baru
                </a>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden animate-card">
                @if($requests->isEmpty())
                    <div class="py-16 text-center text-slate-400">
                        <svg class="w-14 h-14 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p class="text-sm font-semibold">Belum ada permohonan</p>
                        <p class="text-xs mt-1">Tekan tombol "Ajukan Baru" untuk membuat permohonan izin/sakit.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-50">
                        @foreach($requests as $req)
                        <div class="flex items-start gap-4 px-5 py-4 hover:bg-slate-50 transition">
                            <div class="mt-1 w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                                {{ $req->type === 'izin' ? 'bg-indigo-50 text-indigo-600' : 'bg-rose-50 text-rose-500' }}">
                                @if($req->type === 'izin')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-bold text-slate-800">{{ $req->type_label }}</span>
                                    <span class="text-xs text-slate-400">•</span>
                                    <span class="text-xs text-slate-500">{{ $req->date->isoFormat('D MMMM Y') }}</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5 truncate">{{ $req->reason }}</p>
                                @if($req->notes)
                                    <p class="text-xs text-slate-400 mt-1 italic">Catatan: {{ $req->notes }}</p>
                                @endif
                            </div>
                            <div class="flex-shrink-0">
                                @php $color = $req->status_color; @endphp
                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full
                                    {{ $color === 'yellow' ? 'bg-yellow-50 text-yellow-700 border border-yellow-200' : ($color === 'emerald' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200') }}">
                                    {{ $req->status_label }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="px-5 py-4 border-t border-slate-100">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
