<div>
    <!-- ============================
         SECTION 1: Carousel Artikel
    ============================= -->
    <section x-data="{
            current: 0,
            total: {{ $latestContents->count() ?: 1 }},
            timer: null,
            startTimer() { this.timer = setInterval(() => this.next(), 3000) },
            stopTimer() { clearInterval(this.timer) },
            next() { this.current = (this.current + 1) % this.total },
            prev() { this.current = (this.current - 1 + this.total) % this.total },
            goTo(i) { this.current = i }
        }" x-init="startTimer()" @mouseenter="stopTimer()" @mouseleave="startTimer()"
        class="relative w-full overflow-hidden bg-teal-900" style="height: 68vh; min-height: 420px;">
        @if($latestContents->count() > 0)
            {{-- Slides strip: lebar = total × 100%, bergerak via translateX --}}
            <div class="flex h-full transition-transform duration-700 ease-in-out"
                :style="`transform: translateX(-${current * 100}%)`">
                @foreach($latestContents as $content)
                    <div class="relative shrink-0 w-full h-full">
                        {{-- Background image --}}
                        @if($content->cover_image)
                            <img src="{{ Storage::url($content->cover_image) }}" class="absolute inset-0 w-full h-full object-cover"
                                alt="{{ $content->title }}" loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-teal-700 to-blue-800"></div>
                        @endif
                        {{-- Gradient overlay --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                        {{-- Text --}}
                        <div class="absolute inset-0 flex flex-col justify-end px-6 pb-16 md:px-16 md:pb-20 z-10 max-w-5xl">
                            <span
                                class="inline-block bg-teal-400/90 backdrop-blur text-white text-xs font-bold px-4 py-1 rounded-full uppercase tracking-widest mb-4 w-fit shadow">
                                Info & Artikel
                            </span>
                            <h2
                                class="text-2xl sm:text-4xl md:text-5xl font-extrabold text-white leading-tight mb-5 drop-shadow-md line-clamp-3">
                                {{ $content->title }}
                            </h2>
                            <a href="{{ route('konten.show', $content->slug) }}" wire:navigate
                                class="inline-flex items-center gap-2 bg-white text-teal-800 font-bold px-5 py-2.5 rounded-full hover:bg-teal-50 transition-all shadow-lg w-fit text-sm hover:-translate-y-0.5">
                                Baca Selengkapnya →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Dot indicators --}}
            <div class="absolute bottom-5 left-0 right-0 flex justify-center items-center gap-2 z-20">
                @foreach($latestContents as $index => $c)
                    <button @click="goTo({{ $index }})"
                        :class="current === {{ $index }} ? 'bg-white w-6 h-2' : 'bg-white/40 w-2 h-2 hover:bg-white/70'"
                        class="rounded-full transition-all duration-300" aria-label="Slide {{ $index + 1 }}">
                    </button>
                @endforeach
            </div>

            {{-- Prev / Next arrows (hidden on mobile) --}}
            <button @click="prev()"
                class="hidden md:flex absolute left-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 bg-black/30 hover:bg-black/50 text-white rounded-full items-center justify-center transition backdrop-blur-sm">‹</button>
            <button @click="next()"
                class="hidden md:flex absolute right-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 bg-black/30 hover:bg-black/50 text-white rounded-full items-center justify-center transition backdrop-blur-sm">›</button>
        @else
            {{-- fallback jika belum ada konten --}}
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-teal-700 to-blue-700">
                <p class="text-white text-xl font-bold opacity-60">Belum ada artikel</p>
            </div>
        @endif
    </section>

    <!-- SECTION 2: Hero Text + Stats -->
    <section class="bg-white py-16 md:py-24 relative overflow-hidden">
        <div class="absolute inset-0 opacity-[0.03]"
            style="background-image: radial-gradient(circle, #0d9488 1px, transparent 1px); background-size: 28px 28px;">
        </div>

        <div class="relative max-w-5xl mx-auto px-4 text-center">

            <!-- Headline: animasi CSS murni, selalu jalan saat halaman dimuat -->
            <div class="animate-fade-in-up">
                <span
                    class="inline-block bg-teal-50 text-teal-600 text-xs font-bold px-4 py-1.5 rounded-full border border-teal-200 uppercase tracking-widest mb-5">
                    🏝️ Dikelola Langsung Oleh Warga Lokal
                </span>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-5">
                    Jelajahi Keindahan<br>
                    <span class="text-teal-600">Pulau Pramuka</span>
                </h1>
                <p class="text-gray-500 text-base md:text-lg max-w-2xl mx-auto mb-8 leading-relaxed">
                    Platform wisata resmi yang dikelola langsung oleh masyarakat Pulau Pramuka. Pesan layanan liburan
                    terbaik, mulai dari Homestay, Snorkeling, hingga Diving langsung dari warga lokal tanpa perantara.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-14">
                    <a href="{{ route('layanan.index') }}" wire:navigate
                        class="bg-teal-600 text-white font-bold px-8 py-3 rounded-xl hover:bg-teal-700 transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5">
                        Lihat Semua Layanan
                    </a>
                    @guest
                        <a href="{{ route('register') }}" wire:navigate
                            class="border-2 border-teal-500 text-teal-600 font-semibold px-8 py-3 rounded-xl hover:bg-teal-50 transition-all">
                            Daftar Gratis
                        </a>
                    @endguest
                </div>
            </div>

            <!-- Stats: Alpine.js x-init countup — re-init otomatis setiap wire:navigate -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 animate-fade-in-up-delay-1">

                {{-- Layanan Tersedia --}}
                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5" x-data="{ display: 0 }" x-init="
                         let target = {{ $stats['total_services'] }};
                         let step = Math.max(target / 80, 0.1), c = 0;
                         let t = setInterval(() => { c = Math.min(c + step, target); display = Math.floor(c); if (c >= target) clearInterval(t); }, 16);
                     ">
                    <div class="text-3xl font-extrabold text-teal-600 mb-1" x-text="display + '+'">
                        {{ $stats['total_services'] }}+</div>
                    <div class="text-gray-400 text-sm">Layanan Tersedia</div>
                </div>

                {{-- Booking Selesai --}}
                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5" x-data="{ display: 0 }" x-init="
                         let target = {{ $stats['total_bookings'] }};
                         let step = Math.max(target / 80, 0.1), c = 0;
                         let t = setInterval(() => { c = Math.min(c + step, target); display = Math.floor(c); if (c >= target) clearInterval(t); }, 16);
                     ">
                    <div class="text-3xl font-extrabold text-teal-600 mb-1" x-text="display + '+'">
                        {{ $stats['total_bookings'] }}+</div>
                    <div class="text-gray-400 text-sm">Booking Selesai</div>
                </div>

                {{-- Rating: statis karena float --}}
                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5">
                    <div class="text-3xl font-extrabold text-teal-600 mb-1">{{ $stats['avg_rating'] }} ⭐</div>
                    <div class="text-gray-400 text-sm">Rating Rata-rata</div>
                </div>

                {{-- Wisatawan Terdaftar --}}
                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5" x-data="{ display: 0 }" x-init="
                         let target = {{ $stats['total_wisatawan'] }};
                         let step = Math.max(target / 80, 0.1), c = 0;
                         let t = setInterval(() => { c = Math.min(c + step, target); display = Math.floor(c); if (c >= target) clearInterval(t); }, 16);
                     ">
                    <div class="text-3xl font-extrabold text-teal-600 mb-1" x-text="display + '+'">
                        {{ $stats['total_wisatawan'] }}+</div>
                    <div class="text-gray-400 text-sm">Wisatawan Terdaftar</div>
                </div>
            </div>
        </div>
    </section>



    <div class="w-full">
        <!-- Garis Atas -->
        <div class="h-1 w-full bg-gradient-to-r from-transparent via-teal-500 to-transparent"></div>

        <section class="bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 py-16">
                <h2 class="text-2xl font-bold text-gray-800 mb-8 text-center">Kategori Wisata</h2>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:flex md:flex-wrap gap-2 sm:gap-3 justify-center">
                    @foreach($categories as $cat)
                        <a href="{{ route('layanan.index', ['categoryId' => $cat->id]) }}" wire:navigate
                            class="flex items-center justify-center gap-1.5 sm:gap-2 bg-teal-50 hover:bg-teal-600 hover:text-white border border-teal-200 text-teal-700 px-3 sm:px-5 py-2 sm:py-2.5 rounded-full transition-all font-medium text-xs sm:text-sm">
                            @if($cat->icon) <span>{{ $cat->icon }}</span> @endif
                            {{ $cat->name }}
                            <span
                                class="text-[10px] sm:text-xs bg-teal-100 text-teal-600 px-1.5 sm:px-2 py-0.5 rounded-full">{{ $cat->service_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Garis Bawah -->
        <div class="h-1 w-full bg-gradient-to-r from-transparent via-teal-500 to-transparent"></div>
    </div>

    <!-- Featured Services -->
    <section class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Layanan Populer</h2>
                <a href="{{ route('layanan.index') }}" class="text-teal-600 hover:underline text-sm" wire:navigate>Lihat
                    semua →</a>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-6">
                @foreach($featuredServices as $service)
                    <a href="{{ route('layanan.show', $service->slug) }}" wire:navigate
                        class="bg-white rounded-xl lg:rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all group">
                        <div class="h-28 lg:h-48 bg-teal-100 overflow-hidden">
                            @if($service->primaryPhoto)
                                <img src="{{ Storage::url($service->primaryPhoto->photo_path) }}" loading="eager"
                                    alt="{{ $service->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-teal-400 text-3xl lg:text-5xl">
                                    🏖️</div>
                            @endif
                        </div>
                        <div class="p-3 lg:p-5">
                            <span
                                class="text-[10px] lg:text-xs bg-teal-100 text-teal-700 px-1.5 lg:px-2 py-0.5 rounded-full font-medium">{{ $service->category->name }}</span>
                            <h3 class="font-bold text-gray-800 mt-1.5 lg:mt-2 mb-1 text-xs lg:text-base line-clamp-2">
                                {{ $service->name }}</h3>
                            <p class="text-gray-500 text-[10px] lg:text-sm line-clamp-1 lg:line-clamp-2">
                                {{ strip_tags($service->description) }}</p>
                            <div class="flex justify-between items-center mt-2 lg:mt-3">
                                <span
                                    class="text-teal-700 font-bold text-xs lg:text-base">{{ $service->formatted_price }}</span>
                                <span class="text-yellow-500 text-[10px] lg:text-sm">⭐
                                    {{ $service->average_rating ?: 'Baru' }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Latest Content -->
    @if($latestContents->isNotEmpty())
        <section class="bg-teal-700 w-full py-16">
            <div class="max-w-7xl mx-auto px-4">
                <h2 class="text-2xl font-bold text-white mb-8">Info & Artikel Terbaru</h2>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6">
                    @foreach($latestContents as $content)
                        <a href="{{ route('konten.show', $content->slug) }}" wire:navigate
                            class="bg-white rounded-xl lg:rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition-all group">
                            @if($content->cover_image)
                                <div class="h-24 lg:h-40 overflow-hidden">
                                    <img src="{{ Storage::url($content->cover_image) }}" loading="lazy" alt="{{ $content->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>
                            @endif
                            <div class="p-3 lg:p-5">
                                <span
                                    class="text-[10px] lg:text-xs bg-blue-100 text-blue-700 px-1.5 lg:px-2 py-0.5 rounded-full">{{ $content->type_label }}</span>
                                <h3 class="font-bold text-gray-800 mt-1.5 lg:mt-2 text-xs lg:text-sm line-clamp-2">
                                    {{ $content->title }}</h3>
                                <p class="text-gray-500 text-[10px] lg:text-sm mt-1">
                                    {{ $content->published_at?->format('d M Y') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Menjadi Mitra Section -->
    <section class="py-12 sm:py-20 text-white">
        <div class="max-w-5xl mx-auto px-4 text-center">
            <span
                class="bg-teal-50 text-teal-600 text-[10px] sm:text-xs font-bold px-3 sm:px-4 py-1 rounded-full border border-teal-200 uppercase tracking-wider">Peluang
                Usaha</span>
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold mt-3 sm:mt-4 mb-3 sm:mb-4 text-gray-700">
                Ingin Menjadi <span class="text-yellow-300">Mitra Wisata</span> Kami?
            </h2>
            <p class="text-gray-700 text-sm sm:text-lg mb-8 sm:mb-12 max-w-2xl mx-auto">
                Daftarkan usaha wisata Anda dan jangkau lebih banyak wisatawan dari seluruh Indonesia melalui platform
                digital Desa Wisata Pulau Pramuka.
            </p>

            <!-- Steps -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-6 mb-8 sm:mb-12">
                <div
                    class="bg-white/10 backdrop-blur rounded-xl sm:rounded-2xl p-3 sm:p-5 text-center hover:bg-white/20 transition-all">
                    <div class="text-2xl sm:text-4xl mb-2 sm:mb-3">📝</div>
                    <div class="text-yellow-300 font-bold text-[10px] sm:text-sm mb-0.5 sm:mb-1">Langkah 1</div>
                    <h3 class="font-bold text-xs sm:text-base mb-0.5 sm:mb-1 text-gray-700">Daftar Akun</h3>
                    <p class="text-gray-500 text-[10px] sm:text-xs hidden sm:block">Isi formulir pendaftaran sebagai
                        Pengelola Wisata dengan data usaha Anda.</p>
                </div>
                <div
                    class="bg-white/10 backdrop-blur rounded-xl sm:rounded-2xl p-3 sm:p-5 text-center hover:bg-white/20 transition-all">
                    <div class="text-2xl sm:text-4xl mb-2 sm:mb-3">🔍</div>
                    <div class="text-yellow-300 font-bold text-[10px] sm:text-sm mb-0.5 sm:mb-1">Langkah 2</div>
                    <h3 class="font-bold text-xs sm:text-base mb-0.5 sm:mb-1 text-gray-700">Verifikasi Admin</h3>
                    <p class="text-gray-500 text-[10px] sm:text-xs hidden sm:block">Tim pengelola desa wisata akan
                        memverifikasi data usaha Anda dalam 1×24 jam.</p>
                </div>
                <div
                    class="bg-white/10 backdrop-blur rounded-xl sm:rounded-2xl p-3 sm:p-5 text-center hover:bg-white/20 transition-all">
                    <div class="text-2xl sm:text-4xl mb-2 sm:mb-3">✅</div>
                    <div class="text-yellow-300 font-bold text-[10px] sm:text-sm mb-0.5 sm:mb-1">Langkah 3</div>
                    <h3 class="font-bold text-xs sm:text-base mb-0.5 sm:mb-1 text-gray-700">Akun Disetujui</h3>
                    <p class="text-gray-500 text-[10px] sm:text-xs hidden sm:block">Anda akan menerima notifikasi email
                        saat akun telah diaktifkan.</p>
                </div>
                <div
                    class="bg-white/10 backdrop-blur rounded-xl sm:rounded-2xl p-3 sm:p-5 text-center hover:bg-white/20 transition-all">
                    <div class="text-2xl sm:text-4xl mb-2 sm:mb-3">🚀</div>
                    <div class="text-yellow-300 font-bold text-[10px] sm:text-sm mb-0.5 sm:mb-1">Langkah 4</div>
                    <h3 class="font-bold text-xs sm:text-base mb-0.5 sm:mb-1 text-gray-700">Kelola Layanan</h3>
                    <p class="text-gray-500 text-[10px] sm:text-xs hidden sm:block">Login ke panel mitra, tambahkan
                        paket wisata, dan mulai terima pesanan!</p>
                </div>
            </div>

            <a href="{{ route('register') }}?role=admin_layanan" wire:navigate
                class="inline-block bg-yellow-400 text-teal-900 font-extrabold px-6 sm:px-10 py-3 sm:py-4 rounded-xl hover:bg-yellow-300 transition-all shadow-lg text-sm sm:text-lg transform hover:-translate-y-1">
                🤝 Daftar sebagai Mitra Sekarang
            </a>
            <p class="text-teal-600 text-xs sm:text-sm mt-3 sm:mt-4">Gratis — Tidak ada biaya pendaftaran</p>
        </div>
    </section>

</div>