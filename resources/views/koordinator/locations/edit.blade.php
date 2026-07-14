<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Lokasi Absensi') }}
        </h2>
    </x-slot>

    <!-- Leaflet.js -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('koordinator.locations.update', $location) }}" method="POST" x-data="mapPicker({{ $location->latitude }}, {{ $location->longitude }}, {{ $location->radius_meters }})">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="mb-4">
                                    <x-input-label for="name" :value="__('Nama Lokasi')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $location->name)" required />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="address" :value="__('Alamat Lengkap')" />
                                    <textarea id="address" name="address" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" placeholder="Alamat detail lokasi...">{{ old('address', $location->address) }}</textarea>
                                </div>

                                <div class="mb-4 grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="latitude" :value="__('Latitude')" />
                                        <x-text-input id="latitude" class="block mt-1 w-full" type="text" name="latitude" x-model="lat" readonly required />
                                    </div>
                                    <div>
                                        <x-input-label for="longitude" :value="__('Longitude')" />
                                        <x-text-input id="longitude" class="block mt-1 w-full" type="text" name="longitude" x-model="lng" readonly required />
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="radius_meters" :value="__('Radius Valid (Meter)')" />
                                    <x-text-input id="radius_meters" class="block mt-1 w-full" type="number" name="radius_meters" x-model="radius" @input="updateCircle()" required min="5" max="1000" />
                                </div>

                                <div class="mt-6">
                                    <x-primary-button>
                                        {{ __('Perbarui Lokasi') }}
                                    </x-primary-button>
                                    <a href="{{ route('koordinator.locations.index') }}" class="ml-2 inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring ring-gray-300 transition ease-in-out duration-150">
                                        Batal
                                    </a>
                                </div>
                            </div>

                            <div>
                                <x-input-label :value="__('Peta Lokasi')" />
                                <div id="map" class="w-full h-80 rounded border shadow-sm" style="min-height: 380px;"></div>
                                <button type="button" @click="getCurrentLocation()" class="mt-2 w-full inline-flex justify-center items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring ring-gray-300 transition ease-in-out duration-150">
                                    Dapatkan Lokasi Saya Saat Ini
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function mapPicker(initLat, initLng, initRadius) {
            return {
                lat: initLat,
                lng: initLng,
                radius: initRadius,
                map: null,
                marker: null,
                circle: null,

                init() {
                    this.map = L.map('map').setView([this.lat, this.lng], 16);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(this.map);

                    this.marker = L.marker([this.lat, this.lng], { draggable: true }).addTo(this.map);
                    this.circle = L.circle([this.lat, this.lng], {
                        color: 'blue',
                        fillColor: '#30a3ec',
                        fillOpacity: 0.3,
                        radius: parseInt(this.radius)
                    }).addTo(this.map);

                    this.marker.on('dragend', (e) => {
                        let position = this.marker.getLatLng();
                        this.lat = position.lat.toFixed(8);
                        this.lng = position.lng.toFixed(8);
                        this.circle.setLatLng(position);
                    });

                    this.map.on('click', (e) => {
                        let position = e.latlng;
                        this.lat = position.lat.toFixed(8);
                        this.lng = position.lng.toFixed(8);
                        this.marker.setLatLng(position);
                        this.circle.setLatLng(position);
                    });
                },

                updateCircle() {
                    if (this.circle) {
                        this.circle.setRadius(parseInt(this.radius) || 30);
                    }
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
                    } else {
                        alert("Geolocation tidak didukung oleh browser Anda.");
                    }
                }
            }
        }
    </script>
</x-app-layout>
