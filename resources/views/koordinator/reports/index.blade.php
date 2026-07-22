<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-800 tracking-tight">
            Pantauan Laporan Kegiatan Mingguan
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">Semua Laporan Anggota</h3>
                        <div class="flex items-center gap-4 text-xs mt-1">
                            <div class="flex items-center gap-1.5 text-red-600 font-medium">
                                <div class="w-3 h-3 bg-red-100 border border-red-200 rounded-full"></div>
                                Mendekati Deadline (< 3 hari)
                            </div>
                            <div class="flex items-center gap-1.5 text-emerald-600 font-medium">
                                <div class="w-3 h-3 bg-emerald-100 border border-emerald-200 rounded-full"></div>
                                Selesai
                            </div>
                        </div>
                    </div>
                    <div>
                        <form method="GET" action="{{ url()->current() }}">
                            <select name="status" onchange="this.form.submit()" class="text-xs font-semibold bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Semua</option>
                                <option value="To Do" {{ request('status') === 'To Do' ? 'selected' : '' }}>To Do</option>
                                <option value="In Progress" {{ request('status') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Done" {{ request('status') === 'Done' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-slate-500 bg-slate-50 uppercase font-bold tracking-wider">
                            <tr>
                                <th class="px-6 py-4">No.</th>
                                <th class="px-6 py-4">Anggota</th>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4 min-w-[200px]">Kegiatan (What)</th>
                                <th class="px-6 py-4">Deadline</th>
                                <th class="px-6 py-4">PIC</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($reports as $index => $report)
                            @php
                                $isNearDeadline = $report->status !== 'Done' && $report->deadline->diffInDays(now()) <= 3 && $report->deadline >= now();
                                $isOverdue = $report->status !== 'Done' && $report->deadline < now();
                                $bgClass = $report->status === 'Done' ? 'bg-emerald-50/30' : ($isOverdue || $isNearDeadline ? 'bg-red-50/50' : 'hover:bg-slate-50/50');
                            @endphp
                            <tr class="{{ $bgClass }} transition-colors">
                                <td class="px-6 py-4 text-slate-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 font-bold text-slate-800 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($report->user->name, 0, 1)) }}
                                        </div>
                                        {{ $report->user->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600 whitespace-nowrap">{{ $report->tanggal->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-slate-800 font-medium">{{ $report->nama_kegiatan }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-medium {{ $isOverdue || $isNearDeadline ? 'text-red-600' : 'text-slate-600' }}">
                                        {{ $report->deadline->format('d M Y') }}
                                    </span>
                                    @if($isOverdue)
                                        <span class="ml-1 text-[10px] font-bold text-red-600 uppercase tracking-wide bg-red-100 px-1.5 py-0.5 rounded">Lewat!</span>
                                    @elseif($isNearDeadline)
                                        <span class="ml-1 text-[10px] font-bold text-orange-500 uppercase tracking-wide bg-orange-100 px-1.5 py-0.5 rounded">Dekat</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-700 text-sm">
                                    {{ $report->person_in_charge ?: '-' }}
                                </td>
                                {{-- [NONAKTIF] PIC Dokumentasi (foto) - dinonaktifkan, jangan hapus
                                <td class="px-6 py-4">
                                    @if($report->pic)
                                        @php
                                            $isUrl = filter_var($report->pic, FILTER_VALIDATE_URL);
                                            $url = $isUrl ? $report->pic : asset('storage/' . $report->pic);
                                        @endphp
                                        <a href="{{ $url }}" target="_blank" class="block w-10 h-10 rounded border border-slate-200 overflow-hidden hover:scale-105 transition-transform shadow-sm flex items-center justify-center bg-slate-50" title="Lihat Dokumentasi">
                                            ...
                                        </a>
                                    @else
                                        <span class="text-slate-400 text-xs">-</span>
                                    @endif
                                </td>
                                --}}
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full border 
                                        @if($report->status === 'Done') bg-emerald-50 text-emerald-700 border-emerald-200
                                        @elseif($report->status === 'In Progress') bg-amber-50 text-amber-700 border-amber-200
                                        @else bg-blue-50 text-blue-700 border-blue-200
                                        @endif">
                                        {{ $report->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-500 text-xs max-w-[200px] truncate" title="{{ $report->notes }}">
                                    {{ $report->notes ?: '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                    Belum ada laporan kegiatan dari anggota.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
