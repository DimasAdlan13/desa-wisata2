<div>
    <div class="max-w-7xl mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Konten Wisata</h1>

        <div class="flex gap-3 mb-8">
            <a href="{{ route('konten.index') }}" wire:navigate
               class="px-4 py-2 rounded-full text-sm font-medium {{ $type === '' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Semua</a>
            <a href="{{ route('konten.index', ['type' => 'umkm']) }}" wire:navigate
               class="px-4 py-2 rounded-full text-sm font-medium {{ $type === 'umkm' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">UMKM</a>
            <a href="{{ route('konten.index', ['type' => 'kuliner']) }}" wire:navigate
               class="px-4 py-2 rounded-full text-sm font-medium {{ $type === 'kuliner' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Kuliner</a>
            <a href="{{ route('konten.index', ['type' => 'info_wisata']) }}" wire:navigate
               class="px-4 py-2 rounded-full text-sm font-medium {{ $type === 'info_wisata' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Info Wisata</a>
        </div>

        @if($contents->isEmpty())
            <div class="text-center py-16 text-gray-400"><p>Belum ada konten.</p></div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($contents as $content)
                <a href="{{ route('konten.show', $content->slug) }}" wire:navigate
                   class="bg-white rounded-2xl shadow-sm hover:shadow-lg overflow-hidden transition-all group">
                    @if($content->cover_image)
                        <div class="h-44 overflow-hidden">
                            <img src="{{ Storage::url($content->cover_image) }}" alt="{{ $content->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                    @else
                        <div class="h-44 bg-gradient-to-br from-teal-400 to-blue-500 flex items-center justify-center text-white text-5xl">📄</div>
                    @endif
                    <div class="p-5">
                        <span class="text-xs bg-blue-100 text-blue-700 px-2.5 py-0.5 rounded-full">{{ $content->type_label }}</span>
                        <h3 class="font-bold text-gray-800 mt-2 line-clamp-2">{{ $content->title }}</h3>
                        <p class="text-gray-400 text-sm mt-1">{{ $content->published_at?->format('d M Y') }}</p>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="mt-8">{{ $contents->links() }}</div>
        @endif
    </div>
</div>
