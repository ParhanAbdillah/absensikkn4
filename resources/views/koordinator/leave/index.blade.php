<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-800 tracking-tight">Permohonan Izin & Sakit</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-sm font-semibold text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if($pendingCount > 0)
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-3">
                <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <p class="text-sm font-bold text-amber-800">{{ $pendingCount }} permohonan menunggu ulasan Anda.</p>
            </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden animate-card">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-700 text-sm uppercase tracking-wider">Semua Permohonan</h3>
                </div>

                @if($requests->isEmpty())
                    <div class="py-16 text-center text-slate-400">
                        <svg class="w-14 h-14 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p class="text-sm font-semibold">Belum ada permohonan masuk.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-50">
                        @foreach($requests as $req)
                        <div class="p-5">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                                    {{ $req->type === 'izin' ? 'bg-indigo-50 text-indigo-600' : 'bg-rose-50 text-rose-500' }}">
                                    @if($req->type === 'izin')
                                        <span class="text-lg">📋</span>
                                    @else
                                        <span class="text-lg">🤒</span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                        <span class="text-sm font-bold text-slate-800">{{ $req->user->name }}</span>
                                        <span class="text-xs text-slate-400">•</span>
                                        <span class="text-xs font-semibold {{ $req->type === 'izin' ? 'text-indigo-600' : 'text-rose-600' }}">{{ $req->type_label }}</span>
                                        <span class="text-xs text-slate-400">•</span>
                                        <span class="text-xs text-slate-500">{{ $req->date->isoFormat('D MMMM Y') }}</span>
                                    </div>
                                    <p class="text-xs text-slate-600 mt-1">{{ $req->reason }}</p>

                                    @if($req->status === 'pending')
                                    <div class="flex items-center gap-2 mt-3">
                                        <!-- Approve -->
                                        <form action="{{ route('koordinator.leave.approve', $req) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit" class="w-full px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition">
                                                ✓ Setujui
                                            </button>
                                        </form>
                                        <!-- Reject with notes -->
                                        <form action="{{ route('koordinator.leave.reject', $req) }}" method="POST" class="flex-1" onsubmit="return handleReject(this, {{ $req->id }})">
                                            @csrf
                                            <input type="hidden" name="notes" id="notes-{{ $req->id }}">
                                            <button type="submit" class="w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition">
                                                ✕ Tolak
                                            </button>
                                        </form>
                                    </div>
                                    @else
                                        @php $color = $req->status_color; @endphp
                                        <div class="flex items-center gap-2 mt-2">
                                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full
                                                {{ $color === 'emerald' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                                {{ $req->status_label }}
                                            </span>
                                            @if($req->notes)
                                                <span class="text-xs text-slate-400 italic">{{ $req->notes }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
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

    <script>
        function handleReject(form, id) {
            const notes = prompt('Masukkan alasan penolakan:');
            if (!notes) return false;
            document.getElementById('notes-' + id).value = notes;
            return true;
        }
    </script>
</x-app-layout>
