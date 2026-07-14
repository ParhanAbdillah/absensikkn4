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
            .bg-kkn-green {
                background-color: #15803d; /* Tailwind Green 700 */
            }
            .text-kkn-green {
                color: #15803d;
            }
            .border-kkn-green {
                border-color: #15803d;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-800">
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
            class="fixed top-5 right-5 z-50 max-w-sm w-full bg-white shadow-2xl rounded-xl border border-slate-100 p-4 flex items-center gap-3"
            style="display: none;">
            
            <div class="flex-shrink-0">
                <template x-if="type === 'success'">
                    <span class="inline-flex items-center justify-center p-2 bg-green-100 text-green-700 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </span>
                </template>
                <template x-if="type === 'error'">
                    <span class="inline-flex items-center justify-center p-2 bg-red-100 text-red-700 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </span>
                </template>
            </div>
            <div class="flex-1 text-sm font-semibold text-slate-800" x-text="message"></div>
        </div>

        <!-- Google Drive Styled Layout -->
        <div class="min-h-screen flex flex-col md:flex-row">
            
            <!-- Left Sidebar Navigation -->
            <aside class="w-full md:w-64 bg-emerald-900 text-white flex flex-col flex-shrink-0 shadow-lg">
                <!-- Branding Header -->
                <div class="p-6 flex items-center gap-3 border-b border-emerald-800 bg-emerald-950">
                    <img src="{{ asset('logo_sirnaraja.png') }}" class="w-10 h-10 object-contain rounded" alt="Logo KKN">
                    <div>
                        <h1 class="font-extrabold text-sm tracking-wide">KKN SIRNARAJA</h1>
                        <span class="text-[10px] text-emerald-300 font-semibold uppercase">Absensi Kelompok 4</span>
                    </div>
                </div>

                <!-- Navigation Items -->
                <nav class="flex-1 px-4 py-6 space-y-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium {{ request()->routeIs('dashboard') ? 'bg-emerald-700 text-white shadow' : 'hover:bg-emerald-800 text-emerald-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Dashboard
                    </a>

                    @if(Auth::user()->isKoordinator())
                        <a href="{{ route('koordinator.locations.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium {{ request()->routeIs('koordinator.locations.*') ? 'bg-emerald-700 text-white shadow' : 'hover:bg-emerald-800 text-emerald-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Kelola Lokasi
                        </a>
                        <a href="{{ route('koordinator.schedules.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium {{ request()->routeIs('koordinator.schedules.*') ? 'bg-emerald-700 text-white shadow' : 'hover:bg-emerald-800 text-emerald-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Kelola Jadwal
                        </a>
                    @endif

                    @if(Auth::user()->isAnggota() || Auth::user()->isKoordinator())
                        <a href="{{ route('anggota.face.register') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium {{ request()->routeIs('anggota.face.register') ? 'bg-emerald-700 text-white shadow' : 'hover:bg-emerald-800 text-emerald-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Registrasi Wajah
                        </a>
                        @if(Auth::user()->faceData)
                            <a href="{{ route('anggota.attendance.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium {{ request()->routeIs('anggota.attendance.index') ? 'bg-emerald-700 text-white shadow' : 'hover:bg-emerald-800 text-emerald-100' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                Mulai Absen
                            </a>
                        @endif
                        <a href="{{ route('anggota.attendance.history') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium {{ request()->routeIs('anggota.attendance.history') ? 'bg-emerald-700 text-white shadow' : 'hover:bg-emerald-800 text-emerald-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Riwayat Absen
                        </a>
                    @endif
                </nav>

                <!-- Profile Footer Section -->
                <div class="p-4 border-t border-emerald-800 bg-emerald-950 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-emerald-700 flex items-center justify-center font-bold text-xs">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <div class="truncate w-32">
                            <p class="text-xs font-bold truncate">{{ Auth::user()->name }}</p>
                            <span class="text-[9px] text-emerald-300 font-semibold uppercase">{{ Auth::user()->role }}</span>
                        </div>
                    </div>
                    
                    <!-- Logout Action -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-1 hover:bg-emerald-800 rounded-lg text-emerald-300 hover:text-white" title="Keluar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content Pane -->
            <div class="flex-1 flex flex-col min-w-0 bg-slate-50">
                <!-- Header / Search Bar area in Google Drive -->
                <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between">
                    <div>
                        @isset($header)
                            {{ $header }}
                        @else
                            <h2 class="font-bold text-lg text-slate-800">Absensi KKN Sirnaraja</h2>
                        @endisset
                    </div>
                    <div class="flex items-center gap-4 text-xs font-semibold text-slate-500">
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
