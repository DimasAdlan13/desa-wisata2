<div>
    <div class="max-w-7xl mx-auto px-4 py-12">
        <!-- Header + Filters -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Layanan Wisata</h1>
            <p class="text-gray-500">Temukan pengalaman wisata terbaik di Pulau Pramuka</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filter -->
            <aside class="lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-20">
                    <h3 class="font-bold text-gray-700 mb-4">Filter</h3>

                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-600 mb-1 block">Cari Layanan</label>
                        <input wire:model.live.debounce.400ms="search" type="text"
                               placeholder="Snorkeling, Diving..."
                               class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                    </div>

                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-600 mb-1 block">Kategori</label>
                        <select wire:model.live="categoryId"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">Urutkan</label>
                        <select wire:model.live="sortBy"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                            <option value="latest">Terbaru</option>
                            <option value="price_asc">Harga: Murah → Mahal</option>
                            <option value="price_desc">Harga: Mahal → Murah</option>
                            <option value="rating">Rating Tertinggi</option>
                        </select>
                    </div>
                </div>
            </aside>

            <!-- Services Grid -->
            <div class="flex-1">
                <div wire:loading class="text-teal-600 text-sm mb-4 animate-pulse">Memuat...</div>

                @if($services->isEmpty())
                    <div class="text-center py-16 text-gray-400">
                        <div class="text-5xl mb-4">🏖️</div>
                        <p>Tidak ada layanan yang ditemukan.</p>
                        @if($search || $categoryId)
                            <button wire:click="$set('search', ''); $set('categoryId', '')"
                                    class="mt-4 text-teal-600 underline text-sm">Reset filter</button>
                        @endif
                    </div>
                @else
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-5">
                        @foreach($services as $service)
                        <a href="{{ route('layanan.show', $service->slug) }}" wire:navigate
                           class="bg-white rounded-xl lg:rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 overflow-hidden transition-all group">
                            <div class="h-28 lg:h-44 bg-teal-100 overflow-hidden relative">
                                @if($service->primaryPhoto)
                                    <img src="{{ Storage::url($service->primaryPhoto->photo_path) }}"
                                         alt="{{ $service->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-teal-300 text-3xl lg:text-5xl">🏖️</div>
                                @endif
                                <div class="absolute top-2 left-2 lg:top-3 lg:left-3">
                                    <span class="bg-white/90 backdrop-blur text-teal-700 text-[10px] lg:text-xs font-medium px-1.5 lg:px-2.5 py-0.5 lg:py-1 rounded-full">
                                        {{ $service->category->name }}
                                    </span>
                                </div>
                            </div>
                            <div class="p-3 lg:p-4">
                                <h3 class="font-bold text-gray-800 mb-1 text-xs lg:text-base line-clamp-2">{{ $service->name }}</h3>
                                <p class="text-gray-400 text-[10px] lg:text-sm line-clamp-1 lg:line-clamp-2 mb-2 lg:mb-3">{{ strip_tags($service->description) }}</p>
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-teal-700 text-xs lg:text-base">{{ $service->formatted_price }}</span>
                                    <div class="flex items-center gap-1 text-[10px] lg:text-sm text-yellow-500">
                                        ⭐ {{ $service->ratings_avg_rating ? number_format($service->ratings_avg_rating, 1) : 'Baru' }}
                                    </div>
                                </div>
                                <p class="text-[10px] lg:text-xs text-gray-400 mt-1 hidden lg:block">Kuota: {{ $service->quota_per_day }} orang/hari</p>
                            </div>
                        </a>
                        @endforeach
                    </div>

                    <div class="mt-8">{{ $services->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
