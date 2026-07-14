<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Absensi Kehadiran KKN') }}
        </h2>
    </x-slot>

    <!-- face-api.js & Leaflet.js (Lokal) -->
    <script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>
    <link rel="stylesheet" href="{{ asset('leaflet.css') }}" />
    <script src="{{ asset('leaflet.js') }}"></script>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" x-data="attendanceHandler()">
                    
                    @if($schedules->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            Tidak ada jadwal kegiatan yang terdaftar untuk hari ini.
                        </div>
                    @else
                        <!-- Step 1: Pilih Jadwal Kegiatan -->
                        <div x-show="step === 'select'">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Pilih Kegiatan Hari Ini:</h3>
                            <div class="space-y-4">
                                @foreach($schedules as $schedule)
                                    <div class="p-4 border rounded-lg hover:border-indigo-500 cursor-pointer flex justify-between items-center transition"
                                         @click="selectSchedule({{ $schedule->id }}, '{{ $schedule->title }}')">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $schedule->title }}</h4>
                                            <p class="text-xs text-gray-500 mt-1">Lokasi: {{ $schedule->location->name }} (Radius: {{ $schedule->location->radius_meters }}m)</p>
                                            <p class="text-xs text-gray-500">Waktu Mulai: {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} WIB</p>
                                        </div>
                                        <span class="text-indigo-600 font-medium text-sm">Pilih &rarr;</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Step 2: Validasi GPS Lokasi -->
                        <div x-show="step === 'location'">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Validasi Lokasi GPS</h3>
                            <p class="text-sm text-gray-600 mb-4">Sistem sedang memeriksa apakah Anda berada dalam radius 30 meter dari lokasi kegiatan: <strong x-text="selectedTitle"></strong>.</p>
                            
                            <div class="mb-4" x-show="statusMessage">
                                <div :class="statusType === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'" class="p-3 border rounded text-sm">
                                    <span x-text="statusMessage"></span>
                                </div>
                            </div>

                            <div id="map-check" class="w-full h-64 rounded border mb-4" style="min-height: 250px;"></div>

                            <div class="flex justify-between items-center mt-6">
                                <button type="button" @click="step = 'select'" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md font-semibold text-xs uppercase hover:bg-gray-300">Kembali</button>
                                <button type="button" @click="checkLocation()" class="px-4 py-2 bg-indigo-600 text-white rounded-md font-semibold text-xs uppercase hover:bg-indigo-700" :disabled="loading">
                                    <span x-show="loading">Memeriksa GPS...</span>
                                    <span x-show="!loading">Periksa Ulang GPS</span>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Verifikasi Wajah (Kamera) -->
                        <div x-show="step === 'face'">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Verifikasi Wajah (Face Recognition)</h3>
                            <p class="text-sm text-gray-600 mb-4">Silakan hadap lurus ke kamera depan untuk mencocokkan wajah Anda.</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <div class="mb-4" x-show="statusMessage">
                                        <div :class="statusType === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'" class="p-3 border rounded text-sm">
                                            <span x-text="statusMessage"></span>
                                        </div>
                                    </div>

                                    <button type="button" @click="verifyFace()" :disabled="!modelsLoaded || isProcessing" :class="(modelsLoaded && !isProcessing) ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed'" class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition">
                                        <span x-show="isProcessing">Mencocokkan Wajah...</span>
                                        <span x-show="!isProcessing && !modelsLoaded">Memuat Model AI...</span>
                                        <span x-show="!isProcessing && modelsLoaded">Verifikasi & Absen</span>
                                    </button>
                                </div>

                                <div class="flex flex-col items-center">
                                    <div class="relative w-full aspect-video md:w-72 md:h-72 bg-black rounded-lg overflow-hidden border">
                                        <video x-ref="video" autoplay muted playsinline class="w-full h-full object-cover"></video>
                                        <div class="absolute inset-0 border-4 border-dashed border-indigo-500 rounded-lg pointer-events-none opacity-40"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function attendanceHandler() {
            return {
                step: 'select',
                loading: false,
                modelsLoaded: false,
                isProcessing: false,
                statusMessage: '',
                statusType: 'info',
                selectedScheduleId: null,
                selectedTitle: '',
                
                // GPS
                latitude: null,
                longitude: null,
                map: null,
                marker: null,
                circle: null,
                
                // Video stream
                videoStream: null,

                selectSchedule(id, title) {
                    this.selectedScheduleId = id;
                    this.selectedTitle = title;
                    this.step = 'location';
                    
                    // Trigger map initialization
                    setTimeout(() => {
                        this.initMap();
                    }, 300);
                },

                initMap() {
                    // Default center coordinates
                    const defaultLat = -6.200000;
                    const defaultLng = 106.816666;

                    if (!this.map) {
                        this.map = L.map('map-check').setView([defaultLat, defaultLng], 16);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19
                        }).addTo(this.map);
                    }

                    this.checkLocation();
                },

                checkLocation() {
                    this.loading = true;
                    this.statusMessage = 'Mengakses koordinat GPS Anda...';
                    this.statusType = 'info';

                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                this.latitude = position.coords.latitude;
                                this.longitude = position.coords.longitude;
                                
                                // Tandai lokasi di map
                                const latLng = new L.LatLng(this.latitude, this.longitude);
                                if (this.marker) {
                                    this.marker.setLatLng(latLng);
                                } else {
                                    this.marker = L.marker(latLng).addTo(this.map);
                                }
                                this.map.setView(latLng, 16);

                                // Hitung ke server
                                this.verifyLocationOnServer();
                            },
                            () => {
                                this.loading = false;
                                this.statusMessage = 'Gagal mengambil lokasi GPS. Izinkan lokasi di browser.';
                                this.statusType = 'error';
                            },
                            { enableHighAccuracy: true }
                        );
                    } else {
                        this.loading = false;
                        this.statusMessage = 'Browser Anda tidak mendukung Geolocation.';
                        this.statusType = 'error';
                    }
                },

                async verifyLocationOnServer() {
                    try {
                        const response = await fetch('{{ route("anggota.attendance.check-location") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                schedule_id: this.selectedScheduleId,
                                latitude: this.latitude,
                                longitude: this.longitude
                            })
                        });

                        const result = await response.json();
                        this.loading = false;

                        if (result.success && result.is_valid) {
                            this.statusMessage = `Lokasi valid! Anda berada di dalam radius absensi (${result.distance} meter). Silakan lanjut ke verifikasi wajah.`;
                            this.statusType = 'success';
                            
                            setTimeout(() => {
                                this.step = 'face';
                                this.initFaceModule();
                            }, 1500);
                        } else {
                            this.statusMessage = `Gagal! Anda berada di luar radius lokasi absensi (${result.distance} meter dari target ${result.radius} meter).`;
                            this.statusType = 'error';
                        }
                    } catch (error) {
                        this.loading = false;
                        this.statusMessage = 'Kesalahan koneksi saat validasi lokasi.';
                        this.statusType = 'error';
                    }
                },

                async initFaceModule() {
                    this.statusMessage = 'Memuat modul kecerdasan buatan (face-api)...';
                    this.statusType = 'info';

                    try {
                        await faceapi.nets.ssdMobilenetv1.loadFromUri('/models');
                        await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
                        await faceapi.nets.faceRecognitionNet.loadFromUri('/models');

                        this.modelsLoaded = true;
                        this.statusMessage = 'Model AI siap. Membuka kamera depan...';
                        this.statusType = 'success';

                        this.startCamera();
                    } catch (error) {
                        this.statusMessage = 'Gagal mengunduh modul kecerdasan buatan.';
                        this.statusType = 'error';
                    }
                },

                async startCamera() {
                    try {
                        this.videoStream = await navigator.mediaDevices.getUserMedia({
                            video: { width: 640, height: 480, facingMode: 'user' }
                        });
                        this.$refs.video.srcObject = this.videoStream;
                    } catch (error) {
                        this.statusMessage = 'Kamera depan tidak dapat diakses.';
                        this.statusType = 'error';
                    }
                },

                async verifyFace() {
                    if (this.isProcessing) return;
                    this.isProcessing = true;
                    this.statusMessage = 'Memotret dan mendeteksi wajah Anda... Tetap tenang.';
                    this.statusType = 'info';

                    const video = this.$refs.video;
                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    const base64Image = canvas.toDataURL('image/jpeg');

                    // Deteksi wajah
                    const detection = await faceapi.detectSingleFace(video)
                        .withFaceLandmarks()
                        .withFaceDescriptor();

                    if (!detection) {
                        this.statusMessage = 'Wajah tidak terdeteksi. Silakan coba kembali di tempat terang.';
                        this.statusType = 'error';
                        this.isProcessing = false;
                        return;
                    }

                    // Kirim ke server
                    this.submitAttendance(JSON.stringify(Array.from(detection.descriptor)), base64Image);
                },

                async submitAttendance(descriptor, image) {
                    try {
                        const response = await fetch('{{ route("anggota.attendance.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                schedule_id: this.selectedScheduleId,
                                latitude: this.latitude,
                                longitude: this.longitude,
                                descriptor: descriptor,
                                image: image
                            })
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.statusMessage = result.message;
                            this.statusType = 'success';

                            if (this.videoStream) {
                                this.videoStream.getTracks().forEach(track => track.stop());
                            }

                            setTimeout(() => {
                                window.location.href = '{{ route("dashboard") }}';
                            }, 2000);
                        } else {
                            this.statusMessage = result.message;
                            this.statusType = 'error';
                            this.isProcessing = false;
                        }
                    } catch (error) {
                        this.statusMessage = 'Kesalahan koneksi saat melakukan absensi.';
                        this.statusType = 'error';
                        this.isProcessing = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
