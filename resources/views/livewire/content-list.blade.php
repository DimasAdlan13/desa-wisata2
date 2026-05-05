<div>
    <div class="max-w-7xl mx-auto px-4 py-12">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 mb-6 lg:mb-8">Konten Wisata</h1>

        <div class="flex gap-2 lg:gap-3 mb-6 lg:mb-8 overflow-x-auto pb-2">
            <a href="{{ route('konten.index') }}" wire:navigate
               class="px-3 lg:px-4 py-1.5 lg:py-2 rounded-full text-xs lg:text-sm font-medium whitespace-nowrap {{ $type === '' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Semua</a>
            <a href="{{ route('konten.index', ['type' => 'umkm']) }}" wire:navigate
               class="px-3 lg:px-4 py-1.5 lg:py-2 rounded-full text-xs lg:text-sm font-medium whitespace-nowrap {{ $type === 'umkm' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">UMKM</a>
            <a href="{{ route('konten.index', ['type' => 'kuliner']) }}" wire:navigate
               class="px-3 lg:px-4 py-1.5 lg:py-2 rounded-full text-xs lg:text-sm font-medium whitespace-nowrap {{ $type === 'kuliner' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Kuliner</a>
            <a href="{{ route('konten.index', ['type' => 'info_wisata']) }}" wire:navigate
               class="px-3 lg:px-4 py-1.5 lg:py-2 rounded-full text-xs lg:text-sm font-medium whitespace-nowrap {{ $type === 'info_wisata' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Info Wisata</a>
        </div>

        @if($contents->isEmpty())
            <div class="text-center py-16 text-gray-400"><p>Belum ada konten.</p></div>
        @else
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-6">
                @foreach($contents as $content)
                <a href="{{ route('konten.show', $content->slug) }}" wire:navigate
                   class="bg-white rounded-xl lg:rounded-2xl shadow-sm hover:shadow-lg overflow-hidden transition-all group">
                    @if($content->cover_image)
                        <div class="h-24 lg:h-44 overflow-hidden">
                            <img src="{{ Storage::url($content->cover_image) }}" alt="{{ $content->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                    @else
                        <div class="h-24 lg:h-44 bg-gradient-to-br from-teal-400 to-blue-500 flex items-center justify-center text-white text-3xl lg:text-5xl">📄</div>
                    @endif
                    <div class="p-3 lg:p-5">
                        <span class="text-[10px] lg:text-xs bg-blue-100 text-blue-700 px-1.5 lg:px-2.5 py-0.5 rounded-full">{{ $content->type_label }}</span>
                        <h3 class="font-bold text-gray-800 mt-1.5 lg:mt-2 text-xs lg:text-base line-clamp-2">{{ $content->title }}</h3>
                        <p class="text-gray-400 text-[10px] lg:text-sm mt-1">{{ $content->published_at?->format('d M Y') }}</p>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="mt-8">{{ $contents->links() }}</div>
        @endif
    </div>
</div>
