<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - {{ $title ?? 'Autentikasi' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gradient-to-br from-teal-600 to-blue-800 min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" wire:navigate class="inline-flex flex-col items-center gap-2">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <span class="text-teal-700 font-bold text-2xl">DW</span>
                </div>
                <span class="text-white font-semibold text-lg">Desa Wisata Kepulauan Seribu</span>
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8">
            {{ $slot }}
        </div>

        <p class="text-center text-teal-200 text-sm mt-6">
            © {{ date('Y') }} Desa Wisata Kepulauan Seribu
        </p>
    </div>

    @livewireScripts
</body>
</html>
