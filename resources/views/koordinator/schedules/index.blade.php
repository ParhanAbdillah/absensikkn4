<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <h2 class="font-extrabold text-xl text-slate-800 tracking-tight">
                {{ __('Kelola Jadwal Kegiatan') }}
            </h2>
            <button @click="$dispatch('open-modal', 'modal-tambah-jadwal')" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-widest rounded-xl transition shadow-md shadow-emerald-200">
                + Tambah Jadwal
            </button>
        </div>
    </x-slot>

    <div class="py-6" x-data="scheduleIndex()">
        <div class="max-w-7xl mx-auto">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-8">
                    @if($schedules->isEmpty())
                        <div class="text-center py-12 text-slate-400">
                            <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Belum ada jadwal kegiatan yang ditambahkan. Silakan tambah jadwal baru.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-600 text-xs font-bold uppercase tracking-wider">
                                        <th class="px-6 py-4 text-left">Kegiatan</th>
                                        <th class="px-6 py-4 text-left">Lokasi Absen</th>
                                        <th class="px-6 py-4 text-left">Tanggal</th>
                                        <th class="px-6 py-4 text-left">Waktu Mulai</th>
                                        <th class="px-6 py-4 text-left">Toleransi Terlambat</th>
                                        <th class="px-6 py-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100 text-sm text-slate-700">
                                    @foreach($schedules as $schedule)
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-slate-900">{{ $schedule->title }}</div>
                                                <div class="text-xs text-slate-500">{{ Str::limit($schedule->description, 50) }}</div>
                                            </td>
                                            <td class="px-6 py-4 font-semibold text-slate-600">{{ $schedule->location->name }}</td>
                                            <td class="px-6 py-4 text-slate-500">{{ $schedule->activity_date->format('d-m-Y') }}</td>
                                            <td class="px-6 py-4 text-slate-900 font-medium">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} WIB</td>
                                            <td class="px-6 py-4">
                                                @if($schedule->tolerance_time)
                                                    <span class="px-2.5 py-1 bg-yellow-50 text-yellow-700 font-semibold rounded-full text-xs">
                                                        {{ \Carbon\Carbon::parse($schedule->tolerance_time)->format('H:i') }} WIB
                                                    </span>
                                                @else
                                                    <span class="text-slate-400 text-xs">Tepat Waktu</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <button @click="openEditModal({{ json_encode($schedule) }})" class="text-emerald-600 hover:text-emerald-900 font-bold mr-4 text-xs uppercase tracking-wide">Edit</button>
                                                
                                                <form action="{{ route('koordinator.schedules.destroy', $schedule) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 font-bold text-xs uppercase tracking-wide">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Modal Tambah Jadwal -->
        <x-modal name="modal-tambah-jadwal" :show="false" focusable>
            <form action="{{ route('koordinator.schedules.store') }}" method="POST" class="p-6">
                @csrf
                <h3 class="text-lg font-bold text-slate-900 mb-4">Tambah Jadwal Baru</h3>
                
                <div class="mb-4">
                    <x-input-label for="title" value="Nama Kegiatan / Judul" />
                    <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" required placeholder="Contoh: Kerja Bakti / Rapat Posko" />
                </div>

                <div class="mb-4">
                    <x-input-label for="description" value="Deskripsi Kegiatan" />
                    <textarea id="description" name="description" rows="2" class="border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm block mt-1 w-full" placeholder="Detail kegiatan..."></textarea>
                </div>

                <div class="mb-4">
                    <x-input-label for="location_id" value="Lokasi Absensi" />
                    <select id="location_id" name="location_id" class="border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm block mt-1 w-full" required>
                        <option value="">-- Pilih Lokasi Target --</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }} ({{ $location->latitude }}, {{ $location->longitude }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <x-input-label for="activity_date" value="Tanggal Kegiatan" />
                        <x-text-input id="activity_date" class="block mt-1 w-full" type="date" name="activity_date" required />
                    </div>
                    <div>
                        <x-input-label for="start_time" value="Waktu Mulai" />
                        <x-text-input id="start_time" class="block mt-1 w-full" type="time" name="start_time" required />
                    </div>
                    <div>
                        <x-input-label for="tolerance_time" value="Batas Telat (Toleransi)" />
                        <x-text-input id="tolerance_time" class="block mt-1 w-full" type="time" name="tolerance_time" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button @click="$dispatch('close')">Batal</x-secondary-button>
                    <x-primary-button class="bg-emerald-600 hover:bg-emerald-700">Simpan Jadwal</x-primary-button>
                </div>
            </form>
        </x-modal>

        <!-- Modal Edit Jadwal -->
        <x-modal name="modal-edit-jadwal" :show="false" focusable>
            <form :action="'/koordinator/schedules/' + editData.id" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <h3 class="text-lg font-bold text-slate-900 mb-4">Edit Jadwal Kegiatan</h3>
                
                <div class="mb-4">
                    <x-input-label for="edit_title" value="Nama Kegiatan / Judul" />
                    <x-text-input id="edit_title" class="block mt-1 w-full" type="text" name="title" x-model="editData.title" required />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_description" value="Deskripsi Kegiatan" />
                    <textarea id="edit_description" name="description" rows="2" class="border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm block mt-1 w-full" x-model="editData.description"></textarea>
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_location_id" value="Lokasi Absensi" />
                    <select id="edit_location_id" name="location_id" class="border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm block mt-1 w-full" x-model="editData.location_id" required>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <x-input-label for="edit_activity_date" value="Tanggal Kegiatan" />
                        <x-text-input id="edit_activity_date" class="block mt-1 w-full" type="date" name="activity_date" x-model="editData.activity_date" required />
                    </div>
                    <div>
                        <x-input-label for="edit_start_time" value="Waktu Mulai" />
                        <x-text-input id="edit_start_time" class="block mt-1 w-full" type="time" name="start_time" x-model="editData.start_time" required />
                    </div>
                    <div>
                        <x-input-label for="edit_tolerance_time" value="Batas Telat (Toleransi)" />
                        <x-text-input id="edit_tolerance_time" class="block mt-1 w-full" type="time" name="tolerance_time" x-model="editData.tolerance_time" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button @click="$dispatch('close')">Batal</x-secondary-button>
                    <x-primary-button class="bg-emerald-600 hover:bg-emerald-700">Simpan Perubahan</x-primary-button>
                </div>
            </form>
        </x-modal>
    </div>

    <script>
        function scheduleIndex() {
            return {
                editData: { id: '', title: '', description: '', location_id: '', activity_date: '', start_time: '', tolerance_time: '' },
                openEditModal(data) {
                    // format date to YYYY-MM-DD
                    let dateObj = new Date(data.activity_date);
                    let month = '' + (dateObj.getMonth() + 1);
                    let day = '' + dateObj.getDate();
                    let year = dateObj.getFullYear();
                    if (month.length < 2) month = '0' + month;
                    if (day.length < 2) day = '0' + day;
                    
                    this.editData = { 
                        ...data, 
                        activity_date: [year, month, day].join('-'),
                        start_time: data.start_time.substring(0, 5),
                        tolerance_time: data.tolerance_time ? data.tolerance_time.substring(0, 5) : ''
                    };
                    this.$dispatch('open-modal', 'modal-edit-jadwal');
                }
            }
        }
    </script>
</x-app-layout>
