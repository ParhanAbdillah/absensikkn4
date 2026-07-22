<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sistem Absensi KKN - Kelompok 4</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,600,700,800&display=swap" rel="stylesheet" />
        
        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-slate-50 text-slate-800 selection:bg-emerald-500 selection:text-white font-['Inter']">
        
        <div class="min-h-screen flex flex-col">
            <!-- Navbar -->
            <nav class="w-full bg-white border-b border-slate-200 px-6 py-4">
                <div class="max-w-6xl mx-auto flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('logo_sirnaraja.png') }}" alt="Logo Sirnaraja" class="h-8 object-contain">
                    </div>
                    <div>
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-sm font-bold text-slate-600 hover:text-emerald-600 transition">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-2 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition shadow-sm">
                                    Login
                                </a>
                            @endauth
                        @endif
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="flex-grow flex items-center justify-center p-6">
                <div class="max-w-2xl w-full text-center space-y-8">
                    <div class="flex items-center justify-center mb-6">
                        <img src="{{ asset('logo_sirnaraja.png') }}" alt="Logo Sirnaraja" class="h-24 sm:h-28 object-contain drop-shadow-sm">
                    </div>

                    <div class="space-y-4">
                        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                            Sistem Absensi & Laporan KKN
                        </h1>
                        <p class="text-lg text-slate-500 leading-relaxed max-w-lg mx-auto">
                            Politeknik LP3I Kampus Tasikmalaya <br>
                            <span class="font-bold text-emerald-600">Kelompok 4 - Desa Sirnaraja</span>
                        </p>
                    </div>

                    <div class="pt-6">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center min-w-[200px] px-14 py-3.5 text-base font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-2xl transition shadow-lg shadow-emerald-200">
                                    Masuk ke Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center min-w-[200px] px-14 py-3.5 text-base font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-2xl transition shadow-lg shadow-emerald-200">
                                    Login Sistem
                                </a>
                            @endauth
                        @endif
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="w-full bg-white border-t border-slate-200 py-6 text-center">
                <p class="text-xs sm:text-sm text-slate-500 font-medium">
                    &copy; {{ date('Y') }} KKN LP3I Kelompok 4 Desa Sirnaraja. Hak Cipta Dilindungi.
                </p>
            </footer>
        </div>
        
    </body>
</html>
