<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-800 tracking-tight">
            Rekap Laporan Kegiatan Anggota
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 text-lg">Daftar Anggota</h3>
                    <p class="text-xs text-slate-500 mt-1">Klik nama anggota untuk melihat detail laporan kegiatannya</p>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($members as $index => $member)
                    @php
                        $hasBadge = $member->overdue_count > 0 || $member->near_deadline > 0;
                    @endphp
                    <a href="{{ route('koordinator.reports.show', $member->id) }}"
                        class="flex items-center justify-between px-5 py-4 hover:bg-slate-50 transition-colors group">

                        {{-- Avatar & Nama --}}
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm flex-shrink-0 group-hover:bg-emerald-200 transition-colors">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-bold text-slate-800 text-sm group-hover:text-emerald-700 transition-colors">
                                    {{ $member->name }}
                                    @if($hasBadge)
                                        <span class="ml-2 text-[10px] font-bold text-red-600 bg-red-100 px-1.5 py-0.5 rounded uppercase tracking-wide">
                                            Perlu Perhatian
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ $member->divisi ?: ($member->class ?: 'Anggota KKN') }}</div>
                            </div>
                        </div>

                        {{-- Badge Ringkasan --}}
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @if($member->done_count > 0)
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                {{ $member->done_count }} Done
                            </span>
                            @endif
                            @if($member->in_progress > 0)
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                {{ $member->in_progress }} Proses
                            </span>
                            @endif
                            @if($member->todo_count > 0)
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $member->todo_count }} To Do
                            </span>
                            @endif
                            @if($member->total_reports === 0)
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full bg-slate-50 text-slate-400 border border-slate-200">
                                Belum ada laporan
                            </span>
                            @endif

                            {{-- Arrow Icon --}}
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                    @empty
                    <div class="px-6 py-12 text-center text-slate-500">
                        Belum ada anggota terdaftar.
                    </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
