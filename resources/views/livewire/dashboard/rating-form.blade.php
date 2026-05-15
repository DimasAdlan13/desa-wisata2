<div>
    <div class="max-w-xl mx-auto px-4 py-12">
        <a href="{{ route('dashboard') }}" wire:navigate class="text-sm text-teal-600 hover:underline">← Kembali ke Dashboard</a>

        <div class="bg-white rounded-2xl shadow-lg p-8 mt-6">
            <h1 class="text-xl font-bold text-gray-800 mb-1">Beri Rating</h1>
            <p class="text-gray-500 text-sm">{{ $booking->service->name }}</p>

            <form wire:submit.prevent="submit" class="mt-6 space-y-6">
                <!-- Star Rating -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Rating</label>
                    <div class="flex gap-2">
                        @for($i = 1; $i <= 5; $i++)
                        <button type="button" wire:click="setRating({{ $i }})"
                                class="text-4xl transition-transform hover:scale-110 focus:outline-none"
                                title="{{ $i }} bintang">
                            {{ $rating >= $i ? '⭐' : '☆' }}
                        </button>
                        @endfor
                    </div>
                    <p class="text-xs text-gray-400 mt-2">
                        @switch($rating)
                            @case(1) 😞 Sangat Buruk @break
                            @case(2) 😐 Buruk @break
                            @case(3) 🙂 Cukup @break
                            @case(4) 😊 Bagus @break
                            @case(5) 🤩 Luar Biasa! @break
                        @endswitch
                    </p>
                    @error('rating') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Review Text -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ulasan (Opsional)</label>
                    <textarea wire:model="review" rows="4"
                              placeholder="Bagikan pengalaman wisatamu..."
                              class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 @error('review') border-red-300 @enderror"></textarea>
                    @error('review') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                        class="w-full bg-yellow-400 text-yellow-900 font-bold py-3 rounded-xl hover:bg-yellow-300 transition-colors">
                    <span wire:loading.remove> Kirim Rating</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </form>
        </div>
    </div>
</div>
