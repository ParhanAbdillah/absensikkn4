<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-800 tracking-tight">
            {{ __('Riwayat Absensi Saya') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200 animate-card">
                <div class="p-8">
                    
                    @if($attendances->isEmpty())
                        <div class="text-center py-12 text-slate-400">
                            <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-sm font-semibold">Anda belum pernah melakukan absensi.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-600 text-xs font-bold uppercase tracking-wider">
                                        <th class="px-6 py-4 text-left">Tanggal & Waktu</th>
                                        <th class="px-6 py-4 text-left">Lokasi Absen</th>
                                        <th class="px-6 py-4 text-left">Jarak GPS</th>
                                        <th class="px-6 py-4 text-left">Kecocokan Wajah</th>
                                        <th class="px-6 py-4 text-left">Foto Selfie</th>
                                        <th class="px-6 py-4 text-left">Status</th>
                                        <th class="px-6 py-4 text-left">Persetujuan DPL</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100 text-sm text-slate-700">
                                    @foreach($attendances as $attendance)
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="px-6 py-4 font-bold text-slate-900">
                                                {{ $attendance->check_in_at->format('d-m-Y H:i') }} WIB
                                            </td>
                                            <td class="px-6 py-4 font-semibold text-slate-600">
                                                {{ $attendance->location->name ?? 'GPS Posko' }}
                                            </td>
                                            <td class="px-6 py-4 text-slate-500 font-mono text-xs">
                                                {{ round($attendance->distance_meters, 1) }} meter
                                            </td>
                                            <td class="px-6 py-4 text-slate-500 font-mono text-xs">
                                                {{ $attendance->face_match_score ? round((1 - $attendance->face_match_score) * 100, 1) . '%' : '-' }}
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($attendance->photo_path)
                                                    <img src="{{ Storage::url($attendance->photo_path) }}" alt="Foto Absen" class="w-12 h-12 object-cover rounded-xl border border-slate-200 shadow-sm">
                                                @else
                                                    <span class="text-slate-450 text-xs">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full 
                                                    {{ $attendance->status === 'hadir' ? 'bg-green-50 text-green-700' : '' }}
                                                    {{ $attendance->status === 'terlambat' ? 'bg-yellow-50 text-yellow-700' : '' }}
                                                    {{ $attendance->status === 'alpha' ? 'bg-red-50 text-red-700' : '' }}">
                                                    {{ $attendance->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($attendance->approved_by)
                                                    <span class="text-emerald-700 font-bold text-xs uppercase tracking-wide">Disetujui DPL</span>
                                                    <div class="text-[10px] text-slate-400 font-semibold">Oleh: {{ $attendance->approvedBy->name }}</div>
                                                @else
                                                    <span class="text-slate-400 text-xs">Sistem Otomatis</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-6">
                            {{ $attendances->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
