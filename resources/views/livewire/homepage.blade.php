<div>
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-teal-700 via-teal-600 to-blue-700 text-white overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%220.4%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
        <div class="relative max-w-7xl mx-auto px-4 py-24 text-center">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-4 leading-tight">
                Jelajahi Keindahan<br><span class="text-yellow-300">Kepulauan Seribu</span>
            </h1>
            <p class="text-lg md:text-xl text-teal-100 mb-8 max-w-2xl mx-auto">
                Booking layanan wisata terpercaya di Pulau Pramuka. Snorkeling, Diving, Homestay, Kuliner & lebih banyak lagi.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('layanan.index') }}" wire:navigate
                   class="bg-yellow-400 text-teal-900 font-bold px-8 py-3 rounded-xl hover:bg-yellow-300 transition-all shadow-lg">
                    🌊 Lihat Semua Layanan
                </a>
                @guest
                <a href="{{ route('register') }}" wire:navigate
                   class="border-2 border-white text-white font-semibold px-8 py-3 rounded-xl hover:bg-white hover:text-teal-700 transition-all">
                    Daftar Gratis
                </a>
                @endguest
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="bg-white py-10 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div>
                <div class="text-3xl font-bold text-teal-600">{{ $stats['total_services'] }}+</div>
                <div class="text-gray-500 text-sm mt-1">Layanan Tersedia</div>
            </div>
            <div>
                <div class="text-3xl font-bold text-teal-600">{{ $stats['total_bookings'] }}+</div>
                <div class="text-gray-500 text-sm mt-1">Booking Selesai</div>
            </div>
            <div>
                <div class="text-3xl font-bold text-teal-600">{{ $stats['avg_rating'] }}⭐</div>
                <div class="text-gray-500 text-sm mt-1">Rating Rata-rata</div>
            </div>
            <div>
                <div class="text-3xl font-bold text-teal-600">{{ $stats['total_wisatawan'] }}+</div>
                <div class="text-gray-500 text-sm mt-1">Wisatawan Terdaftar</div>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="max-w-7xl mx-auto px-4 py-16">
        <h2 class="text-2xl font-bold text-gray-800 mb-8 text-center">Kategori Wisata</h2>
        <div class="flex flex-wrap gap-3 justify-center">
            @foreach($categories as $cat)
            <a href="{{ route('layanan.index', ['categoryId' => $cat->id]) }}" wire:navigate
               class="flex items-center gap-2 bg-teal-50 hover:bg-teal-600 hover:text-white border border-teal-200 text-teal-700 px-5 py-2.5 rounded-full transition-all font-medium">
                @if($cat->icon) <span>{{ $cat->icon }}</span> @endif
                {{ $cat->name }}
                <span class="text-xs bg-teal-100 hover:bg-teal-500 text-teal-600 hover:text-white px-2 py-0.5 rounded-full">{{ $cat->service_count }}</span>
            </a>
            @endforeach
        </div>
    </section>

    <!-- Featured Services -->
    <section class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Layanan Populer</h2>
                <a href="{{ route('layanan.index') }}" class="text-teal-600 hover:underline text-sm" wire:navigate>Lihat semua →</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredServices as $service)
                <a href="{{ route('layanan.show', $service->slug) }}" wire:navigate
                   class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all group">
                    <div class="h-48 bg-teal-100 overflow-hidden">
                        @if($service->primaryPhoto)
                            <img src="{{ Storage::url($service->primaryPhoto->photo_path) }}"
                                 alt="{{ $service->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-teal-400 text-5xl">🏖️</div>
                        @endif
                    </div>
                    <div class="p-5">
                        <span class="text-xs bg-teal-100 text-teal-700 px-2 py-0.5 rounded-full font-medium">{{ $service->category->name }}</span>
                        <h3 class="font-bold text-gray-800 mt-2 mb-1">{{ $service->name }}</h3>
                        <p class="text-gray-500 text-sm line-clamp-2">{{ strip_tags($service->description) }}</p>
                        <div class="flex justify-between items-center mt-3">
                            <span class="text-teal-700 font-bold">{{ $service->formatted_price }}</span>
                            <span class="text-yellow-500 text-sm">⭐ {{ $service->average_rating ?: 'Baru' }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Latest Content -->
    @if($latestContents->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 py-16">
        <h2 class="text-2xl font-bold text-gray-800 mb-8">Info & Artikel Terbaru</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($latestContents as $content)
            <a href="{{ route('konten.show', $content->slug) }}" wire:navigate
               class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition-all group">
                @if($content->cover_image)
                    <div class="h-40 overflow-hidden">
                        <img src="{{ Storage::url($content->cover_image) }}" alt="{{ $content->title }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                @endif
                <div class="p-5">
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">{{ $content->type_label }}</span>
                    <h3 class="font-bold text-gray-800 mt-2 line-clamp-2">{{ $content->title }}</h3>
                    <p class="text-gray-500 text-sm mt-1">{{ $content->published_at?->format('d M Y') }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif
</div>
