<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-['Inter'] text-slate-800 antialiased selection:bg-emerald-500 selection:text-white h-full overflow-hidden">
        <div class="h-full flex flex-col justify-center items-center p-4 bg-slate-50 overflow-y-auto">
            <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-xl shadow-slate-200/50 overflow-hidden rounded-2xl border border-slate-100 my-auto">
                <div class="mb-6 text-center">
                    <a href="/" class="inline-block mb-4">
                        <img src="{{ asset('logo_sirnaraja.png') }}" alt="Logo Sirnaraja" class="h-16 w-16 object-contain mx-auto drop-shadow-sm">
                    </a>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Selamat Datang</h2>
                    <p class="text-sm text-slate-500 mt-1">Sistem Absensi KKN Kelompok 4</p>
                </div>
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
