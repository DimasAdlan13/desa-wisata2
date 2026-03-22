<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Platform booking layanan wisata Pulau Pramuka, Kepulauan Seribu">
    <title>{{ config('app.name') }} - {{ $title ?? 'Desa Wisata' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2" wire:navigate>
                    <div class="w-8 h-8 bg-teal-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-sm">DW</span>
                    </div>
                    <span class="font-bold text-teal-700 hidden sm:block">Desa Wisata</span>
                </a>

                <!-- Nav Links -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-teal-600 transition-colors" wire:navigate>Beranda</a>
                    <a href="{{ route('layanan.index') }}" class="text-gray-600 hover:text-teal-600 transition-colors" wire:navigate>Layanan</a>
                    <a href="{{ route('konten.index') }}" class="text-gray-600 hover:text-teal-600 transition-colors" wire:navigate>Konten</a>
                </div>

                <!-- Auth -->
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-teal-600" wire:navigate>
                            👤 {{ auth()->user()->name }}
                        </a>
                        @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdminLayanan())
                            <a href="/admin" class="text-sm bg-orange-500 text-white px-3 py-1.5 rounded-lg hover:bg-orange-600 transition">Admin</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="text-sm text-red-500 hover:text-red-700">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-teal-600" wire:navigate>Masuk</a>
                        <a href="{{ route('register') }}" class="text-sm bg-teal-600 text-white px-4 py-1.5 rounded-lg hover:bg-teal-700 transition" wire:navigate>Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)"
             class="fixed top-20 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg max-w-sm">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 6000)"
             class="fixed top-20 right-4 z-50 bg-blue-500 text-white px-6 py-3 rounded-xl shadow-lg max-w-sm">
            ℹ️ {{ session('info') }}
        </div>
    @endif

    <!-- Main Content -->
    <main class="min-h-screen">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-teal-800 text-teal-100 mt-16 py-10">
        <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-3 gap-8">
            <div>
                <h3 class="font-bold text-white text-lg mb-2">Desa Wisata Kepulauan Seribu</h3>
                <p class="text-sm text-teal-200">Platform booking layanan wisata Pulau Pramuka, Kepulauan Seribu, Jakarta.</p>
            </div>
            <div>
                <h4 class="font-semibold text-white mb-2">Navigasi</h4>
                <ul class="space-y-1 text-sm">
                    <li><a href="{{ route('layanan.index') }}" class="hover:text-white" wire:navigate>Layanan Wisata</a></li>
                    <li><a href="{{ route('konten.index') }}" class="hover:text-white" wire:navigate>Artikel & Info</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-white" wire:navigate>Daftar Mitra</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-white mb-2">Kontak</h4>
                <p class="text-sm">📍 Pulau Pramuka, Kepulauan Seribu</p>
                <p class="text-sm">📧 info@desawisataseribu.id</p>
            </div>
        </div>
        <div class="text-center text-sm text-teal-300 mt-8 pt-4 border-t border-teal-700">
            © {{ date('Y') }} Desa Wisata Kepulauan Seribu. All rights reserved.
        </div>
    </footer>

    @livewireScripts
</body>
</html>
