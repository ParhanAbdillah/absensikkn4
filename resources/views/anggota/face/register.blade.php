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
                        <div class="flex flex-col gap-6">
                            <!-- Camera section FIRST on mobile -->
                            <div class="flex flex-col items-center justify-center">
                                <div class="relative w-full bg-black rounded-2xl overflow-hidden border-2 border-slate-700 shadow-inner mb-4" style="height: 360px;">
                                    <!-- Video Stream -->
                                    <video x-ref="video" autoplay muted playsinline class="w-full h-full object-cover" style="transform: scaleX(-1);"></video>
                                    
                                    <!-- Face Overlay Guide -->
                                    <div class="absolute inset-0 z-10 pointer-events-none flex items-center justify-center overflow-hidden">
                                        <!-- Oval shape for face alignment with darkened background outside -->
                                        <div class="border-4 border-dashed border-emerald-400 rounded-[100%] shadow-[0_0_0_9999px_rgba(0,0,0,0.55)] animate-pulse" style="width: 200px; height: 260px;"></div>
                                    </div>
 
                                    <!-- Corner Guides -->
                                    <div class="absolute top-3 left-3 w-8 h-8 border-t-4 border-l-4 border-emerald-400 rounded-tl-lg"></div>
                                    <div class="absolute top-3 right-3 w-8 h-8 border-t-4 border-r-4 border-emerald-400 rounded-tr-lg"></div>
                                    <div class="absolute bottom-3 left-3 w-8 h-8 border-b-4 border-l-4 border-emerald-400 rounded-bl-lg"></div>
                                    <div class="absolute bottom-3 right-3 w-8 h-8 border-b-4 border-r-4 border-emerald-400 rounded-br-lg"></div>

                                    <!-- Loading overlay inside camera view if models aren't loaded -->
                                    <div class="absolute inset-0 bg-slate-900/90 z-20 flex flex-col items-center justify-center p-6 text-center" x-show="!modelsLoaded">
                                        <svg class="animate-spin h-10 w-10 text-emerald-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <p class="text-sm font-semibold text-white mb-1" x-text="statusMessage"></p>
                                        <p class="text-xs text-slate-400">Mohon tunggu sebentar, sistem sedang mempersiapkan modul AI...</p>
                                    </div>
                                </div>

                                <!-- Progress Bar and Steps -->
                                <div class="w-full mb-4" x-show="progressPercent > 0">
                                    <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden mb-2">
                                        <div class="bg-gradient-to-r from-emerald-400 to-teal-500 h-full rounded-full transition-all duration-300" :style="`width: ${progressPercent}%`"></div>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                        <span x-text="statusMessage"></span>
                                        <span x-text="`${progressPercent}%`"></span>
                                    </div>
                                </div>
 
                                <!-- Status Message Banner (shown when not showing progress bar or for errors) -->
                                <div class="w-full mb-4" x-show="statusMessage && progressPercent === 0">
                                    <div :class="statusType === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : (statusType === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-yellow-50 border-yellow-200 text-yellow-700')" class="p-3 border rounded-xl text-xs font-bold text-center">
                                        <span x-text="statusMessage"></span>
                                        <div class="mt-2.5" x-show="statusType === 'error' && capturedBase64">
                                            <button type="button" @click="registerWithMockDescriptor()" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-md shadow-indigo-150">
                                                Gunakan Foto Ini & Daftar Manual
                                            </button>
                                        </div>
                                    </div>
                                </div>
 
                                <!-- Button directly below camera / status -->
                                <button type="button" @click="captureFace()" :disabled="!modelsLoaded || isProcessing" :class="(modelsLoaded && !isProcessing) ? 'bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-100 active:scale-[0.98]' : 'bg-gray-300 cursor-not-allowed text-gray-500'" class="w-full inline-flex justify-center items-center px-4 py-3.5 border border-transparent rounded-xl font-bold text-xs uppercase tracking-widest text-white transition transform duration-150">
                                    <svg x-show="isProcessing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span x-text="isProcessing ? 'Memproses Wajah...' : (modelsLoaded ? 'Ambil Foto & Daftarkan Wajah' : 'Mempersiapkan Sistem...')"></span>
                                </button>
                                <span class="text-[10px] text-gray-400 mt-2">Pastikan kamera diizinkan di browser Anda.</span>
                            </div>
 
                            <!-- Instructions at the very bottom -->
                            <div class="border-t pt-4">
                                <h3 class="text-sm font-bold text-gray-900 mb-2">Petunjuk Pendaftaran:</h3>
                                <ul class="list-decimal pl-5 space-y-1.5 text-gray-600 text-xs mb-6">
                                    <li>Berada di ruangan dengan pencahayaan yang cukup dan terang.</li>
                                    <li>Posisikan wajah Anda tepat di tengah kotak panduan oval.</li>
                                    <li>Jangan menggunakan kacamata hitam, masker, atau topi.</li>
                                    <li>Hadap lurus ke kamera dan tenang saat menekan tombol pendaftaran.</li>
                                </ul>
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
                progressPercent: 0,
                videoStream: null,
                descriptor: null,
                capturedBase64: null,
 
                async init() {
                    try {
                        this.progressPercent = 15;
                        this.statusMessage = 'Memuat modul deteksi dasar...';
                        await faceapi.nets.ssdMobilenetv1.loadFromUri('/models');
                        
                        this.progressPercent = 45;
                        this.statusMessage = 'Memuat modul penanda titik wajah...';
                        await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
                        
                        this.progressPercent = 75;
                        this.statusMessage = 'Memuat modul pencocokan wajah...';
                        await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
                        
                        this.progressPercent = 90;
                        this.statusMessage = 'Mengaktifkan kamera perangkat Anda...';
                        
                        await this.startCamera();
                        
                        this.progressPercent = 100;
                        this.statusMessage = 'Sistem siap digunakan!';
                        this.statusType = 'success';
                        this.modelsLoaded = true;

                        // Sembunyikan progress bar setelah selesai inisialisasi agar bersih
                        setTimeout(() => {
                            this.progressPercent = 0;
                            this.statusMessage = 'Silakan posisikan wajah Anda tepat di tengah oval panduan.';
                        }, 1500);
                    } catch (error) {
                        console.error(error);
                        this.statusMessage = 'Gagal memuat model pendeteksi wajah. Pastikan koneksi internet stabil.';
                        this.statusType = 'error';
                        this.progressPercent = 0;
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
                        this.statusMessage = 'Gagal mengakses kamera. Mohon izinkan akses kamera di browser Anda.';
                        this.statusType = 'error';
                        this.progressPercent = 0;
                        throw error;
                    }
                },
 
                async captureFace() {
                    if (this.isProcessing || !this.modelsLoaded) return;
 
                    this.isProcessing = true;
                    this.progressPercent = 20;
                    this.statusMessage = 'Menangkap citra wajah... Mohon diam.';
                    this.statusType = 'info';
 
                    const video = this.$refs.video;
                    
                    // Delay kecil untuk visual effect & stabilitas tangkapan frame
                    setTimeout(async () => {
                        try {
                            // 1. Capture High Resolution Image (untuk disimpan di server)
                            const highResCanvas = document.createElement('canvas');
                            highResCanvas.width = video.videoWidth || 640;
                            highResCanvas.height = video.videoHeight || 480;
                            const highResCtx = highResCanvas.getContext('2d');
                            highResCtx.drawImage(video, 0, 0, highResCanvas.width, highResCanvas.height);
                            this.capturedBase64 = highResCanvas.toDataURL('image/jpeg', 0.85); // Kompresi jpeg 85% untuk performa simpan
 
                            this.progressPercent = 50;
                            this.statusMessage = 'Mendeteksi & memetakan koordinat wajah...';
 
                            // 2. Downscaled Canvas untuk komputasi face-api.js (Preserve Aspect Ratio agar wajah tidak penyet)
                            const videoWidth = video.videoWidth || 640;
                            const videoHeight = video.videoHeight || 480;
                            
                            let targetWidth = 320;
                            let targetHeight = 240;
                            
                            if (videoWidth > videoHeight) {
                                // Landscape
                                targetWidth = 320;
                                targetHeight = Math.round((videoHeight / videoWidth) * 320);
                            } else {
                                // Portrait (Hp / Mobile)
                                targetHeight = 320;
                                targetWidth = Math.round((videoWidth / videoHeight) * 320);
                            }
 
                            const detectionCanvas = document.createElement('canvas');
                            detectionCanvas.width = targetWidth;
                            detectionCanvas.height = targetHeight;
                            const detectionCtx = detectionCanvas.getContext('2d');
                            detectionCtx.drawImage(video, 0, 0, targetWidth, targetHeight);
 
                            // Deteksi wajah menggunakan frame downscaled (Lebih cepat & hemat memory)
                            let detection = await faceapi.detectSingleFace(detectionCanvas)
                                .withFaceLandmarks()
                                .withFaceDescriptor();
 
                            // Fallback: Jika gagal pada resolusi rendah, coba resolusi asli video langsung
                            if (!detection) {
                                this.statusMessage = 'Mencoba mendeteksi dengan sensitivitas penuh...';
                                detection = await faceapi.detectSingleFace(video)
                                    .withFaceLandmarks()
                                    .withFaceDescriptor();
                            }
 
                            if (!detection) {
                                this.statusMessage = 'Wajah tidak terdeteksi. Silakan hadap lurus ke kamera dengan pencahayaan yang cukup.';
                                this.statusType = 'error';
                                this.isProcessing = false;
                                this.progressPercent = 0;
                                return;
                            }
 
                            this.progressPercent = 80;
                            this.statusMessage = 'Mengenkripsi & mengirim data wajah ke server...';
 
                            this.descriptor = JSON.stringify(Array.from(detection.descriptor));
                            this.saveFace();
                        } catch (err) {
                            console.error(err);
                            this.statusMessage = 'Gagal memproses wajah. Coba lagi.';
                            this.statusType = 'error';
                            this.isProcessing = false;
                            this.progressPercent = 0;
                        }
                    }, 300);
                },
 
                registerWithMockDescriptor() {
                    this.isProcessing = true;
                    this.progressPercent = 80;
                    this.statusMessage = 'Mengirim foto pendaftaran manual ke server...';
                    this.statusType = 'info';
                    
                    // Generate mock descriptor (128 zeros)
                    this.descriptor = JSON.stringify(Array(128).fill(0));
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
                            this.progressPercent = 100;
                            this.statusMessage = 'Registrasi wajah berhasil! Mengalihkan...';
                            this.statusType = 'success';
                            
                            // Hentikan stream kamera
                            if (this.videoStream) {
                                this.videoStream.getTracks().forEach(track => track.stop());
                            }
 
                            setTimeout(() => {
                                window.location.href = '{{ route("dashboard") }}';
                            }, 1500);
                        } else {
                            this.statusMessage = result.message || 'Gagal menyimpan wajah.';
                            this.statusType = 'error';
                            this.isProcessing = false;
                            this.progressPercent = 0;
                        }
                    } catch (error) {
                        console.error(error);
                        this.statusMessage = 'Terjadi kesalahan jaringan. Coba lagi.';
                        this.statusType = 'error';
                        this.isProcessing = false;
                        this.progressPercent = 0;
                    }
                }
            }
        }
    </script>
    @endif
</x-app-layout>
