<div>
    <div class="max-w-2xl mx-auto px-4 py-12">
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" wire:navigate class="text-sm text-teal-600 hover:underline">← Kembali ke Dashboard</a>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h1 class="text-xl font-bold text-gray-800 mb-1">Detail Booking</h1>
            <p class="text-gray-400 text-sm">{{ $booking->booking_code }}</p>

            @if(session('success'))
                <div class="mt-4 bg-green-50 text-green-700 border border-green-200 rounded-xl p-3 text-sm">{{ session('success') }}</div>
            @endif

            <div class="mt-6 space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Layanan</span><span class="font-medium">{{ $booking->service->name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Tanggal</span><span>{{ $booking->booking_date->format('d M Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Peserta</span><span>{{ $booking->pax }} orang</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Total</span><span class="font-bold text-teal-700">{{ $booking->formatted_total_price }}</span></div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Status</span>
                    <span class="px-3 py-1 rounded-full text-xs font-medium capitalize
                        {{ match($booking->status) {
                            'pending'   => 'bg-yellow-100 text-yellow-700',
                            'confirmed' => 'bg-blue-100 text-blue-700',
                            'completed' => 'bg-green-100 text-green-700',
                            'cancelled' => 'bg-gray-100 text-gray-600',
                            'rejected'  => 'bg-red-100 text-red-700',
                            default     => 'bg-gray-100 text-gray-600'
                        } }}">{{ $booking->status }}</span>
                </div>

                @if($booking->booking_details)
                    <div class="pt-3 border-t border-gray-100">
                        <p class="text-gray-600 font-medium mb-2">Detail Pesanan</p>
                        @foreach($booking->booking_details as $key => $value)
                            <div class="flex justify-between">
                                <span class="text-gray-500 capitalize">{{ str_replace('_', ' ', $key) }}</span>
                                <span>{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($booking->rejection_reason)
                    <div class="bg-red-50 border border-red-200 rounded-xl p-3">
                        <p class="text-red-600 text-xs font-medium">Alasan Penolakan</p>
                        <p class="text-red-700 text-sm mt-1">{{ $booking->rejection_reason }}</p>
                    </div>
                @endif
            </div>

            <!-- Payment Upload (hanya untuk pending) -->
            @if($booking->isPending())
            <div class="mt-8 border-t border-gray-100 pt-6">
                <h3 class="font-semibold text-gray-700 mb-1">Upload Bukti Pembayaran</h3>
                <p class="text-gray-400 text-xs mb-4">Format: JPG/PNG, max 2MB. Transfer ke BRI: 1234-5678-9012 a/n Desa Wisata Kepulauan Seribu</p>

                <form wire:submit.prevent="uploadPaymentProof" class="space-y-4">
                    <div>
                        <input wire:model="paymentProof" type="file" accept="image/*"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                        @error('paymentProof') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div wire:loading wire:target="paymentProof" class="text-teal-600 text-xs animate-pulse">Mengupload...</div>

                    @if($booking->payment_proof)
                        <div class="text-green-600 text-sm flex items-center gap-2">
                            ✅ Bukti bayar sudah diupload.
                            <a href="{{ Storage::url($booking->payment_proof) }}" target="_blank" class="underline text-xs">Lihat</a>
                        </div>
                    @endif

                    <button type="submit"
                            class="bg-teal-600 text-white font-semibold px-6 py-2.5 rounded-xl hover:bg-teal-700 transition-colors text-sm">
                        <span wire:loading.remove>Upload Bukti Bayar</span>
                        <span wire:loading>Mengupload...</span>
                    </button>
                </form>

                <!-- Cancel -->
                <div class="mt-4">
                    <button wire:click="cancelBooking" wire:confirm="Apakah kamu yakin ingin membatalkan booking ini?"
                            class="text-red-500 text-sm hover:underline">
                        Batalkan Booking
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
