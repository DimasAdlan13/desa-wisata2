<div>
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Lupa Password?</h2>
            <p class="text-gray-500 text-sm mt-1">Masukkan email kamu dan kami akan kirimkan link untuk membuat password baru.</p>
        </div>

        @if($sent)
            <div class="bg-teal-50 border border-teal-200 rounded-xl p-4 text-sm text-teal-700">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-medium">Email terkirim!</p>
                        <p class="mt-1 text-teal-600">Cek inbox atau folder spam kamu. Link reset password berlaku selama 60 menit.</p>
                    </div>
                </div>
            </div>
        @endif

        @if(!$sent)
        <form wire:submit.prevent="sendResetLink" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input wire:model="email" type="email" placeholder="email@kamu.com"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400 @error('email') border-red-300 @enderror">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                    class="w-full bg-teal-600 text-white font-bold py-3 rounded-xl hover:bg-teal-700 transition-colors">
                <span wire:loading.remove>Kirim Link Reset Password</span>
                <span wire:loading>Mengirim...</span>
            </button>
        </form>
        @endif

        <p class="text-center text-sm text-gray-500">
            Ingat password kamu?
            <a href="{{ route('login') }}" wire:navigate class="text-teal-600 hover:underline font-medium">Masuk di sini</a>
        </p>
    </div>
</div>
