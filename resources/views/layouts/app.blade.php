<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Absensi KKN Sirnaraja</title>

        <!-- Google Fonts: Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-[#f8fafc] text-slate-800" x-data="{}">
        <!-- Toast Notification (Top Right, Auto Disappear 3 Seconds) -->
        <div x-data="{ 
                show: false, 
                message: '', 
                type: 'success',
                init() {
                    window.addEventListener('show-toast', (e) => {
                        this.message = e.detail.message;
                        this.type = e.detail.type || 'success';
                        this.show = true;
                        setTimeout(() => this.show = false, 3000);
                    });
                    @if(session('success'))
                        this.message = '{{ session('success') }}';
                        this.type = 'success';
                        this.show = true;
                        setTimeout(() => this.show = false, 3000);
                    @endif
                    @if(session('error'))
                        this.message = '{{ session('error') }}';
                        this.type = 'error';
                        this.show = true;
                        setTimeout(() => this.show = false, 3000);
                    @endif
                }
            }" 
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed top-5 right-5 z-50 max-w-sm w-full bg-white shadow-2xl rounded-2xl border border-slate-100 p-4 flex items-center gap-3"
            style="display: none;">
            
            <div class="flex-shrink-0">
                <template x-if="type === 'success'">
                    <span class="inline-flex items-center justify-center p-2 bg-emerald-100 text-emerald-700 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </span>
                </template>
                <template x-if="type === 'error'">
                    <span class="inline-flex items-center justify-center p-2 bg-red-100 text-red-700 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </span>
                </template>
            </div>
            <div class="flex-1 text-sm font-semibold text-slate-800" x-text="message"></div>
        </div>

        <!-- Google Drive Styled Layout (EXACT COPY OF ATTACHED SCREENSHOT) -->
        <div class="min-h-screen flex flex-col md:flex-row bg-[#f8fafc]">
            
            <!-- Left Sidebar Navigation (Google Drive Look: White bg, clean text, blue/green hover/active states) -->
            <aside class="w-full md:w-64 bg-white flex flex-col flex-shrink-0 border-r border-slate-200">
                <!-- Branding Header -->
                <div class="p-6 flex items-center gap-3 border-b border-slate-100">
                    <img src="{{ asset('logo_sirnaraja.png') }}" class="w-10 h-10 object-contain rounded" alt="Logo KKN">
                    <div>
                        <h1 class="font-bold text-slate-800 text-sm tracking-tight leading-none mb-1">Absensi KKN</h1>
                        <span class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider">Desa Sirnaraja</span>
                    </div>
                </div>

                <!-- Google Drive Styled "Upload New Files" -> "Daftar Wajah Baru" Button -->
                <div class="px-5 pt-6 pb-2">
                    <a href="{{ route('anggota.face.register') }}" class="inline-flex items-center justify-center gap-3 w-full px-6 py-3.5 bg-white border border-slate-200 hover:border-slate-300 text-slate-700 font-semibold text-sm rounded-full transition shadow-sm hover:shadow-md">
                        <span class="text-emerald-500 text-xl font-bold">+</span>
                        Daftar Wajah Baru
                    </a>
                </div>

                <!-- Navigation Items (Exactly Matching the Left Sidebar Items List layout of GDrive) -->
                <nav class="flex-1 px-4 py-4 space-y-1.5">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-4 px-4 py-2.5 rounded-full transition font-medium text-sm {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'hover:bg-slate-100 text-slate-600' }}">
                        <svg class="w-5 h-5 text-slate-500 {{ request()->routeIs('dashboard') ? 'text-emerald-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard Utama
                    </a>

                    @if(Auth::user()->isKoordinator())
                        <div class="pt-4 pb-1.5 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">PENGELOLAAN</div>
                        
                        <a href="{{ route('koordinator.locations.index') }}" class="flex items-center gap-4 px-4 py-2.5 rounded-full transition font-medium text-sm {{ request()->routeIs('koordinator.locations.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'hover:bg-slate-100 text-slate-600' }}">
                            <svg class="w-5 h-5 text-slate-500 {{ request()->routeIs('koordinator.locations.*') ? 'text-emerald-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Titik Lokasi GPS
                        </a>
                        <a href="{{ route('koordinator.schedules.index') }}" class="flex items-center gap-4 px-4 py-2.5 rounded-full transition font-medium text-sm {{ request()->routeIs('koordinator.schedules.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'hover:bg-slate-100 text-slate-600' }}">
                            <svg class="w-5 h-5 text-slate-500 {{ request()->routeIs('koordinator.schedules.*') ? 'text-emerald-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Jadwal Kegiatan
                        </a>
                    @endif

                    @if(Auth::user()->isAnggota() || Auth::user()->isKoordinator())
                        <div class="pt-4 pb-1.5 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">AKTIVITAS MAHASISWA</div>

                        @if(Auth::user()->faceData)
                            <a href="{{ route('anggota.attendance.index') }}" class="flex items-center gap-4 px-4 py-2.5 rounded-full transition font-medium text-sm {{ request()->routeIs('anggota.attendance.index') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'hover:bg-slate-100 text-slate-600' }}">
                                <svg class="w-5 h-5 text-slate-500 {{ request()->routeIs('anggota.attendance.index') ? 'text-emerald-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                Mulai Absen Masuk
                            </a>
                        @endif
                        <a href="{{ route('anggota.attendance.history') }}" class="flex items-center gap-4 px-4 py-2.5 rounded-full transition font-medium text-sm {{ request()->routeIs('anggota.attendance.history') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'hover:bg-slate-100 text-slate-600' }}">
                            <svg class="w-5 h-5 text-slate-500 {{ request()->routeIs('anggota.attendance.history') ? 'text-emerald-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Riwayat Absensi
                        </a>
                    @endif
                </nav>

                <!-- Profile Footer Section -->
                <div class="p-4 border-t border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-extrabold text-sm shadow-sm">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="truncate w-28">
                            <p class="text-xs font-bold text-slate-800 truncate leading-none mb-1">{{ Auth::user()->name }}</p>
                            <span class="text-[9px] text-emerald-600 font-bold uppercase tracking-wider">{{ Auth::user()->role }}</span>
                        </div>
                    </div>
                    
                    <!-- Logout Action -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 hover:bg-slate-150 rounded-xl text-slate-400 hover:text-slate-700 transition" title="Keluar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content Pane -->
            <div class="flex-1 flex flex-col min-w-0 bg-[#f8fafc]">
                <!-- Header / Search Bar area in Google Drive -->
                <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between">
                    <div>
                        @isset($header)
                            {{ $header }}
                        @else
                            <h2 class="font-extrabold text-lg text-slate-800 tracking-tight">Absensi KKN Sirnaraja</h2>
                        @endisset
                    </div>
                    <div class="flex items-center gap-4 text-xs font-bold text-slate-400">
                        <span>Hari ini: {{ Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
                    </div>
                </header>

                <!-- Page Content Section -->
                <main class="flex-1 p-8 overflow-y-auto">
                    {{ $slot }}
                </main>
            </div>

        </div>
    </body>
</html>
