<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Jadwal Kegiatan KKN') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('koordinator.schedules.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Nama Kegiatan / Judul')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" required placeholder="Contoh: Kerja Bakti / Rapat Posko" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Deskripsi Kegiatan')" />
                            <textarea id="description" name="description" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" placeholder="Detail kegiatan..."></textarea>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="location_id" :value="__('Lokasi Absensi')" />
                            <select id="location_id" name="location_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                <option value="">-- Pilih Lokasi Valid GPS --</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }} ({{ $location->latitude }}, {{ $location->longitude }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <x-input-label for="activity_date" :value="__('Tanggal Kegiatan')" />
                                <x-text-input id="activity_date" class="block mt-1 w-full" type="date" name="activity_date" required />
                            </div>
                            <div>
                                <x-input-label for="start_time" :value="__('Waktu Mulai')" />
                                <x-text-input id="start_time" class="block mt-1 w-full" type="time" name="start_time" required />
                            </div>
                            <div>
                                <x-input-label for="tolerance_time" :value="__('Batas Telat (Toleransi)')" />
                                <x-text-input id="tolerance_time" class="block mt-1 w-full" type="time" name="tolerance_time" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <x-primary-button>
                                {{ __('Simpan Jadwal') }}
                            </x-primary-button>
                            <a href="{{ route('koordinator.schedules.index') }}" class="ml-2 inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring ring-gray-300 transition ease-in-out duration-150">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
