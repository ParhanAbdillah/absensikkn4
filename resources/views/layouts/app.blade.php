<!DOCTYPE html>
<html lang="id" class="h-full overflow-hidden">

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

        .animate-card {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .animate-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.08);
        }

        .animate-button {
            transition: all 0.2s ease-in-out;
        }

        .animate-button:active {
            transform: scale(0.95);
        }
    </style>
</head>

<body class="font-sans antialiased bg-[#f8fafc] text-slate-800 h-full overflow-hidden" x-data="{}">
    @php
        $warningReportsCount = 0;
        if (Auth::check() && (Auth::user()->isKoordinator() || Auth::user()->isSekretaris())) {
            $warningReportsCount = \App\Models\ActivityReport::where('status', '!=', 'Done')
                ->whereDate('deadline', '<=', now()->addDays(3))
                ->count();
        }
    @endphp
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
    }" x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-2"
        x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed top-5 right-5 z-50 max-w-sm w-full bg-white shadow-2xl rounded-2xl border border-slate-100 p-4 flex items-center gap-3"
        style="display: none;">

        <div class="flex-shrink-0">
            <template x-if="type === 'success'">
                <span class="inline-flex items-center justify-center p-2 bg-emerald-100 text-emerald-700 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </span>
            </template>
            <template x-if="type === 'error'">
                <span class="inline-flex items-center justify-center p-2 bg-red-100 text-red-700 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </span>
            </template>
        </div>
        <div class="flex-1 text-sm font-semibold text-slate-800" x-text="message"></div>
    </div>

    <!-- Google Drive Styled Layout (EXACT COPY OF ATTACHED SCREENSHOT) -->
    <div class="h-full w-full flex flex-col md:flex-row bg-[#f8fafc] overflow-hidden">

        <!-- Left Sidebar Navigation (Google Drive Look: White bg, clean text, blue/green hover/active states) -->
        <aside
            class="hidden md:flex w-full md:w-64 h-full bg-white flex-col flex-shrink-0 border-r border-slate-200 overflow-y-auto">
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
                <a href="{{ route('anggota.face.register') }}"
                    class="inline-flex items-center justify-center gap-3 w-full px-6 py-3.5 bg-white border border-slate-200 hover:border-slate-300 text-slate-700 font-semibold text-sm rounded-full transition shadow-sm hover:shadow-md">
                    <span class="text-emerald-500 text-xl font-bold">+</span>
                    Daftar Wajah Baru
                </a>
            </div>

            <!-- Navigation Items (Exactly Matching the Left Sidebar Items List layout of GDrive) -->
            <nav class="flex-1 px-4 py-4 space-y-1.5">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-4 px-4 py-2.5 rounded-full transition font-medium text-sm {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'hover:bg-slate-100 text-slate-600' }}">
                    <svg class="w-5 h-5 text-slate-500 {{ request()->routeIs('dashboard') ? 'text-emerald-600' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    Dashboard Utama
                </a>

                @if (Auth::user()->isKoordinator() || Auth::user()->isSekretaris() || Auth::user()->isDpl())
                    <div class="pt-4 pb-1.5 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        PENGELOLAAN</div>

                    @if (Auth::user()->isSekretaris())
                        <a href="{{ route('koordinator.users.index') }}"
                            class="flex items-center gap-4 px-4 py-2.5 rounded-full transition font-medium text-sm {{ request()->routeIs('koordinator.users.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'hover:bg-slate-100 text-slate-600' }}">
                            <svg class="w-5 h-5 text-slate-500 {{ request()->routeIs('koordinator.users.*') ? 'text-emerald-600' : '' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                            Kelola Anggota
                        </a>

                        <a href="{{ route('koordinator.locations.index') }}"
                            class="flex items-center gap-4 px-4 py-2.5 rounded-full transition font-medium text-sm {{ request()->routeIs('koordinator.locations.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'hover:bg-slate-100 text-slate-600' }}">
                            <svg class="w-5 h-5 text-slate-500 {{ request()->routeIs('koordinator.locations.*') ? 'text-emerald-600' : '' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Titik Lokasi GPS
                        </a>
                    @endif

                    <a href="{{ route('koordinator.attendance.rekap') }}"
                        class="flex items-center gap-4 px-4 py-2.5 rounded-full transition font-medium text-sm {{ request()->routeIs('koordinator.attendance.rekap') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'hover:bg-slate-100 text-slate-600' }}">
                        <svg class="w-5 h-5 text-slate-500 {{ request()->routeIs('koordinator.attendance.rekap') ? 'text-emerald-600' : '' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Rekap Absensi
                    </a>
                    <a href="{{ route('koordinator.reports.index') }}"
                        class="flex items-center justify-between px-4 py-2.5 rounded-full transition font-medium text-sm {{ request()->routeIs('koordinator.reports.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'hover:bg-slate-100 text-slate-600' }}">
                        <div class="flex items-center gap-4">
                            <svg class="w-5 h-5 text-slate-500 {{ request()->routeIs('koordinator.reports.*') ? 'text-emerald-600' : '' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <span>Rekap Laporan</span>
                        </div>
                        @if ($warningReportsCount > 0)
                            <span
                                class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-[0_0_8px_rgba(239,68,68,0.8)] animate-pulse">{{ $warningReportsCount }}</span>
                        @endif
                    </a>
                @endif

                @if (Auth::user()->isAnggota() || Auth::user()->isKoordinator())
                    <div class="pt-4 pb-1.5 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        AKTIVITAS MAHASISWA</div>

                    @if (Auth::user()->faceData)
                        <a href="{{ route('anggota.attendance.index') }}"
                            class="flex items-center gap-4 px-4 py-2.5 rounded-full transition font-medium text-sm {{ request()->routeIs('anggota.attendance.index') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'hover:bg-slate-100 text-slate-600' }}">
                            <svg class="w-5 h-5 text-slate-500 {{ request()->routeIs('anggota.attendance.index') ? 'text-emerald-600' : '' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                </path>
                            </svg>
                            Mulai Absen Masuk
                        </a>
                    @endif
                    <a href="{{ route('anggota.attendance.history') }}"
                        class="flex items-center gap-4 px-4 py-2.5 rounded-full transition font-medium text-sm {{ request()->routeIs('anggota.attendance.history') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'hover:bg-slate-100 text-slate-600' }}">
                        <svg class="w-5 h-5 text-slate-500 {{ request()->routeIs('anggota.attendance.history') ? 'text-emerald-600' : '' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Riwayat Absensi
                    </a>
                    <a href="{{ route('anggota.reports.index') }}"
                        class="flex items-center gap-4 px-4 py-2.5 rounded-full transition font-medium text-sm {{ request()->routeIs('anggota.reports.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'hover:bg-slate-100 text-slate-600' }}">
                        <svg class="w-5 h-5 text-slate-500 {{ request()->routeIs('anggota.reports.*') ? 'text-emerald-600' : '' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Laporan Kegiatan
                    </a>
                @endif
            </nav>

            <!-- Profile Footer Section -->
            <div class="p-4 border-t border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-extrabold text-sm shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="truncate w-28">
                        <p class="text-xs font-bold text-slate-800 truncate leading-none mb-1">
                            {{ Auth::user()->name }}</p>
                        <span
                            class="text-[9px] text-emerald-600 font-bold uppercase tracking-wider">{{ Auth::user()->role }}</span>
                    </div>
                </div>

                <!-- Logout Action -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="p-2 hover:bg-slate-150 rounded-xl text-slate-400 hover:text-slate-700 transition"
                        title="Keluar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Pane -->
        <div class="flex-1 flex flex-col min-w-0 bg-[#f8fafc] h-full overflow-hidden relative">
            <!-- Header / Search Bar area in Google Drive -->
            <header
                class="min-h-[4rem] flex-shrink-0 bg-white border-b border-slate-200 px-5 md:px-8 py-3 flex flex-col md:flex-row md:items-center gap-2 md:justify-between">
                <div class="flex-1 min-w-0">
                    @isset($header)
                        {{ $header }}
                    @else
                        <h2 class="font-extrabold text-lg text-slate-800 tracking-tight">Absensi KKN Sirnaraja</h2>
                    @endisset
                </div>
                <div class="hidden md:flex items-center gap-4 text-xs font-bold text-slate-400 flex-shrink-0">
                    <span>Hari ini: {{ Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
                </div>
            </header>

            <!-- Page Content Section -->
            <main class="flex-1 p-5 md:p-8 pb-28 md:pb-8 overflow-y-auto">
                {{ $slot }}
            </main>
        </div>

    </div>

    <!-- Mobile Bottom Navigation (Floating Capsule style) -->
    <div class="md:hidden fixed bottom-6 left-4 right-4 z-50">
        <div
            class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] flex justify-between items-center px-2 py-3 relative">

            @if (Auth::user()->isAnggota())
                <!-- Left Side -->
                <div class="flex-1 flex justify-around items-center">
                    <!-- Home -->
                    <a href="{{ route('dashboard') }}"
                        class="flex flex-col items-center gap-1 w-12 {{ request()->routeIs('dashboard') ? 'text-indigo-600 font-bold' : 'text-slate-400' }}">
                        <svg class="w-6 h-6" fill="{{ request()->routeIs('dashboard') ? 'currentColor' : 'none' }}"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                        <span class="text-[10px]">Home</span>
                    </a>

                    <!-- Laporan -->
                    <a href="{{ route('anggota.reports.index') }}"
                        class="flex flex-col items-center gap-1 w-12 {{ request()->routeIs('anggota.reports.*') ? 'text-indigo-600 font-bold' : 'text-slate-400' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                        <span class="text-[10px]">Laporan</span>
                    </a>

                    <!-- Panduan -->
                    {{-- <a href="{{ route('anggota.panduan') }}" class="flex flex-col items-center gap-1 w-12 {{ request()->routeIs('anggota.panduan') ? 'text-indigo-600 font-bold' : 'text-slate-400' }}">
                            <svg class="w-6 h-6" fill="{{ request()->routeIs('anggota.panduan') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-[10px]">Panduan</span>
                        </a> --}}
                </div>

                <!-- Center Floating Button (Scanner) -->
                <div class="absolute left-1/2 -translate-x-1/2 -top-6 z-10">
                    <a href="{{ Auth::user()->faceData ? route('anggota.attendance.index') : route('anggota.face.register') }}"
                        class="flex items-center justify-center w-16 h-16 bg-indigo-600 rounded-full text-white shadow-lg border-[6px] border-[#f8fafc] transition transform active:scale-95"
                        title="Scan Absen">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </a>
                </div>

                <!-- Center Spacer -->
                <div class="w-14 flex-shrink-0"></div>

                <!-- Right Side -->
                <div class="flex-1 flex justify-around items-center">
                    <!-- History -->
                    <a href="{{ route('anggota.attendance.history') }}"
                        class="flex flex-col items-center gap-1 w-12 {{ request()->routeIs('anggota.attendance.history') ? 'text-indigo-600 font-bold' : 'text-slate-400' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-[10px]">History</span>
                    </a>

                    <!-- Profile -->
                    <a href="{{ route('profile.edit') }}"
                        class="flex flex-col items-center gap-1 w-12 {{ request()->routeIs('profile.edit') ? 'text-indigo-600 font-bold' : 'text-slate-400' }}">
                        <svg class="w-6 h-6"
                            fill="{{ request()->routeIs('profile.edit') ? 'currentColor' : 'none' }}"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-[10px]">Profil</span>
                    </a>
                </div>
            @elseif(Auth::user()->isSekretaris())
                <!-- Left Side (Sekretaris: Home, Rekap, Laporan) -->
                <div class="flex-1 flex justify-around items-center pr-1">
                    <!-- Home -->
                    <a href="{{ route('dashboard') }}"
                        class="flex flex-col items-center gap-1 w-11 {{ request()->routeIs('dashboard') ? 'text-indigo-600 font-bold' : 'text-slate-400' }}">
                        <svg class="w-5 h-5" fill="{{ request()->routeIs('dashboard') ? 'currentColor' : 'none' }}"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                        <span class="text-[10px]">Home</span>
                    </a>

                    <!-- Rekap Dropdown -->
                    <div x-data="{ rekapOpen: false }" class="relative flex flex-col items-center">
                        <div x-show="rekapOpen"
                             @click.away="rekapOpen = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="absolute bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-50 min-w-[160px]"
                             style="display:none; bottom: calc(100% + 10px); left: 50%; transform: translateX(-50%);">
                            <a href="{{ route('koordinator.attendance.rekap') }}"
                               class="flex items-center gap-2.5 px-4 py-3 text-xs font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition border-b border-slate-100">
                                <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Rekap Absen
                            </a>
                            <a href="{{ route('koordinator.reports.index') }}"
                               class="flex items-center gap-2.5 px-4 py-3 text-xs font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 transition">
                                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                                Rekap Laporan
                                @if ($warningReportsCount > 0)
                                    <span class="ml-auto bg-red-500 text-white text-[8px] font-bold min-w-[16px] h-4 flex items-center justify-center rounded-full shadow-[0_0_6px_rgba(239,68,68,0.8)] animate-pulse">{{ $warningReportsCount }}</span>
                                @endif
                            </a>
                        </div>

                        <button @click="rekapOpen = !rekapOpen"
                            class="flex flex-col items-center gap-1 w-11 {{ (request()->routeIs('koordinator.attendance.rekap') || request()->routeIs('koordinator.reports.*')) ? 'text-indigo-600 font-bold' : 'text-slate-400' }}">
                            <div class="relative">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                @if ($warningReportsCount > 0)
                                    <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[8px] font-bold min-w-[14px] h-3.5 flex items-center justify-center rounded-full border border-white shadow-[0_0_6px_rgba(239,68,68,0.8)] animate-pulse">{{ $warningReportsCount }}</span>
                                @endif
                            </div>
                            <span class="text-[10px]">Rekap</span>
                        </button>
                    </div>

                    <!-- Laporan -->
                    <a href="{{ route('anggota.reports.index') }}"
                        class="flex flex-col items-center gap-1 w-11 {{ request()->routeIs('anggota.reports.*') ? 'text-indigo-600 font-bold' : 'text-slate-400' }}">
                        <svg class="w-5 h-5" fill="{{ request()->routeIs('anggota.reports.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <span class="text-[10px]">Laporan</span>
                    </a>
                </div>

                <!-- Center Floating Button (Scanner) -->
                <div class="absolute left-1/2 -translate-x-1/2 -top-6 z-10">
                    <a href="{{ Auth::user()->faceData ? route('anggota.attendance.index') : route('anggota.face.register') }}"
                        class="flex items-center justify-center w-16 h-16 bg-indigo-600 rounded-full text-white shadow-lg border-[6px] border-[#f8fafc] transition transform active:scale-95"
                        title="Scan Absen">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </a>
                </div>

                <!-- Center Spacer -->
                <div class="w-14 flex-shrink-0"></div>

                <!-- Right Side (Sekretaris: Anggota, Lokasi, Profil) -->
                <div class="flex-1 flex justify-around items-center pl-1">
                    <a href="{{ route('koordinator.users.index') }}"
                        class="flex flex-col items-center gap-1 w-11 {{ request()->routeIs('koordinator.users.*') ? 'text-indigo-600 font-bold' : 'text-slate-400' }}">
                        <svg class="w-5 h-5"
                            fill="{{ request()->routeIs('koordinator.users.*') ? 'currentColor' : 'none' }}"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        <span class="text-[10px]">Anggota</span>
                    </a>

                    <a href="{{ route('koordinator.locations.index') }}"
                        class="flex flex-col items-center gap-1 w-11 {{ request()->routeIs('koordinator.locations.*') ? 'text-indigo-600 font-bold' : 'text-slate-400' }}">
                        <svg class="w-5 h-5"
                            fill="{{ request()->routeIs('koordinator.locations.*') ? 'currentColor' : 'none' }}"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-[10px]">Lokasi</span>
                    </a>

                    <a href="{{ route('profile.edit') }}"
                        class="flex flex-col items-center gap-1 w-11 {{ request()->routeIs('profile.edit') ? 'text-indigo-600 font-bold' : 'text-slate-400' }}">
                        <svg class="w-5 h-5"
                            fill="{{ request()->routeIs('profile.edit') ? 'currentColor' : 'none' }}"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-[10px]">Profil</span>
                    </a>
                </div>
            @elseif(Auth::user()->isKoordinator())
                <!-- Left Side (Koordinator: Home, Rekap) -->
                <div class="flex-1 flex justify-around items-center">
                    <!-- Home -->
                    <a href="{{ route('dashboard') }}"
                        class="flex flex-col items-center gap-1 w-12 {{ request()->routeIs('dashboard') ? 'text-indigo-600 font-bold' : 'text-slate-400' }}">
                        <svg class="w-6 h-6" fill="{{ request()->routeIs('dashboard') ? 'currentColor' : 'none' }}"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                        <span class="text-[10px]">Home</span>
                    </a>

                    <!-- Rekap Dropdown -->
                    <div x-data="{ rekapOpen: false }" class="relative flex flex-col items-center">
                        <div x-show="rekapOpen"
                             @click.away="rekapOpen = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="absolute bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-50 min-w-[160px]"
                             style="display:none; bottom: calc(100% + 10px); left: 50%; transform: translateX(-50%);">
                            <a href="{{ route('koordinator.attendance.rekap') }}"
                               class="flex items-center gap-2.5 px-4 py-3 text-xs font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition border-b border-slate-100">
                                <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Rekap Absen
                            </a>
                            <a href="{{ route('koordinator.reports.index') }}"
                               class="flex items-center gap-2.5 px-4 py-3 text-xs font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 transition">
                                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                                Rekap Laporan
                                @if ($warningReportsCount > 0)
                                    <span class="ml-auto bg-red-500 text-white text-[8px] font-bold min-w-[16px] h-4 flex items-center justify-center rounded-full shadow-[0_0_6px_rgba(239,68,68,0.8)] animate-pulse">{{ $warningReportsCount }}</span>
                                @endif
                            </a>
                        </div>

                        <button @click="rekapOpen = !rekapOpen"
                            class="flex flex-col items-center gap-1 w-12 {{ (request()->routeIs('koordinator.attendance.rekap') || request()->routeIs('koordinator.reports.*')) ? 'text-indigo-600 font-bold' : 'text-slate-400' }}">
                            <div class="relative">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                @if ($warningReportsCount > 0)
                                    <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[8px] font-bold min-w-[14px] h-3.5 flex items-center justify-center rounded-full border border-white shadow-[0_0_6px_rgba(239,68,68,0.8)] animate-pulse">{{ $warningReportsCount }}</span>
                                @endif
                            </div>
                            <span class="text-[10px]">Rekap</span>
                        </button>
                    </div>
                </div>

                <!-- Center Floating Button (Scanner) -->
                <div class="absolute left-1/2 -translate-x-1/2 -top-6 z-10">
                    <a href="{{ Auth::user()->faceData ? route('anggota.attendance.index') : route('anggota.face.register') }}"
                        class="flex items-center justify-center w-16 h-16 bg-indigo-600 rounded-full text-white shadow-lg border-[6px] border-[#f8fafc] transition transform active:scale-95"
                        title="Scan Absen">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </a>
                </div>

                <!-- Center Spacer -->
                <div class="w-14 flex-shrink-0"></div>

                <!-- Right Side (Koordinator: Laporan, Profil) -->
                <div class="flex-1 flex justify-around items-center">
                    <!-- Laporan (upload kegiatan pribadi) -->
                    <a href="{{ route('anggota.reports.index') }}"
                        class="flex flex-col items-center gap-1 w-12 {{ request()->routeIs('anggota.reports.*') ? 'text-indigo-600 font-bold' : 'text-slate-400' }}">
                        <svg class="w-6 h-6" fill="{{ request()->routeIs('anggota.reports.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <span class="text-[10px]">Laporan</span>
                    </a>

                    <!-- Profil -->
                    <a href="{{ route('profile.edit') }}"
                        class="flex flex-col items-center gap-1 w-12 {{ request()->routeIs('profile.edit') ? 'text-indigo-600 font-bold' : 'text-slate-400' }}">
                        <svg class="w-6 h-6"
                            fill="{{ request()->routeIs('profile.edit') ? 'currentColor' : 'none' }}"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-[10px]">Profil</span>
                    </a>
                </div>
            @else
                <!-- DPL / Others -->
                <div class="flex-1 flex justify-around items-center">
                    <a href="{{ route('dashboard') }}"
                        class="flex flex-col items-center gap-1 w-12 {{ request()->routeIs('dashboard') ? 'text-indigo-600 font-bold' : 'text-slate-400' }}">
                        <svg class="w-6 h-6" fill="{{ request()->routeIs('dashboard') ? 'currentColor' : 'none' }}"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                        <span class="text-[10px]">Home</span>
                    </a>
                </div>
                
                <div class="absolute left-1/2 -translate-x-1/2 -top-6 z-10">
                    <a href="{{ Auth::user()->faceData ? route('anggota.attendance.index') : route('anggota.face.register') }}"
                        class="flex items-center justify-center w-16 h-16 bg-indigo-600 rounded-full text-white shadow-lg border-[6px] border-[#f8fafc] transition transform active:scale-95"
                        title="Scan Absen">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </a>
                </div>

                <div class="w-14 flex-shrink-0"></div>
                <div class="flex-1 flex justify-around items-center">
                    <a href="{{ route('profile.edit') }}"
                        class="flex flex-col items-center gap-1 w-12 {{ request()->routeIs('profile.edit') ? 'text-indigo-600 font-bold' : 'text-slate-400' }}">
                        <svg class="w-6 h-6"
                            fill="{{ request()->routeIs('profile.edit') ? 'currentColor' : 'none' }}"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-[10px]">Profil</span>
                    </a>
                </div>
            @endif
        </div>
    </div>

    @stack('scripts')
</body>

</html>
