<div>
    <div class="max-w-3xl mx-auto px-4 py-12">
        <a href="{{ route('konten.index') }}" wire:navigate class="text-sm text-teal-600 hover:underline mb-6 block">← Kembali</a>

        <article class="bg-white rounded-2xl shadow-sm p-8">
            @if($content->cover_image)
                <img src="{{ Storage::url($content->cover_image) }}" alt="{{ $content->title }}"
                     class="w-full h-64 object-cover rounded-xl mb-6">
            @endif
            <span class="text-xs bg-blue-100 text-blue-700 px-2.5 py-0.5 rounded-full">{{ $content->type_label }}</span>
            <h1 class="text-3xl font-bold text-gray-800 mt-3 mb-2">{{ $content->title }}</h1>
            <p class="text-gray-400 text-sm mb-6">{{ $content->published_at?->format('d M Y') }}</p>
            <div class="prose prose-teal max-w-none">{!! $content->body !!}</div>
        </article>
    </div>
</div>
