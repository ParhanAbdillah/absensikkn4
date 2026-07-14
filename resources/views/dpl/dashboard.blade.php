<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-800 tracking-tight">
            {{ __('Dashboard DPL') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto">

            <!-- Welcome Profile Panel -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <p class="text-lg font-extrabold text-emerald-700 mb-1">Selamat Datang, {{ Auth::user()->name }}!</p>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Dosen Pembimbing Lapangan (DPL)</p>
                </div>
                <div class="bg-emerald-50/50 px-4 py-3 rounded-2xl border border-emerald-100 flex items-center gap-3">
                    <span class="p-2 bg-emerald-100 text-emerald-700 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </span>
                    <div>
                        <span class="text-[10px] uppercase text-slate-400 font-bold block">Mahasiswa Dibimbing</span>
                        <span class="text-lg font-extrabold text-emerald-800 leading-none">{{ $totalMembers }} Orang</span>
                    </div>
                </div>
            </div>

            <!-- Monitoring Kehadiran (Table List in Google Drive style) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="font-extrabold text-base text-slate-800 tracking-tight">Log Kehadiran Mahasiswa KKN</h3>
                </div>

                @if($attendances->isEmpty())
                    <div class="text-center py-12 text-slate-400">
                        <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <p class="text-sm font-semibold">Belum ada data absensi masuk untuk kelompok KKN ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead>
                                <tr class="bg-slate-50 text-slate-600 text-xs font-bold uppercase tracking-wider">
                                    <th class="px-6 py-4 text-left">Mahasiswa</th>
                                    <th class="px-6 py-4 text-left">Kegiatan</th>
                                    <th class="px-6 py-4 text-left">Tanggal/Waktu</th>
                                    <th class="px-6 py-4 text-left">GPS & Wajah</th>
                                    <th class="px-6 py-4 text-left">Foto Absen</th>
                                    <th class="px-6 py-4 text-left">Status</th>
                                    <th class="px-6 py-4 text-center">Aksi Approval</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 text-sm text-slate-700">
                                @foreach($attendances as $attendance)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-slate-900 leading-tight mb-1">{{ $attendance->user->name }}</div>
                                            <div class="text-xs text-slate-400 font-semibold uppercase">NIM: {{ $attendance->user->nim }}</div>
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-slate-600">
                                            {{ $attendance->schedule->title }}
                                        </td>
                                        <td class="px-6 py-4 text-slate-500">
                                            {{ $attendance->check_in_at->format('d-m-Y H:i') }} WIB
                                        </td>
                                        <td class="px-6 py-4 text-xs text-slate-500">
                                            <div class="font-semibold mb-1">Jarak: <span class="text-slate-700">{{ round($attendance->distance_meters, 1) }}m</span></div>
                                            <div class="font-semibold">Match: <span class="text-slate-700">{{ $attendance->face_match_score ? round((1 - $attendance->face_match_score) * 100, 1) . '%' : '-' }}</span></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($attendance->photo_path)
                                                <img src="{{ Storage::url($attendance->photo_path) }}" alt="Foto Selfie" class="w-12 h-12 object-cover rounded-xl border border-slate-200 shadow-sm">
                                            @else
                                                <span class="text-slate-400 text-xs">-</span>
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
                                        <td class="px-6 py-4 text-center">
                                            @if(!$attendance->approved_by && $attendance->status !== 'hadir')
                                                <button @click="$dispatch('open-modal', 'modal-approve-{{ $attendance->id }}')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                                                    Setujui Manual
                                                </button>

                                                <!-- Modal Persetujuan Manual DPL -->
                                                <x-modal name="modal-approve-{{ $attendance->id }}" :show="false" focusable>
                                                    <form action="{{ route('dpl.attendance.approve', $attendance) }}" method="POST" class="p-6 text-left">
                                                        @csrf
                                                        <h3 class="text-lg font-bold text-slate-900 mb-2">Persetujuan Absensi Manual</h3>
                                                        <p class="text-xs text-slate-500 mb-4">Setujui absensi untuk <strong>{{ $attendance->user->name }}</strong> secara manual jika terjadi kendala sistem.</p>
                                                        
                                                        <div class="mb-4">
                                                            <x-input-label for="notes_{{ $attendance->id }}" value="Catatan / Alasan Persetujuan" />
                                                            <x-text-input id="notes_{{ $attendance->id }}" class="block mt-1 w-full" type="text" name="notes" placeholder="Contoh: Kamera rusak / GPS error di lapangan" required />
                                                        </div>

                                                        <div class="flex justify-end gap-3">
                                                            <x-secondary-button @click="$dispatch('close')">Batal</x-secondary-button>
                                                            <x-primary-button class="bg-emerald-600 hover:bg-emerald-700">Setujui Sekarang</x-primary-button>
                                                        </div>
                                                    </form>
                                                </x-modal>
                                            @elseif($attendance->approved_by)
                                                <div class="text-emerald-700 font-bold text-xs uppercase tracking-wide mb-1">Disetujui DPL</div>
                                                <div class="text-[10px] text-slate-400 font-semibold">Oleh: {{ $attendance->approvedBy->name }}</div>
                                            @else
                                                <span class="text-slate-400 text-xs font-medium">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-6 border-t border-slate-100">
                        {{ $attendances->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
