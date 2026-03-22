<div>
    <div class="max-w-4xl mx-auto px-4 py-12">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard Saya</h1>

        <!-- Tabs -->
        <div class="flex gap-2 border-b border-gray-200 mb-6">
            @foreach([['active', 'Booking Aktif'], ['history', 'Riwayat'], ['profile', 'Profil']] as [$tab, $label])
            <button wire:click="setTab('{{ $tab }}')"
                    class="px-5 py-2.5 text-sm font-medium transition-all rounded-t-xl
                           {{ $activeTab === $tab ? 'bg-teal-600 text-white' : 'text-gray-500 hover:text-teal-600' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>

        <!-- Booking Aktif -->
        @if($activeTab === 'active')
            @if($activeBookings->isEmpty())
                <div class="text-center py-12 text-gray-400">
                    <div class="text-5xl mb-3">📅</div>
                    <p>Belum ada booking aktif.</p>
                    <a href="{{ route('layanan.index') }}" wire:navigate class="mt-4 inline-block text-teal-600 underline text-sm">Cari Layanan</a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($activeBookings as $booking)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex gap-4">
                        @if($booking->service->primaryPhoto)
                            <img src="{{ Storage::url($booking->service->primaryPhoto->photo_path) }}"
                                 class="w-20 h-20 object-cover rounded-xl flex-shrink-0">
                        @else
                            <div class="w-20 h-20 bg-teal-100 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">🏖️</div>
                        @endif
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-bold text-gray-800">{{ $booking->service->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $booking->booking_code }} · {{ $booking->booking_date->format('d M Y') }}</p>
                                    <p class="text-sm text-gray-500">{{ $booking->pax }} orang · {{ $booking->formatted_total_price }}</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-medium capitalize
                                    {{ match($booking->status) {
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'confirmed' => 'bg-blue-100 text-blue-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    } }}">
                                    {{ $booking->status }}
                                </span>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('dashboard.booking', $booking) }}" wire:navigate
                                   class="text-sm text-teal-600 hover:underline font-medium">Lihat Detail & Upload Bukti Bayar →</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div>{{ $activeBookings->links() }}</div>
                </div>
            @endif
        @endif

        <!-- Riwayat -->
        @if($activeTab === 'history')
            @if($historyBookings->isEmpty())
                <div class="text-center py-12 text-gray-400">
                    <div class="text-5xl mb-3">📋</div>
                    <p>Belum ada riwayat booking.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($historyBookings as $booking)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex gap-4">
                        @if($booking->service->primaryPhoto)
                            <img src="{{ Storage::url($booking->service->primaryPhoto->photo_path) }}"
                                 class="w-20 h-20 object-cover rounded-xl flex-shrink-0">
                        @else
                            <div class="w-20 h-20 bg-gray-100 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">📋</div>
                        @endif
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-bold text-gray-800">{{ $booking->service->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $booking->booking_code }} · {{ $booking->booking_date->format('d M Y') }}</p>
                                    <p class="text-sm font-medium text-gray-700 mt-1">{{ $booking->formatted_total_price }}</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-medium capitalize
                                    {{ match($booking->status) {
                                        'completed' => 'bg-green-100 text-green-700',
                                        'cancelled' => 'bg-gray-100 text-gray-600',
                                        'rejected'  => 'bg-red-100 text-red-700',
                                        default     => 'bg-gray-100 text-gray-600'
                                    } }}">
                                    {{ $booking->status }}
                                </span>
                            </div>
                            @if($booking->status === 'completed')
                                @if(!$booking->rating)
                                    <a href="{{ route('dashboard.rating', $booking) }}" wire:navigate
                                       class="mt-2 inline-block text-sm bg-yellow-400 text-yellow-900 font-medium px-4 py-1.5 rounded-lg hover:bg-yellow-300 transition">
                                        ⭐ Beri Rating
                                    </a>
                                @else
                                    <p class="mt-2 text-sm text-gray-500">
                                        Rating: {{ str_repeat('⭐', $booking->rating->rating) }}
                                        <span class="text-gray-400">"{{ $booking->rating->review }}"</span>
                                    </p>
                                @endif
                            @endif
                        </div>
                    </div>
                    @endforeach
                    <div>{{ $historyBookings->links() }}</div>
                </div>
            @endif
        @endif

        <!-- Profil -->
        @if($activeTab === 'profile')
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-md">
                <h3 class="font-bold text-gray-700 mb-4">Informasi Akun</h3>
                <div class="space-y-3 text-sm">
                    <div><span class="text-gray-500 w-28 inline-block">Nama</span><span class="font-medium">{{ auth()->user()->name }}</span></div>
                    <div><span class="text-gray-500 w-28 inline-block">Email</span><span>{{ auth()->user()->email }}</span></div>
                    <div><span class="text-gray-500 w-28 inline-block">No. HP</span><span>{{ auth()->user()->phone ?? '-' }}</span></div>
                    <div><span class="text-gray-500 w-28 inline-block">Role</span>
                        <span class="bg-teal-100 text-teal-700 px-2 py-0.5 rounded-full text-xs font-medium">{{ auth()->user()->role }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
