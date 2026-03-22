<div>
    <div class="max-w-3xl mx-auto px-4 py-12">
        <div class="mb-6">
            <a href="{{ route('layanan.show', $this->service->slug) }}" wire:navigate
               class="text-sm text-teal-600 hover:underline">← Kembali ke Detail Layanan</a>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="flex items-start gap-4 mb-8">
                @if($this->service->primaryPhoto)
                    <img src="{{ Storage::url($this->service->primaryPhoto->photo_path) }}"
                         alt="{{ $this->service->name }}"
                         class="w-20 h-20 rounded-xl object-cover flex-shrink-0">
                @else
                    <div class="w-20 h-20 rounded-xl bg-teal-100 flex items-center justify-center text-3xl flex-shrink-0">🏖️</div>
                @endif
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Form Booking</h1>
                    <p class="text-teal-700 font-medium">{{ $this->service->name }}</p>
                    <p class="text-gray-500 text-sm">{{ $this->service->formatted_price }} / orang</p>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-50 text-green-700 border border-green-200 rounded-xl p-4 mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <form wire:submit.prevent="submit" class="space-y-6">

                <!-- Tanggal & Pax -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Wisata <span class="text-red-500">*</span></label>
                        <input wire:model.live="bookingDate" type="date"
                               min="{{ now()->toDateString() }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400
                                      @error('bookingDate') border-red-300 @enderror">
                        @error('bookingDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                        <!-- Remaining quota indicator -->
                        @if($bookingDate)
                        <div class="mt-2">
                            @if($this->remainingQuota > 0)
                                <p class="text-green-600 text-xs">✅ Tersedia: {{ $this->remainingQuota }} slot</p>
                            @else
                                <p class="text-red-500 text-xs">❌ Kuota habis untuk tanggal ini</p>
                            @endif
                        </div>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Peserta (Pax) <span class="text-red-500">*</span></label>
                        <input wire:model="pax" type="number" min="1" max="{{ $this->service->quota_per_day }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400
                                      @error('pax') border-red-300 @enderror">
                        @error('pax') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Dynamic Fields (dari form_schema) -->
                @if($this->service->form_schema)
                    <div class="border-t border-gray-100 pt-4">
                        <h3 class="font-semibold text-gray-700 mb-4">Informasi Tambahan</h3>
                        @foreach($this->service->form_schema as $fieldKey => $fieldLabel)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ $fieldLabel }} <span class="text-red-500">*</span>
                            </label>
                            @if(str_contains(strtolower($fieldLabel), 'keterangan') || str_contains(strtolower($fieldLabel), 'catatan'))
                                <textarea wire:model="dynamicFields.{{ $fieldKey }}" rows="3"
                                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400
                                                 @error('dynamicFields.'.$fieldKey) border-red-300 @enderror"></textarea>
                            @else
                                <input type="text" wire:model="dynamicFields.{{ $fieldKey }}"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400
                                              @error('dynamicFields.'.$fieldKey) border-red-300 @enderror">
                            @endif
                            @error('dynamicFields.'.$fieldKey)
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        @endforeach
                    </div>
                @endif

                <!-- Total Harga -->
                @if($pax && $bookingDate)
                <div class="bg-teal-50 border border-teal-200 rounded-xl p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-teal-700">Total Pembayaran</span>
                        <span class="text-xl font-bold text-teal-700">
                            Rp {{ number_format($this->service->price * $pax, 0, ',', '.') }}
                        </span>
                    </div>
                    <p class="text-teal-500 text-xs mt-1">{{ $pax }} orang × {{ $this->service->formatted_price }}</p>
                </div>
                @endif

                <!-- Info Pembayaran -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <p class="text-yellow-800 text-sm font-medium">💳 Info Pembayaran</p>
                    <p class="text-yellow-700 text-sm mt-1">Pembayaran dilakukan via transfer bank. Bukti pembayaran diunggah di halaman detail booking setelah booking dibuat.</p>
                </div>

                <button type="submit"
                        wire:loading.attr="disabled"
                        class="w-full bg-teal-600 text-white font-bold py-3 rounded-xl hover:bg-teal-700 transition-colors
                               disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove>🎉 Buat Booking Sekarang</span>
                    <span wire:loading>Memproses...</span>
                </button>
            </form>
        </div>
    </div>
</div>
