<div x-data="{ showLoginModal: false }">
    <!-- Hero Image / Gallery -->
    <div class="w-full bg-teal-900 h-64 md:h-96 relative">
        @if($service->primaryPhoto)
            <img src="{{ Storage::url($service->primaryPhoto->photo_path) }}" alt="{{ $service->name }}"
                class="w-full h-full object-cover opacity-80">
        @else
            <div class="w-full h-full flex items-center justify-center text-teal-300 text-6xl">🏖️</div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 to-transparent"></div>
        <div class="absolute bottom-0 left-0 w-full p-6 md:p-12">
            <div class="max-w-7xl mx-auto">
                <span
                    class="bg-teal-500 text-white px-3 py-1 rounded-full text-sm font-semibold mb-3 inline-block">{{ $service->category->name }}</span>
                <h1 class="text-3xl md:text-5xl font-bold text-white mb-2">{{ $service->name }}</h1>
                <p class="text-teal-100 flex items-center gap-2">📍 {{ $service->location ?? 'Pulau Pramuka' }}</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="bg-white/50 backdrop-blur pb-4 mb-4">
            <a href="{{ route('layanan.index') }}" wire:navigate
                class="text-teal-600 hover:underline text-sm font-medium">← Kembali ke Layanan</a>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Left Content: Description & Photos & Reviews -->
            <div class="flex-1 space-y-10">
                <!-- Description -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Tentang Layanan Ini</h2>
                    <div class="prose prose-teal max-w-none text-gray-600">
                        {!! $service->description !!}
                    </div>
                </div>

                <!-- Gallery -->
                @if($service->photos->count() > 1)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">Galeri Foto</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($service->photos as $photo)
                                <a href="{{ Storage::url($photo->photo_path) }}" target="_blank"
                                    class="block h-32 md:h-48 overflow-hidden rounded-xl">
                                    <img src="{{ Storage::url($photo->photo_path) }}"
                                        class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Reviews -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-2xl font-bold text-gray-800">Ulasan Wisatawan</h2>
                        <div class="flex items-center gap-2">
                            <span class="text-yellow-400 text-2xl">⭐</span>
                            <span
                                class="text-xl font-bold text-gray-800">{{ $avgRating ? number_format($avgRating, 1) : '-' }}</span>
                            <span class="text-gray-400">({{ $ratings->count() }} ulasan)</span>
                        </div>
                    </div>

                    @if($ratings->isEmpty())
                        <div class="text-center py-8 text-gray-400">Belum ada ulasan untuk layanan ini.</div>
                    @else
                        <div class="space-y-6">
                            @foreach($ratings as $rating)
                                <div class="border-b border-gray-100 pb-6 last:border-0 last:pb-0">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-teal-100 text-teal-700 rounded-full flex items-center justify-center font-bold">
                                                {{ substr($rating->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800 text-sm">{{ $rating->user->name }}</p>
                                                <p class="text-xs text-gray-400">{{ $rating->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <div class="text-yellow-400 text-sm">
                                            {{ str_repeat('⭐', $rating->rating) }}
                                        </div>
                                    </div>
                                    @if($rating->review)
                                        <p class="text-gray-600 mt-3 text-sm italic">"{{ $rating->review }}"</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Sidebar: Booking Card -->
            <aside class="lg:w-[380px] flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-lg border border-teal-100 p-6 sticky top-24">
                    <div class="text-center mb-6">
                        <p class="text-sm text-gray-500 mb-1">Harga mulai dari</p>
                        <h3 class="text-3xl font-extrabold text-teal-700">{{ $service->formatted_price }} <span
                                class="text-sm font-normal text-gray-500">/
                                {{ strtolower($service->unit_name ?: 'orang') }}</span></h3>
                    </div>

                    <div class="space-y-4 mb-8">
                        <div class="flex items-center gap-3 text-gray-600">
                            <span class="text-xl">👥</span>
                            <div>
                                <p class="text-sm text-gray-500">Kuota Maksimal</p>
                                <p class="font-semibold">{{ $service->quota_per_day }}
                                    {{ $service->unit_name ?: 'Pax' }} / hari
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 text-gray-600">
                            <span class="text-xl">📞</span>
                            <div>
                                <p class="text-sm text-gray-500">Kontak Pengelola</p>
                                <p class="font-semibold">{{ $service->contact_person ?? '-' }}</p>
                            </div>
                        </div>
                        @if($service->user)
                            <div class="flex items-center gap-3 text-gray-600">
                                <span class="text-xl">🏢</span>
                                <div>
                                    <p class="text-sm text-gray-500">Dikelola Oleh</p>
                                    <p class="font-semibold">{{ $service->user->business_name ?? $service->user->name }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    @auth
                        @if(auth()->user()->isWisatawan())
                            @if($availableQuota !== null && $availableQuota <= 0)
                                <div class="bg-red-50 text-red-700 px-4 py-3 rounded-xl text-center text-sm font-medium mb-4">
                                    Mohon maaf, kuota untuk hari ini sudah penuh.
                                </div>
                            @endif

                            <a href="{{ route('booking.create', $service->slug) }}" wire:navigate
                                class="block w-full text-center bg-yellow-400 text-yellow-900 font-bold px-6 py-4 rounded-xl hover:bg-yellow-300 transition-colors shadow-md transform hover:-translate-y-1">
                                Booking Sekarang
                            </a>
                        @else
                            <div class="bg-blue-50 text-blue-800 px-4 py-3 rounded-xl text-center text-sm font-medium">
                                Anda login sebagai Admin. Hanya Wisatawan yang bisa membuat Booking.
                            </div>
                        @endif
                    @else
                        <button @click="showLoginModal = true"
                            class="block w-full text-center bg-yellow-400 text-yellow-900 font-bold px-6 py-4 rounded-xl hover:bg-yellow-300 transition-colors shadow-md transform hover:-translate-y-1">
                            🎫 Pesan Sekarang
                        </button>
                        <p class="text-center text-xs text-gray-500 mt-4">Belum punya akun? <a
                                href="{{ route('register') }}" class="text-teal-600 hover:underline">Daftar</a></p>
                    @endauth


                </div>
            </aside>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════
         CUSTOM LOGIN MODAL — Level root, di atas semua layer
    ═══════════════════════════════════════════════ -->
    <div x-show="showLoginModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @keydown.escape.window="showLoginModal = false"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="display: none;">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showLoginModal = false"></div>

        <div x-show="showLoginModal" x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full p-8 text-center z-10">
            <div class="w-16 h-16 bg-teal-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>

            <h3 class="text-xl font-bold text-gray-800 mb-2">Masuk Dulu, Yuk!</h3>
            <p class="text-gray-500 text-sm mb-6">
                Kamu perlu <span class="font-semibold text-teal-600">login</span> terlebih dahulu untuk melanjutkan
                proses booking layanan ini.
            </p>

            <div class="flex gap-3">
                <button @click="showLoginModal = false"
                    class="w-1/3 py-3 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <a href="{{ route('login') }}"
                    class="w-2/3 py-3 rounded-xl bg-teal-600 text-white text-sm font-bold hover:bg-teal-700 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Masuk Sekarang
                </a>
            </div>

            <p class="text-center text-xs text-gray-400 mt-4">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-teal-600 hover:underline font-medium">Daftar Gratis</a>
            </p>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════
         LAYANAN SERUPA — Content-Based Filtering
    ═══════════════════════════════════════════════ -->
    @if($similarServices->isNotEmpty())
        <div class="border-t border-gray-100 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 py-12 md:py-16">

                <!-- Header -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-8">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">
                            Layanan Serupa
                        </h2>

                    </div>
                    <a href="{{ route('layanan.index', ['categoryId' => $service->category_id]) }}" wire:navigate
                        class="inline-flex items-center gap-1 text-teal-600 hover:text-teal-800 text-sm font-semibold transition-colors shrink-0">
                        Lihat semua kategori ini →
                    </a>
                </div>

                <!-- Grid Cards: 2 kolom (mobile) → 3 kolom (sm ke atas) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4 md:gap-6">
                    @foreach($similarServices as $similar)
                        <a href="{{ route('layanan.show', $similar->slug) }}" wire:navigate
                            class="group bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col">

                            <!-- Thumbnail -->
                            <div class="relative h-28 sm:h-40 md:h-44 bg-teal-50 overflow-hidden shrink-0">
                                @if($similar->primaryPhoto)
                                    <img src="{{ Storage::url($similar->primaryPhoto->photo_path) }}" alt="{{ $similar->name }}"
                                        loading="lazy"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-3xl sm:text-5xl text-teal-300">
                                        {{ $similar->category->icon ?? '🏖️' }}
                                    </div>
                                @endif

                                <!-- Badge Kategori -->
                                <span
                                    class="absolute top-2 left-2 sm:top-3 sm:left-3 bg-white/90 backdrop-blur-sm text-teal-700 text-[10px] sm:text-xs font-bold px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-full shadow-sm">
                                    {{ $similar->category->name }}
                                </span>
                            </div>

                            <!-- Konten Card -->
                            <div class="p-3 sm:p-4 flex flex-col flex-1 gap-1 sm:gap-2">
                                <h3
                                    class="font-bold text-gray-800 text-xs sm:text-sm leading-snug line-clamp-2 group-hover:text-teal-700 transition-colors">
                                    {{ $similar->name }}
                                </h3>

                                <!-- Rating -->
                                @php $simRating = round($similar->ratings->avg('rating') ?? 0, 1); @endphp
                                <div class="flex items-center gap-1 sm:gap-1.5 text-[10px] sm:text-xs text-gray-500">
                                    @if($simRating > 0)
                                        <span class="text-yellow-400">⭐</span>
                                        <span class="font-semibold text-gray-700">{{ $simRating }}</span>
                                        <span class="hidden sm:inline">({{ $similar->ratings->count() }} ulasan)</span>
                                    @else
                                        <span class="text-gray-400 italic">Belum ada ulasan</span>
                                    @endif
                                </div>

                                <!-- Harga di bagian bawah -->
                                <div class="mt-auto pt-2 sm:pt-3 border-t border-gray-50 flex items-center justify-between">
                                    <div>
                                        <p class="text-[8px] sm:text-[10px] text-gray-400 uppercase tracking-wide">Mulai dari</p>
                                        <p class="text-teal-700 font-extrabold text-xs sm:text-sm">{{ $similar->formatted_price }}</p>
                                    </div>
                                    <span
                                        class="hidden sm:inline text-xs bg-teal-50 text-teal-600 group-hover:bg-teal-600 group-hover:text-white font-semibold px-3 py-1.5 rounded-full transition-colors">
                                        Detail →
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

</div>