<div>
    <!-- Hero Image / Gallery -->
    <div class="w-full bg-teal-900 h-64 md:h-96 relative">
        @if($service->primaryPhoto)
            <img src="{{ Storage::url($service->primaryPhoto->photo_path) }}" alt="{{ $service->name }}" class="w-full h-full object-cover opacity-80">
        @else
            <div class="w-full h-full flex items-center justify-center text-teal-300 text-6xl">🏖️</div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 to-transparent"></div>
        <div class="absolute bottom-0 left-0 w-full p-6 md:p-12">
            <div class="max-w-7xl mx-auto">
                <span class="bg-teal-500 text-white px-3 py-1 rounded-full text-sm font-semibold mb-3 inline-block">{{ $service->category->name }}</span>
                <h1 class="text-3xl md:text-5xl font-bold text-white mb-2">{{ $service->name }}</h1>
                <p class="text-teal-100 flex items-center gap-2">📍 {{ $service->location ?? 'Kepulauan Seribu' }}</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="bg-white/50 backdrop-blur pb-4 mb-4">
           <a href="{{ route('layanan.index') }}" wire:navigate class="text-teal-600 hover:underline text-sm font-medium">← Kembali ke Layanan</a>
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
                            <a href="{{ Storage::url($photo->photo_path) }}" target="_blank" class="block h-32 md:h-48 overflow-hidden rounded-xl">
                                <img src="{{ Storage::url($photo->photo_path) }}" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
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
                            <span class="text-xl font-bold text-gray-800">{{ $avgRating ? number_format($avgRating, 1) : '-' }}</span>
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
                                        <div class="w-10 h-10 bg-teal-100 text-teal-700 rounded-full flex items-center justify-center font-bold">
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
                        <h3 class="text-3xl font-extrabold text-teal-700">{{ $service->formatted_price }} <span class="text-sm font-normal text-gray-500">/ orang</span></h3>
                    </div>

                    <div class="space-y-4 mb-8">
                        <div class="flex items-center gap-3 text-gray-600">
                            <span class="text-xl">👥</span>
                            <div>
                                <p class="text-sm text-gray-500">Kuota Maksimal</p>
                                <p class="font-semibold">{{ $service->quota_per_day }} Pax / hari</p>
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
                                🎫 Booking Sekarang
                            </a>
                        @else
                            <div class="bg-blue-50 text-blue-800 px-4 py-3 rounded-xl text-center text-sm font-medium">
                                Anda login sebagai Admin. Hanya Wisatawan yang bisa membuat Booking.
                            </div>
                        @endif
                    @else
                        <button wire:click="redirectToLogin" wire:confirm="Anda harus login terlebih dahulu untuk membuat pesanan. Lanjut ke halaman login?"
                           class="block w-full text-center bg-yellow-400 text-yellow-900 font-bold px-6 py-4 rounded-xl hover:bg-yellow-300 transition-colors shadow-md transform hover:-translate-y-1">
                            🎫 Pesan Sekarang
                        </button>
                        <p class="text-center text-xs text-gray-500 mt-4">Belum punya akun? <a href="{{ route('register') }}" class="text-teal-600 hover:underline">Daftar</a></p>
                    @endauth

                </div>
            </aside>
        </div>
    </div>
</div>
