<div>
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Masuk</h2>
            <p class="text-gray-500 text-sm mt-1">Belum punya akun? <a href="{{ route('register') }}" wire:navigate class="text-teal-600 hover:underline">Daftar sekarang</a></p>
        </div>

        @if(session('info'))
            <div class="bg-blue-50 text-blue-700 border border-blue-200 rounded-xl p-3 text-sm">{{ session('info') }}</div>
        @endif

        <form wire:submit.prevent="login" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input wire:model="email" type="email" placeholder="email@kamu.com"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400 @error('email') border-red-300 @enderror">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input wire:model="password" type="password" placeholder="••••••••"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400 @error('password') border-red-300 @enderror">
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-center gap-2">
                <input wire:model="remember" type="checkbox" id="remember" class="rounded text-teal-600">
                <label for="remember" class="text-sm text-gray-600">Ingat saya</label>
            </div>
            <button type="submit"
                    class="w-full bg-teal-600 text-white font-bold py-3 rounded-xl hover:bg-teal-700 transition-colors">
                <span wire:loading.remove>Masuk</span>
                <span wire:loading>Memproses...</span>
            </button>
        </form>
    </div>
</div>
