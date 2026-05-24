<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - {{ $title ?? 'Autentikasi' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="antialiased bg-gradient-to-br from-teal-600 to-blue-800 md:bg-none md:bg-gray-50 text-gray-800">

    <div class="w-full flex min-h-screen md:h-screen overflow-hidden">

        <!-- Bagian Kiri (Hanya muncul di md ke atas) -->
        <div class="hidden md:flex md:w-1/2 bg-teal-900 relative items-center justify-center">
            <!-- Background Image -->
            <img src="{{ asset('images/wisata_pulau_pramuka_e4ddfe0849.webp') }}"
                alt="Island View" class="absolute inset-0 w-full h-full object-cover opacity-40">

            <!-- Overlay Content -->
            <div class="relative z-10 px-12 text-white">
                <a href="{{ route('home') }}" wire:navigate class="inline-flex items-center gap-3 mb-8">
                    <img src="{{ asset('images/logo.webp') }}" alt="Logo Desa Wisata" class="w-12 h-12 object-contain">
                    <span class="font-bold text-2xl tracking-tight">Desa Wisata<br>Pulau Pramuka</span>
                </a>

                <h1 class="text-4xl lg:text-5xl font-extrabold mb-4 leading-tight">Jelajahi Pulau Pramuka</h1>
                <p class="text-lg text-teal-100 max-w-md leading-relaxed">
                    Booking layanan wisata terpercaya di Pulau Pramuka dalam satu platform.
                </p>
            </div>
        </div>

        <!-- Bagian Kanan / Kontainer Utama Mobile -->
        <div
            class="w-full max-w-md md:max-w-none px-4 md:px-0 md:w-1/2 flex flex-col md:bg-white lg:bg-gray-50 mx-auto h-screen overflow-y-auto">

            <div class="w-full md:max-w-md md:p-12 mx-auto my-auto py-10">

                <!-- Desktop Back Button -->
                <div class="hidden md:flex mb-6 justify-start">
                    <a href="{{ route('home') }}" wire:navigate
                        class="text-teal-600 hover:text-teal-700 font-medium text-sm flex items-center gap-1 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                        Beranda
                    </a>
                </div>

                <!-- Mobile Logo (Persis seperti aslinya, hanya muncul di mobile) -->
                <div class="md:hidden text-center mb-8 mt-8">
                    <a href="{{ route('home') }}" wire:navigate class="inline-flex flex-col items-center gap-2">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-lg">
                            <span class="text-teal-700 font-bold text-2xl">DW</span>
                        </div>
                        <span class="text-white font-semibold text-lg">Desa Wisata Pulau Pramuka</span>
                    </a>
                </div>

                <!-- Form Box -->
                <div class="bg-white rounded-2xl md:rounded-3xl shadow-2xl p-8 md:p-10 w-full">
                    {{ $slot }}
                </div>

                <!-- Copyright -->
                <p class="text-center text-teal-200 md:text-gray-400 text-sm mt-6 md:mt-8 mb-8 md:mb-0">
                    © {{ date('Y') }} Desa Wisata Pulau Pramuka
                </p>
            </div>
        </div>
    </div>

    @livewireScripts
</body>

</html>
