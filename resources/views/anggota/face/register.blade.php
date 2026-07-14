<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrasi Wajah Kehadiran') }}
        </h2>
    </x-slot>

    <!-- face-api.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" x-data="faceRegister()">
                    
                    @if($faceData)
                        <!-- Tampilan Jika Sudah Registrasi -->
                        <div class="text-center py-6">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Wajah Anda Sudah Terdaftar</h3>
                            <p class="text-sm text-gray-600 mb-6">Wajah Anda saat ini sudah bisa digunakan untuk absensi kehadiran di lokasi KKN.</p>
                            
                            <div class="mb-6 flex justify-center">
                                <img src="{{ Storage::url($faceData->reference_photo) }}" alt="Wajah Referensi" class="w-48 h-48 object-cover rounded-lg border-2 border-green-500 shadow-md">
                            </div>

                            <form action="{{ route('anggota.face.destroy') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data wajah ini? Anda harus melakukan registrasi ulang untuk absen.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-950 focus:outline-none focus:ring ring-red-300 transition ease-in-out duration-150">
                                    Hapus Data Wajah
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Form / Kamera Registrasi Wajah -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2">Petunjuk Pendaftaran:</h3>
                                <ul class="list-decimal pl-5 space-y-2 text-gray-600 text-sm mb-6">
                                    <li>Berada di ruangan dengan pencahayaan yang cukup.</li>
                                    <li>Posisikan wajah Anda tepat di tengah kotak kamera.</li>
                                    <li>Jangan menggunakan kacamata hitam, masker, atau topi.</li>
                                    <li>Hadap lurus ke kamera saat menekan tombol "Ambil Foto Wajah".</li>
                                </ul>

                                <div class="mb-4" x-show="statusMessage">
                                    <div :class="statusType === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-yellow-100 border-yellow-400 text-yellow-700'" class="p-3 border rounded text-sm">
                                        <span x-text="statusMessage"></span>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <button type="button" @click="captureFace()" :disabled="!modelsLoaded || isProcessing" :class="(modelsLoaded && !isProcessing) ? 'bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-100' : 'bg-gray-300 cursor-not-allowed text-gray-500'" class="w-full inline-flex justify-center items-center px-4 py-3.5 border border-transparent rounded-xl font-bold text-xs uppercase tracking-widest text-white transition">
                                        <span x-text="isProcessing ? 'Memproses Wajah...' : (modelsLoaded ? 'Daftarkan Wajah' : 'Memuat Model AI...')"></span>
                                    </button>
                                </div>
                            </div>

                            <div class="flex flex-col items-center justify-center">
                                <div class="relative w-full aspect-video md:w-80 md:h-80 bg-black rounded-lg overflow-hidden border shadow-inner">
                                    <!-- Video Stream -->
                                    <video x-ref="video" autoplay muted playsinline class="w-full h-full object-cover"></video>
                                    
                                    <!-- Overlay Frame -->
                                    <div class="absolute inset-0 border-4 border-dashed border-indigo-500 rounded-lg pointer-events-none opacity-40"></div>
                                </div>
                                <span class="text-xs text-gray-500 mt-2">Pastikan kamera diizinkan di browser Anda.</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(!$faceData)
    <script>
        function faceRegister() {
            return {
                modelsLoaded: false,
                capturing: false,
                isProcessing: false,
                statusMessage: 'Memuat modul pendeteksi wajah...',
                statusType: 'info',
                videoStream: null,
                descriptor: null,
                capturedBase64: null,

                async init() {
                    try {
                        // Load models face-api.js dari local folder public/models
                        await faceapi.nets.ssdMobilenetv1.loadFromUri('/models');
                        await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
                        await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
                        
                        this.modelsLoaded = true;
                        this.statusMessage = 'Model AI siap. Mengaktifkan kamera...';
                        this.statusType = 'success';
                        
                        this.startCamera();
                    } catch (error) {
                        console.error(error);
                        this.statusMessage = 'Gagal memuat model pendeteksi wajah.';
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
                        console.error(error);
                        this.statusMessage = 'Gagal mengakses kamera. Pastikan izin kamera aktif.';
                        this.statusType = 'error';
                    }
                },

                async captureFace() {
                    if (this.isProcessing) return;

                    this.isProcessing = true;
                    this.statusMessage = 'Menganalisis wajah Anda... Tetap diam.';
                    this.statusType = 'info';

                    // Buat canvas dari video frame untuk dianalisis
                    const video = this.$refs.video;
                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    
                    // Simpan base64 foto
                    this.capturedBase64 = canvas.toDataURL('image/jpeg');

                    // Deteksi wajah dan ekstrak descriptor
                    const detection = await faceapi.detectSingleFace(video)
                        .withFaceLandmarks()
                        .withFaceDescriptor();

                    if (!detection) {
                        this.statusMessage = 'Wajah tidak terdeteksi. Silakan posisikan wajah Anda dengan benar dan coba lagi.';
                        this.statusType = 'error';
                        this.isProcessing = false;
                        return;
                    }

                    this.descriptor = JSON.stringify(Array.from(detection.descriptor));
                    this.saveFace();
                },

                async saveFace() {
                    try {
                        const response = await fetch('{{ route("anggota.face.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                descriptor: this.descriptor,
                                image: this.capturedBase64
                            })
                        });

                        const result = await response.json();
                        
                        if (result.success) {
                            this.statusMessage = 'Registrasi wajah berhasil! Mengalihkan ke dashboard...';
                            this.statusType = 'success';
                            
                            // Stop kamera
                            if (this.videoStream) {
                                this.videoStream.getTracks().forEach(track => track.stop());
                            }

                            setTimeout(() => {
                                window.location.href = '{{ route("dashboard") }}';
                            }, 2000);
                        } else {
                            this.statusMessage = result.message || 'Gagal menyimpan wajah.';
                            this.statusType = 'error';
                            this.isProcessing = false;
                        }
                    } catch (error) {
                        console.error(error);
                        this.statusMessage = 'Terjadi kesalahan koneksi saat mengirim data.';
                        this.statusType = 'error';
                        this.isProcessing = false;
                    }
                }
            }
        }
    </script>
    @endif
</x-app-layout>
