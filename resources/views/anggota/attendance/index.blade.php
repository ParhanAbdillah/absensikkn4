<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-800 tracking-tight">
            {{ __('Absensi Kehadiran KKN') }}
        </h2>
    </x-slot>

    <!-- face-api.js & Leaflet.js (Lokal) -->
    <script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>
    <link rel="stylesheet" href="{{ asset('leaflet.css') }}" />
    <script src="{{ asset('leaflet.js') }}"></script>

    <div class="py-6" x-data="attendanceHandler()">
        <div class="max-w-4xl mx-auto">
            
            <!-- Real-time Clock Card -->
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl shadow-lg p-6 mb-6 text-white flex flex-col md:flex-row items-center justify-between animate-card">
                <div>
                    <h3 class="text-emerald-50 font-medium text-sm mb-1">Waktu Absensi Saat Ini</h3>
                    <div class="text-3xl font-extrabold font-mono tracking-wider shadow-sm" x-text="currentTime"></div>
                </div>
                <div class="mt-4 md:mt-0 text-right">
                    <p class="text-sm font-semibold opacity-90">{{ Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</p>
                    <p class="text-xs opacity-75 mt-1">Pastikan absen sesuai jadwal</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200 animate-card">
                <div class="p-8">
                    
                    @if($locations->isEmpty())
                        <div class="text-center py-12 text-slate-400">
                            <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <p class="text-sm font-semibold">Titik lokasi absensi belum disiapkan oleh Koordinator.</p>
                        </div>
                    @else
                        <!-- Validasi GPS Lokasi (Step 1) -->
                        <div x-show="step === 'location'">
                            <h3 class="text-lg font-bold text-slate-900 mb-2">Validasi Geofence GPS</h3>
                            <p class="text-xs text-slate-500 mb-4">Mendeteksi jarak Anda ke titik koordinat absensi: <strong class="text-emerald-700 font-bold" x-text="selectedTitle"></strong>.</p>
                            
                            <div class="mb-4" x-show="statusMessage">
                                <div :class="statusType === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700'" class="p-3 border rounded-xl text-xs font-bold">
                                    <span x-text="statusMessage"></span>
                                </div>
                            </div>

                            <div id="map-check" class="w-full h-64 rounded-2xl border mb-4" style="min-height: 250px;"></div>

                            <div class="flex justify-between items-center mt-6 gap-2">
                                <button type="button" @click="bypassLocation()" class="px-4 py-2.5 bg-slate-500 hover:bg-slate-600 text-white rounded-xl font-bold text-xs uppercase transition shadow-md shadow-slate-200">
                                    Lanjut Tanpa GPS
                                </button>
                                <button type="button" @click="checkLocation()" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs uppercase transition shadow-md shadow-emerald-200 animate-button" :disabled="loading">
                                    <span x-text="loading ? 'Memeriksa GPS...' : 'Periksa Ulang GPS'"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Verifikasi Wajah (Kamera) -->
                        <div x-show="step === 'face'">
                            <h3 class="text-lg font-bold text-slate-900 mb-1">Verifikasi Wajah</h3>
                            <p class="text-xs text-slate-500 mb-4">Posisikan wajah di dalam lingkaran, lalu tekan tombol di bawah.</p>

                            <div class="flex flex-col gap-4">
                                <!-- Camera - tall portrait -->
                                <div class="relative w-full bg-black rounded-2xl overflow-hidden border-2 border-slate-700 shadow-inner" style="height: 360px;">
                                    <video x-ref="video" autoplay muted playsinline class="w-full h-full object-cover" style="transform: scaleX(-1);"></video>
                                    <!-- Face Overlay -->
                                    <div class="absolute inset-0 z-10 pointer-events-none flex items-center justify-center overflow-hidden">
                                        <div class="border-4 border-dashed border-emerald-400 rounded-[100%] shadow-[0_0_0_9999px_rgba(0,0,0,0.55)] animate-pulse" style="width: 200px; height: 260px;"></div>
                                    </div>
                                    <!-- Corner Guides -->
                                    <div class="absolute top-3 left-3 w-8 h-8 border-t-4 border-l-4 border-emerald-400 rounded-tl-lg"></div>
                                    <div class="absolute top-3 right-3 w-8 h-8 border-t-4 border-r-4 border-emerald-400 rounded-tr-lg"></div>
                                    <div class="absolute bottom-3 left-3 w-8 h-8 border-b-4 border-l-4 border-emerald-400 rounded-bl-lg"></div>
                                    <div class="absolute bottom-3 right-3 w-8 h-8 border-b-4 border-r-4 border-emerald-400 rounded-br-lg"></div>
                                    <!-- Status Message Overlay inside camera -->
                                    <div x-show="statusMessage" class="absolute bottom-0 left-0 right-0 z-20 px-4 py-3">
                                        <div :class="statusType === 'success' ? 'bg-emerald-900/80 text-emerald-200' : 'bg-red-900/80 text-red-200'" class="p-3 rounded-xl text-xs font-bold text-center backdrop-blur-sm">
                                            <span x-text="statusMessage"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Button immediately below camera -->
                                <button type="button" @click="verifyFace()" :disabled="!modelsLoaded || isProcessing"
                                    :class="(modelsLoaded && !isProcessing) ? 'bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-200 active:scale-95' : 'bg-gray-300 cursor-not-allowed text-gray-500'"
                                    class="w-full inline-flex justify-center items-center gap-2 px-4 py-4 rounded-2xl font-bold text-sm uppercase tracking-widest text-white transition animate-button">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span x-text="isProcessing ? 'Mencocokkan Wajah...' : (modelsLoaded ? 'Verifikasi & Absen Masuk' : 'Memuat Model AI...')"></span>
                                </button>

                                <!-- Tips at bottom -->
                                <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                    <p class="text-xs font-bold text-slate-600 mb-2">💡 Tips Verifikasi Wajah:</p>
                                    <ul class="space-y-1 text-xs text-slate-500">
                                        <li>• Pastikan cahaya cukup & wajah tidak tertutup</li>
                                        <li>• Hadap lurus ke kamera, tidak miring</li>
                                        <li>• Jika gagal, coba jauhkan sedikit dari layar</li>
                                    </ul>
                                    <a href="{{ route('anggota.leave.create') }}" class="mt-3 flex items-center gap-2 text-xs font-bold text-amber-600 hover:underline">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        Tidak bisa hadir? Ajukan Izin / Sakit
                                    </a>
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
                step: 'location',
                loading: false,
                modelsLoaded: false,
                isProcessing: false,
                statusMessage: '',
                statusType: 'info',
                selectedLocationId: {{ $locations->first()?->id ?? 'null' }},
                selectedTitle: '{{ $locations->first()?->name ?? '' }}',
                
                // Waktu realtime
                currentTime: '',
                timerInterval: null,

                // GPS
                latitude: null,
                longitude: null,
                map: null,
                marker: null,
                circle: null,
                
                // Video stream
                videoStream: null,

                init() {
                    this.updateTime();
                    this.timerInterval = setInterval(() => this.updateTime(), 1000);
                    
                    if (this.selectedLocationId) {
                        setTimeout(() => {
                            this.initMap();
                        }, 300);
                    }
                },

                updateTime() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString('id-ID', { hour12: false });
                },

                initMap() {
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
                                location_id: this.selectedLocationId,
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

                bypassLocation() {
                    this.latitude = null;
                    this.longitude = null;
                    this.statusMessage = 'Melanjutkan ke verifikasi wajah tanpa GPS...';
                    this.statusType = 'success';
                    
                    setTimeout(() => {
                        this.step = 'face';
                        this.initFaceModule();
                    }, 1000);
                },

                async initFaceModule() {
                    this.statusMessage = 'Memuat modul kecerdasan buatan (face-api)...';
                    this.statusType = 'info';

                    try {
                        await Promise.all([
                            faceapi.nets.ssdMobilenetv1.loadFromUri('/models'),
                            faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                            faceapi.nets.faceRecognitionNet.loadFromUri('/models')
                        ]);

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
                                location_id: this.selectedLocationId,
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
