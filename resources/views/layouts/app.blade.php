<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Platform booking layanan wisata Pulau Pramuka, Kepulauan Seribu">
    <title>{{ config('app.name') }} - {{ $title ?? 'Desa Wisata' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 1s ease-out both;
        }
        .animate-fade-in-up-delay-1 {
            animation: fadeInUp 1s ease-out 0.15s both;
        }
        .animate-fade-in-up-delay-2 {
            animation: fadeInUp 1s ease-out 0.3s both;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">

    <!-- Navigation -->
    <nav
        x-data="{
            open: false,
            scrolled: false,
            currentPath: window.location.pathname,
            isActive(path) {
                if (path === '/') return this.currentPath === '/';
                return this.currentPath.startsWith(path);
            }
        }"
        x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 40 })"
        :class="scrolled ? 'backdrop-blur-md shadow-sm border-b border-black/5' : 'backdrop-blur-sm'"
        class="sticky top-0 z-50 transition-all duration-500"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 flex-shrink-0" wire:navigate>
                    <img src="{{ asset('images/logo 1.png') }}"
                         alt="Logo Desa Wisata"
                         class="h-10 w-auto transition-all duration-300 drop-shadow-sm"
                         :class="scrolled ? 'h-9' : 'h-10'">
                    <div class="hidden sm:block">
                        <span :class="scrolled ? 'text-gray-900' : 'text-gray-800'" class="font-bold text-sm leading-none block transition-colors duration-300">Desa Wisata</span>
                        <span :class="scrolled ? 'text-teal-600' : 'text-teal-500'" class="text-xs leading-none transition-colors duration-300">Kepulauan Seribu</span>
                    </div>
                </a>


                <!-- Desktop Nav Links: bordered grouped container (md+) -->
                <div :class="scrolled ? 'border-gray-200' : 'border-gray-200/60'"
                     class="hidden md:flex items-center border rounded-2xl p-1 shadow-sm backdrop-blur-sm transition-all duration-300">
                    <a href="{{ route('home') }}" wire:navigate
                       :class="isActive('/') && currentPath === '/' ? 'text-teal-700 font-semibold' : 'text-gray-500 hover:text-teal-700 hover:bg-white/70'"
                       class="px-4 py-1.5 rounded-xl text-sm transition-all duration-200">
                        Beranda
                    </a>
                    <a href="{{ route('layanan.index') }}" wire:navigate
                       :class="isActive('/layanan') ? 'text-teal-700 font-semibold' : 'text-gray-500 hover:text-teal-700 hover:bg-white/70'"
                       class="px-4 py-1.5 rounded-xl text-sm transition-all duration-200">
                        Layanan Wisata
                    </a>
                    <a href="{{ route('konten.index') }}" wire:navigate
                       :class="isActive('/konten') ? 'text-teal-700 font-semibold' : 'text-gray-500 hover:text-teal-700 hover:bg-white/70'"
                       class="px-4 py-1.5 rounded-xl text-sm transition-all duration-200">
                        Info & Artikel
                    </a>
                </div>

                <!-- Desktop Auth (md: 768px ke atas) -->
                <div class="hidden md:flex items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" wire:navigate
                           class="flex items-center gap-2 text-sm text-gray-700 hover:text-teal-700 px-3 py-2 rounded-lg hover:bg-gray-50 transition-all">
                            <div class="w-7 h-7 bg-teal-100 rounded-full flex items-center justify-center">
                                <span class="text-teal-700 font-bold text-xs">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            </div>
                            <span class="hidden lg:block max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                        </a>
                        @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdminLayanan())
                            <a href="/admin"
                               class="text-xs font-semibold bg-orange-500 text-white px-3 py-1.5 rounded-lg hover:bg-orange-600 transition-all shadow-sm">
                                Panel Admin
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="text-sm text-gray-400 hover:text-red-500 px-2 py-1.5 rounded-lg hover:bg-red-50 transition-all">
                                Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" wire:navigate
                           class="text-sm font-medium text-gray-600 hover:text-teal-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-all">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" wire:navigate
                           class="text-sm font-semibold bg-teal-600 text-white px-4 py-2 rounded-xl hover:bg-teal-700 transition-all shadow-sm hover:shadow-md">
                            Daftar Gratis
                        </a>
                    @endauth
                </div>

                <!-- Mobile: Auth shortcut + Hamburger (< 768px) -->
                <div class="flex md:hidden items-center gap-2">
                    @guest
                        <a href="{{ route('register') }}" wire:navigate
                           class="text-xs font-semibold bg-teal-600 text-white px-3 py-1.5 rounded-lg hover:bg-teal-700 transition-all">
                            Daftar
                        </a>
                    @endguest
                    @auth
                        <div class="w-8 h-8 bg-teal-100 rounded-full flex items-center justify-center">
                            <span class="text-teal-700 font-bold text-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        </div>
                    @endauth
                    <!-- Hamburger Button -->
                    <button @click="open = !open"
                            class="p-2 rounded-lg text-gray-500 hover:text-teal-700 hover:bg-gray-100 transition-all"
                            aria-label="Toggle Menu">
                        <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Dropdown Menu (< 768px) -->
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            @click.outside="open = false"
            class="md:hidden border-t border-gray-100 bg-white shadow-lg"
            x-cloak
        >
            <div class="max-w-7xl mx-auto px-4 py-3 space-y-1">
                <!-- Nav Links -->
                <a href="{{ route('home') }}" wire:navigate @click="open = false"
                   :class="isActive('/') && currentPath === '/' ? 'text-teal-700 bg-teal-50 font-semibold' : 'text-gray-600'"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm hover:bg-gray-50 transition-all">
                    <span>🏠</span> Beranda
                </a>
                <a href="{{ route('layanan.index') }}" wire:navigate @click="open = false"
                   :class="isActive('/layanan') ? 'text-teal-700 bg-teal-50 font-semibold' : 'text-gray-600'"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm hover:bg-gray-50 transition-all">
                    <span>🌊</span> Layanan Wisata
                </a>
                <a href="{{ route('konten.index') }}" wire:navigate @click="open = false"
                   :class="isActive('/konten') ? 'text-teal-700 bg-teal-50 font-semibold' : 'text-gray-600'"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm hover:bg-gray-50 transition-all">
                    <span>📰</span> Info & Artikel
                </a>

                <!-- Divider -->
                <div class="border-t border-gray-100 my-2"></div>

                <!-- Auth Mobile -->
                @auth
                    <a href="{{ route('dashboard') }}" wire:navigate @click="open = false"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
                        <div class="w-7 h-7 bg-teal-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-teal-700 font-bold text-xs">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-gray-400">Lihat Dashboard</div>
                        </div>
                    </a>
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdminLayanan())
                        <a href="/admin" @click="open = false"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-orange-600 bg-orange-50 hover:bg-orange-100 font-semibold transition-all">
                            <span>⚙️</span> Panel Admin
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-red-500 hover:bg-red-50 transition-all text-left">
                            <span>🚪</span> Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" wire:navigate @click="open = false"
                       class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-medium text-teal-700 border border-teal-200 hover:bg-teal-50 transition-all">
                        Masuk ke Akun
                    </a>
                    <a href="{{ route('register') }}" wire:navigate @click="open = false"
                       class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold text-white bg-teal-600 hover:bg-teal-700 transition-all">
                        🌊 Daftar Gratis Sekarang
                    </a>
                @endauth
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
