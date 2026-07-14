<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard DPL') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 flex justify-between items-center">
                    <div>
                        <p class="text-lg font-bold text-blue-600 mb-1">Selamat Datang, {{ Auth::user()->name }}!</p>
                        <p class="text-sm text-gray-600">Dosen Pembimbing Lapangan (DPL)</p>
                    </div>
                    <div class="bg-blue-50 px-4 py-3 rounded border">
                        <span class="text-xs uppercase text-blue-700 font-bold block">Total Anggota Dibimbing</span>
                        <span class="text-2xl font-bold text-blue-900">{{ $totalMembers }} Mahasiswa</span>
                    </div>
                </div>
            </div>

            <!-- Monitoring Kehadiran -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg text-gray-800 mb-4">Log Riwayat Kehadiran Kelompok KKN</h3>

                    @if($attendances->isEmpty())
                        <p class="text-sm text-gray-500 text-center py-6">Belum ada data absensi masuk.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mahasiswa</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kegiatan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal/Waktu</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jarak GPS & Match</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Foto Absen</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi Approval</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($attendances as $attendance)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold text-gray-900">{{ $attendance->user->name }}</div>
                                                <div class="text-xs text-gray-500">NIM: {{ $attendance->user->nim }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $attendance->schedule->title }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $attendance->check_in_at->format('d-m-Y H:i') }} WIB
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                Jarak: {{ round($attendance->distance_meters, 1) }}m<br>
                                                Match: {{ $attendance->face_match_score ? round((1 - $attendance->face_match_score) * 100, 1) . '%' : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($attendance->photo_path)
                                                    <img src="{{ Storage::url($attendance->photo_path) }}" alt="Foto" class="w-12 h-12 object-cover rounded border">
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $attendance->status === 'hadir' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $attendance->status === 'terlambat' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $attendance->status === 'alpha' ? 'bg-red-100 text-red-800' : '' }}">
                                                    {{ ucfirst($attendance->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                @if(!$attendance->approved_by && $attendance->status !== 'hadir')
                                                    <form action="{{ route('dpl.attendance.approve', $attendance) }}" method="POST" class="flex gap-2">
                                                        @csrf
                                                        <input type="text" name="notes" placeholder="Catatan DPL" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded shadow-sm text-xs p-1" required>
                                                        <button type="submit" class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-semibold">
                                                            Approve
                                                        </button>
                                                    </form>
                                                @elseif($attendance->approved_by)
                                                    <span class="text-green-600 font-semibold text-xs">Disetujui Manual</span>
                                                    <div class="text-[10px] text-gray-500">Oleh: {{ $attendance->approvedBy->name }}</div>
                                                @else
                                                    <span class="text-gray-400 text-xs">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $attendances->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
