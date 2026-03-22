<div>
    <div class="space-y-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Akun</h2>
            <p class="text-gray-500 text-sm mt-1">Sudah punya akun? <a href="{{ route('login') }}" wire:navigate class="text-teal-600 hover:underline">Masuk</a></p>
        </div>

        <form wire:submit.prevent="register" class="space-y-4">
            <!-- Role Selector -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Daftar sebagai</label>
                <div class="grid grid-cols-2 gap-3">
                    <label wire:click="$set('role', 'wisatawan')"
                           class="cursor-pointer border-2 rounded-xl p-3 text-center transition-all
                                  {{ $role === 'wisatawan' ? 'border-teal-600 bg-teal-50 text-teal-700' : 'border-gray-200 text-gray-500' }}">
                        <div class="text-xl mb-1">🏖️</div>
                        <div class="text-sm font-medium">Wisatawan</div>
                    </label>
                    <label wire:click="$set('role', 'admin_layanan')"
                           class="cursor-pointer border-2 rounded-xl p-3 text-center transition-all
                                  {{ $role === 'admin_layanan' ? 'border-teal-600 bg-teal-50 text-teal-700' : 'border-gray-200 text-gray-500' }}">
                        <div class="text-xl mb-1">🏢</div>
                        <div class="text-sm font-medium">Admin Layanan</div>
                    </label>
                </div>
                @if($role === 'admin_layanan')
                    <p class="text-xs text-yellow-600 mt-2 bg-yellow-50 border border-yellow-200 rounded-lg p-2">
                        ⚠️ Akun Admin Layanan membutuhkan persetujuan Super Admin sebelum bisa digunakan.
                    </p>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input wire:model="name" type="text" placeholder="Nama kamu"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400 @error('name') border-red-300 @enderror">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                    <input wire:model="phone" type="text" placeholder="08xx"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input wire:model="email" type="email" placeholder="email@kamu.com"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400 @error('email') border-red-300 @enderror">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input wire:model="password" type="password" placeholder="Min. 8 karakter"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400 @error('password') border-red-300 @enderror">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <input wire:model="passwordConfirm" type="password" placeholder="Ulangi password"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400">
                </div>
            </div>

            <!-- Business Fields (Admin Layanan only) -->
            @if($role === 'admin_layanan')
            <div class="space-y-4 border-t border-gray-100 pt-4">
                <h3 class="font-medium text-gray-700">Info Bisnis</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bisnis</label>
                    <input wire:model="businessName" type="text" placeholder="Nama usaha kamu"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400 @error('businessName') border-red-300 @enderror">
                    @error('businessName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <input wire:model="businessAddress" type="text" placeholder="Alamat lengkap"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400 @error('businessAddress') border-red-300 @enderror">
                    @error('businessAddress') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Bisnis</label>
                    <textarea wire:model="businessDesc" rows="2" placeholder="Ceritakan bisnis kamu..."
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400"></textarea>
                </div>
            </div>
            @endif

            <button type="submit"
                    class="w-full bg-teal-600 text-white font-bold py-3 rounded-xl hover:bg-teal-700 transition-colors">
                <span wire:loading.remove>Daftar Sekarang</span>
                <span wire:loading>Mendaftarkan...</span>
            </button>
        </form>
    </div>
</div>
