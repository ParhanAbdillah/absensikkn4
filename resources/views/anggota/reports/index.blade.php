<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-800 tracking-tight">
            Laporan Kegiatan Mingguan
        </h2>
    </x-slot>

    <div class="py-6" x-data="{ reportToEdit: null, reportToDelete: null, reportToUpload: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Header Section inside Content -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Daftar Kegiatan</h3>
                    <p class="text-xs text-slate-500 mt-1">Kelola dan laporkan kegiatan mingguan Anda.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <form method="GET" action="{{ url()->current() }}">
                        <select name="status" onchange="this.form.submit()" class="text-xs font-semibold bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua</option>
                            <option value="To Do" {{ request('status') === 'To Do' ? 'selected' : '' }}>To Do</option>
                            <option value="In Progress" {{ request('status') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Done" {{ request('status') === 'Done' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </form>
                    <a href="{{ route('anggota.reports.export') }}"
                       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl transition shadow-md shadow-emerald-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export Excel
                    </a>
                    <button @click="$dispatch('open-modal', 'create-report')" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl transition shadow-md shadow-indigo-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Kegiatan
                    </button>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-slate-500 bg-slate-50 uppercase font-bold tracking-wider">
                            <tr>
                                <th class="px-6 py-4">No.</th>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4 min-w-[200px]">What (Kegiatan)</th>
                                <th class="px-6 py-4">Deadline</th>
                                <th class="px-6 py-4">PIC</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Notes</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($reports as $index => $report)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 text-slate-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 font-medium text-slate-800 whitespace-nowrap">{{ $report->tanggal->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-slate-800 font-medium">{{ $report->nama_kegiatan }}</td>
                                <td class="px-6 py-4 text-slate-600 whitespace-nowrap">{{ $report->deadline->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-slate-700 text-sm">
                                    {{ $report->person_in_charge ?: '-' }}
                                </td>
                                {{-- [NONAKTIF] PIC Dokumentasi (upload foto) - dinonaktifkan, jangan hapus
                                <td class="px-6 py-4">
                                    @if($report->pic)
                                        @php
                                            $isUrl = filter_var($report->pic, FILTER_VALIDATE_URL);
                                            $url = $isUrl ? $report->pic : asset('storage/' . $report->pic);
                                        @endphp
                                        <div class="flex items-center gap-2">
                                            <a href="{{ $url }}" target="_blank" ...>...</a>
                                            <button @click="$dispatch('open-modal', 'upload-pic'); reportToUpload = {{ $report->id }}" ...>Ganti Foto</button>
                                        </div>
                                    @else
                                        <button @click="$dispatch('open-modal', 'upload-pic'); reportToUpload = {{ $report->id }}" ...>Upload</button>
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
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="$dispatch('open-modal', 'edit-report'); reportToEdit = {{ $report->toJson() }}" class="p-2 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-lg transition-colors border border-slate-100 bg-slate-50" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        <button @click="$dispatch('open-modal', 'delete-report'); reportToDelete = {{ $report->id }}" class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors border border-slate-100 bg-slate-50" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4 text-slate-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <p class="text-slate-500 font-medium">Belum ada kegiatan yang dilaporkan.</p>
                                    <p class="text-sm text-slate-400 mt-1">Klik tombol "Tambah Kegiatan" untuk memulai.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Create Modal -->
        <x-modal name="create-report" focusable>
            <form method="post" action="{{ route('anggota.reports.store') }}" class="p-6">
                @csrf
                <h2 class="text-lg font-bold text-slate-800 mb-6 border-b pb-3">
                    Tambah Kegiatan Baru
                </h2>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="tanggal" value="Tanggal" />
                            <x-text-input id="tanggal" name="tanggal" type="date" class="mt-1 block w-full" :value="old('tanggal')" required />
                            <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="deadline" value="Deadline" />
                            <x-text-input id="deadline" name="deadline" type="date" class="mt-1 block w-full" :value="old('deadline')" required />
                            <x-input-error :messages="$errors->get('deadline')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="nama_kegiatan" value="Nama Kegiatan (What)" />
                        <x-text-input id="nama_kegiatan" name="nama_kegiatan" type="text" class="mt-1 block w-full" :value="old('nama_kegiatan')" required placeholder="Contoh: Sosialisasi UMKM" />
                        <x-input-error :messages="$errors->get('nama_kegiatan')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="person_in_charge" value="PIC (Person In Charge)" />
                        <x-text-input id="person_in_charge" name="person_in_charge" type="text" class="mt-1 block w-full" :value="old('person_in_charge')" placeholder="Nama penanggung jawab kegiatan" />
                        <x-input-error :messages="$errors->get('person_in_charge')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="To Do">To Do</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Done">Done</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="notes" value="Notes / Keterangan" />
                        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Batal
                    </x-secondary-button>
                    <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                        Simpan
                    </x-primary-button>
                </div>
            </form>
        </x-modal>

        <!-- Edit Modal -->
        <x-modal name="edit-report" focusable>
            <form method="post" :action="reportToEdit ? '{{ url('anggota/reports') }}/' + reportToEdit.id : '#'" class="p-6">
                @csrf
                @method('PUT')
                <h2 class="text-lg font-bold text-slate-800 mb-6 border-b pb-3">
                    Edit Kegiatan
                </h2>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="edit_tanggal" value="Tanggal" />
                            <x-text-input id="edit_tanggal" name="tanggal" type="date" class="mt-1 block w-full" x-bind:value="reportToEdit ? reportToEdit.tanggal.split('T')[0] : ''" required />
                        </div>
                        <div>
                            <x-input-label for="edit_deadline" value="Deadline" />
                            <x-text-input id="edit_deadline" name="deadline" type="date" class="mt-1 block w-full" x-bind:value="reportToEdit ? reportToEdit.deadline.split('T')[0] : ''" required />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="edit_nama_kegiatan" value="Nama Kegiatan (What)" />
                        <x-text-input id="edit_nama_kegiatan" name="nama_kegiatan" type="text" class="mt-1 block w-full" x-bind:value="reportToEdit ? reportToEdit.nama_kegiatan : ''" required />
                    </div>

                    <div>
                        <x-input-label for="edit_person_in_charge" value="PIC (Person In Charge)" />
                        <x-text-input id="edit_person_in_charge" name="person_in_charge" type="text" class="mt-1 block w-full" x-bind:value="reportToEdit ? reportToEdit.person_in_charge : ''" placeholder="Nama penanggung jawab kegiatan" />
                        <x-input-error :messages="$errors->get('person_in_charge')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="edit_status" value="Status" />
                        <select id="edit_status" name="status" class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" x-bind:value="reportToEdit ? reportToEdit.status : ''" required>
                            <option value="To Do">To Do</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Done">Done</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="edit_notes" value="Notes / Keterangan" />
                        <textarea id="edit_notes" name="notes" rows="3" class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" x-bind:value="reportToEdit ? reportToEdit.notes : ''"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Batal
                    </x-secondary-button>
                    <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                        Update
                    </x-primary-button>
                </div>
            </form>
        </x-modal>

        {{-- [NONAKTIF] Upload Pic Modal - dinonaktifkan, jangan hapus
        <x-modal name="upload-pic" focusable>
            <form method="post" :action="reportToUpload ? '{{ url('anggota/reports') }}/' + reportToUpload + '/upload-pic' : '#'" enctype="multipart/form-data" class="p-6">
                @csrf
                <h2 class="text-lg font-bold text-slate-800 mb-6 border-b pb-3">
                    Upload Dokumentasi (Gambar)
                </h2>

                <div class="space-y-4">
                    <div>
                        <x-input-label for="upload_pic" value="Pilih Gambar Dokumentasi" />
                        <input id="upload_pic" name="pic" type="file" accept="image/*" class="mt-2 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required />
                        <p class="text-xs text-slate-400 mt-1">Format: JPEG, PNG, JPG, WEBP. Maksimal ukuran: 2MB.</p>
                        <x-input-error :messages="$errors->get('pic')" class="mt-2" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Batal
                    </x-secondary-button>
                    <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                        Upload
                    </x-primary-button>
                </div>
            </form>
        </x-modal>
        --}}

        <!-- Delete Modal -->
        <x-modal name="delete-report" focusable>
            <form method="post" :action="reportToDelete ? '{{ url('anggota/reports') }}/' + reportToDelete : '#'" class="p-6">
                @csrf
                @method('DELETE')
                <div class="flex items-center gap-4 text-red-600 mb-6">
                    <div class="p-3 bg-red-100 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800">
                        Hapus Laporan Kegiatan
                    </h2>
                </div>
                
                <p class="text-sm text-slate-600 mb-6">
                    Apakah Anda yakin ingin menghapus laporan kegiatan ini? Data yang dihapus tidak dapat dikembalikan.
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Batal
                    </x-secondary-button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Hapus
                    </button>
                </div>
            </form>
        </x-modal>
    </div>
</x-app-layout>
