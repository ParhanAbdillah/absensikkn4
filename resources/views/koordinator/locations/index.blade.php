<x-app-layout>
    <div x-data="locationIndex()">
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <h2 class="font-extrabold text-xl text-slate-800 tracking-tight">
                {{ __('Kelola Lokasi Absensi') }}
            </h2>
            <button @click="$dispatch('open-modal', 'modal-tambah-lokasi')" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-widest rounded-xl transition shadow-md shadow-emerald-200">
                + Tambah Lokasi
            </button>
        </div>
    </x-slot>

    <!-- Leaflet.js (Lokal) -->
    <link rel="stylesheet" href="{{ asset('leaflet.css') }}" />
    <script src="{{ asset('leaflet.js') }}"></script>

    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-8">
                    @if($locations->isEmpty())
                        <div class="text-center py-12 text-slate-400">
                            <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Belum ada lokasi absensi yang ditambahkan. Silakan tambah lokasi baru.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-600 text-xs font-bold uppercase tracking-wider">
                                        <th class="px-6 py-4 text-left">Nama Lokasi</th>
                                        <th class="px-6 py-4 text-left">Alamat</th>
                                        <th class="px-6 py-4 text-left">Koordinat (Lat, Lng)</th>
                                        <th class="px-6 py-4 text-left">Radius</th>
                                        <th class="px-6 py-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100 text-sm text-slate-700">
                                    @foreach($locations as $location)
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="px-6 py-4 font-bold text-slate-900">{{ $location->name }}</td>
                                            <td class="px-6 py-4 text-slate-500">{{ $location->address ?? '-' }}</td>
                                            <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $location->latitude }}, {{ $location->longitude }}</td>
                                            <td class="px-6 py-4">
                                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-semibold rounded-full text-xs">
                                                    {{ $location->radius_meters }} meter
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <button @click="openEditModal({{ json_encode($location) }})" class="text-emerald-600 hover:text-emerald-900 font-bold mr-4 text-xs uppercase tracking-wide">Edit</button>
                                                
                                                <form action="{{ route('koordinator.locations.destroy', $location) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lokasi ini?')">
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

        <!-- Modal Tambah Lokasi -->
        <x-modal name="modal-tambah-lokasi" :show="false" focusable>
            <form action="{{ route('koordinator.locations.store') }}" method="POST" class="p-6" x-data="mapPicker()" x-init="initMap('map-tambah')">
                @csrf
                <h3 class="text-lg font-bold text-slate-900 mb-4">Tambah Lokasi Baru</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="mb-4">
                            <x-input-label for="name" value="Nama Lokasi" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required placeholder="Posko KKN / Balai Desa" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="address" value="Alamat Lengkap" />
                            <textarea id="address" name="address" rows="2" class="border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm block mt-1 w-full" placeholder="Alamat detail..."></textarea>
                        </div>

                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="latitude" value="Latitude" />
                                <x-text-input id="latitude" class="block mt-1 w-full bg-slate-50" type="text" name="latitude" x-model="lat" readonly required />
                            </div>
                            <div>
                                <x-input-label for="longitude" value="Longitude" />
                                <x-text-input id="longitude" class="block mt-1 w-full bg-slate-50" type="text" name="longitude" x-model="lng" readonly required />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="radius_meters" value="Radius Valid (Meter)" />
                            <x-text-input id="radius_meters" class="block mt-1 w-full" type="number" name="radius_meters" x-model="radius" @input="updateCircle()" required min="5" max="500" />
                        </div>
                    </div>

                    <div>
                        <x-input-label value="Peta Penentuan Titik Koordinat" />
                        <div id="map-tambah" class="w-full h-64 rounded-xl border mt-2" style="min-height: 220px;"></div>
                        <button type="button" @click="getCurrentLocation()" class="mt-2 w-full inline-flex justify-center items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs uppercase rounded-xl transition">
                            Deteksi Lokasi Saya
                        </button>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button @click="$dispatch('close')">Batal</x-secondary-button>
                    <x-primary-button class="bg-emerald-600 hover:bg-emerald-700">Simpan Lokasi</x-primary-button>
                </div>
            </form>
        </x-modal>

        <!-- Modal Edit Lokasi -->
        <x-modal name="modal-edit-lokasi" :show="false" focusable>
            <form :action="'/koordinator/locations/' + editData.id" method="POST" class="p-6" x-data="mapPickerEdit()" x-init="$watch('editData', value => updateEditData(value))">
                @csrf
                @method('PUT')
                <h3 class="text-lg font-bold text-slate-900 mb-4">Edit Lokasi Absensi</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="mb-4">
                            <x-input-label for="edit_name" value="Nama Lokasi" />
                            <x-text-input id="edit_name" class="block mt-1 w-full" type="text" name="name" x-model="editData.name" required />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="edit_address" value="Alamat Lengkap" />
                            <textarea id="edit_address" name="address" rows="2" class="border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm block mt-1 w-full" x-model="editData.address"></textarea>
                        </div>

                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="edit_latitude" value="Latitude" />
                                <x-text-input id="edit_latitude" class="block mt-1 w-full bg-slate-50" type="text" name="latitude" x-model="lat" readonly required />
                            </div>
                            <div>
                                <x-input-label for="edit_longitude" value="Longitude" />
                                <x-text-input id="edit_longitude" class="block mt-1 w-full bg-slate-50" type="text" name="longitude" x-model="lng" readonly required />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="edit_radius_meters" value="Radius Valid (Meter)" />
                            <x-text-input id="edit_radius_meters" class="block mt-1 w-full" type="number" name="radius_meters" x-model="radius" @input="updateCircle()" required min="5" max="500" />
                        </div>
                    </div>

                    <div>
                        <x-input-label value="Peta Penentuan Titik Koordinat" />
                        <div id="map-edit" class="w-full h-64 rounded-xl border mt-2" style="min-height: 220px;"></div>
                        <button type="button" @click="getCurrentLocation()" class="mt-2 w-full inline-flex justify-center items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs uppercase rounded-xl transition">
                            Deteksi Lokasi Saya
                        </button>
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
        function locationIndex() {
            return {
                editData: { id: '', name: '', address: '', latitude: '', longitude: '', radius_meters: 30 },
                openEditModal(data) {
                    this.editData = { ...data };
                    this.$dispatch('open-modal', 'modal-edit-lokasi');
                }
            }
        }

        function mapPicker() {
            return {
                lat: -6.200000,
                lng: 106.816666,
                radius: 30,
                map: null,
                marker: null,
                circle: null,

                initMap(elementId) {
                    setTimeout(() => {
                        this.map = L.map(elementId).setView([this.lat, this.lng], 16);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.map);
                        this.marker = L.marker([this.lat, this.lng], { draggable: true }).addTo(this.map);
                        this.circle = L.circle([this.lat, this.lng], {
                            color: 'emerald',
                            fillColor: '#10b981',
                            fillOpacity: 0.3,
                            radius: parseInt(this.radius)
                        }).addTo(this.map);

                        this.marker.on('dragend', () => {
                            let pos = this.marker.getLatLng();
                            this.lat = pos.lat.toFixed(8);
                            this.lng = pos.lng.toFixed(8);
                            this.circle.setLatLng(pos);
                        });

                        this.map.on('click', (e) => {
                            this.lat = e.latlng.lat.toFixed(8);
                            this.lng = e.latlng.lng.toFixed(8);
                            this.marker.setLatLng(e.latlng);
                            this.circle.setLatLng(e.latlng);
                        });
                    }, 200);
                },

                updateCircle() {
                    if (this.circle) this.circle.setRadius(parseInt(this.radius) || 30);
                },

                getCurrentLocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition((position) => {
                            this.lat = position.coords.latitude.toFixed(8);
                            this.lng = position.coords.longitude.toFixed(8);
                            let newLatLng = new L.LatLng(this.lat, this.lng);
                            this.marker.setLatLng(newLatLng);
                            this.circle.setLatLng(newLatLng);
                            this.map.setView(newLatLng, 16);
                        });
                    }
                }
            }
        }

        function mapPickerEdit() {
            return {
                lat: 0,
                lng: 0,
                radius: 30,
                map: null,
                marker: null,
                circle: null,

                updateEditData(data) {
                    if (!data || !data.latitude) return;
                    this.lat = parseFloat(data.latitude);
                    this.lng = parseFloat(data.longitude);
                    this.radius = parseInt(data.radius_meters);
                    this.initEditMap();
                },

                initEditMap() {
                    setTimeout(() => {
                        if (this.map) {
                            this.map.remove();
                        }
                        this.map = L.map('map-edit').setView([this.lat, this.lng], 16);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.map);
                        this.marker = L.marker([this.lat, this.lng], { draggable: true }).addTo(this.map);
                        this.circle = L.circle([this.lat, this.lng], {
                            color: 'emerald',
                            fillColor: '#10b981',
                            fillOpacity: 0.3,
                            radius: parseInt(this.radius)
                        }).addTo(this.map);

                        this.marker.on('dragend', () => {
                            let pos = this.marker.getLatLng();
                            this.lat = pos.lat.toFixed(8);
                            this.lng = pos.lng.toFixed(8);
                            this.circle.setLatLng(pos);
                        });

                        this.map.on('click', (e) => {
                            this.lat = e.latlng.lat.toFixed(8);
                            this.lng = e.latlng.lng.toFixed(8);
                            this.marker.setLatLng(e.latlng);
                            this.circle.setLatLng(e.latlng);
                        });
                    }, 200);
                },

                updateCircle() {
                    if (this.circle) this.circle.setRadius(parseInt(this.radius) || 30);
                },

                getCurrentLocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition((position) => {
                            this.lat = position.coords.latitude.toFixed(8);
                            this.lng = position.coords.longitude.toFixed(8);
                            let newLatLng = new L.LatLng(this.lat, this.lng);
                            this.marker.setLatLng(newLatLng);
                            this.circle.setLatLng(newLatLng);
                            this.map.setView(newLatLng, 16);
                        });
                    }
                }
            }
        }
    </script>
</div>
</x-app-layout>

