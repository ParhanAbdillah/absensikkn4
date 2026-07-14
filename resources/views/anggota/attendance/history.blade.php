<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Absensi Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if($attendances->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            Anda belum pernah melakukan absensi.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal & Waktu</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kegiatan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jarak GPS</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kecocokan Wajah</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto Saat Absen</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persetujuan DPL</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($attendances as $attendance)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-950 font-medium">
                                                {{ $attendance->check_in_at->format('d-m-Y H:i') }} WIB
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 font-medium">{{ $attendance->schedule->title }}</div>
                                                <div class="text-xs text-gray-500">Lokasi: {{ $attendance->location->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ round($attendance->distance_meters, 1) }} meter
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $attendance->face_match_score ? round((1 - $attendance->face_match_score) * 100, 1) . '%' : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($attendance->photo_path)
                                                    <img src="{{ Storage::url($attendance->photo_path) }}" alt="Foto Absen" class="w-12 h-12 object-cover rounded border">
                                                @else
                                                    <span class="text-gray-400 text-xs">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $attendance->status === 'hadir' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $attendance->status === 'terlambat' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $attendance->status === 'alpha' ? 'bg-red-100 text-red-800' : '' }}
                                                    {{ in_array($attendance->status, ['izin', 'sakit']) ? 'bg-blue-100 text-blue-800' : '' }}">
                                                    {{ ucfirst($attendance->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($attendance->approved_by)
                                                    <span class="text-green-600 font-medium">Disetujui Manual</span>
                                                    <div class="text-xs text-gray-400">Oleh: {{ $attendance->approvedBy->name }}</div>
                                                @else
                                                    <span class="text-gray-400 text-xs">Otomatis Sistem</span>
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
