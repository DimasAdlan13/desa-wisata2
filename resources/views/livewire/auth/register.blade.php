<div>
    <div class="space-y-4">
        @if($role === 'admin_layanan')
            <!-- Mitra Header -->
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="bg-teal-100 text-teal-700 text-xs font-bold px-3 py-1 rounded-full">🤝 Pendaftaran
                        Mitra</span>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Daftar sebagai Pengelola Wisata</h2>
                <p class="text-gray-500 text-sm mt-1">Sudah punya akun? <a href="{{ route('login') }}" wire:navigate
                        class="text-teal-600 hover:underline">Masuk</a></p>
            </div>
        @else
            <!-- Wisatawan Header -->
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Daftar Akun</h2>
                <p class="text-gray-500 text-sm mt-1">Sudah punya akun? <a href="{{ route('login') }}" wire:navigate
                        class="text-teal-600 hover:underline">Masuk</a></p>
            </div>
        @endif

        <form wire:submit.prevent="register" class="space-y-4">

            {{-- ==================================== --}}
            {{--             LANGKAH 1                --}}
            {{-- ==================================== --}}
            @if($step === 1)
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-bold text-teal-600 bg-teal-50 px-2 py-1 rounded-md">Langkah 1 dari 2</span>
                        <span class="text-xs text-gray-500 font-medium">Informasi Dasar</span>
                    </div>
                    <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-teal-500 w-1/2 rounded-full"></div>
                    </div>
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

            {{-- =====================================================
            DROPDOWN PROVINSI & KOTA — Khusus Wisatawan
            =====================================================
            Provinsi : fetch dari file lokal /data/provinces.json
            → instan, tidak butuh internet
            Kota/Kab : pertama kali fetch dari API emsifa,
            lalu disimpan ke localStorage browser
            → kunjungan berikutnya instan dari cache
            ===================================================== --}}
            @if($role === 'wisatawan')
                <div x-data="{
                                    provinces: [],
                                    cities: [],
                                    selectedProvinceId: '',
                                    selectedProvinceName: '',
                                    loadingProvince: true,
                                    loadingCity: false,
                                    init() {
                                        fetch('/data/provinces.json')
                                            .then(r => r.json())
                                            .then(data => {
                                                this.provinces = data;
                                                this.loadingProvince = false;
                                            });
                                    },
                                    selectProvince(id, name) {
                                        this.selectedProvinceId = id;
                                        this.selectedProvinceName = name;
                                        this.cities = [];
                                        this.loadingCity = true;

                                        const cacheKey = 'regencies_' + id;
                                        const cached = localStorage.getItem(cacheKey);
                                        if (cached) {
                                            this.cities = JSON.parse(cached);
                                            this.loadingCity = false;
                                        } else {
                                            // Fetch ke proxy Laravel kita sendiri (bukan emsifa langsung)
                                            // → Tidak ada CORS issue, server yang ambil data dari emsifa
                                            fetch('/api/wilayah/regencies/' + id)
                                                .then(r => {
                                                    if (!r.ok) throw new Error('HTTP ' + r.status);
                                                    return r.json();
                                                })
                                                .then(data => {
                                                    localStorage.setItem(cacheKey, JSON.stringify(data));
                                                    this.cities = data;
                                                    this.loadingCity = false;
                                                })
                                                .catch(err => {
                                                    console.error('Gagal memuat kota:', err.message);
                                                    this.loadingCity = false;
                                                    this.cities = [];
                                                    localStorage.removeItem(cacheKey);
                                                    alert('Gagal memuat data kota. Silakan coba pilih provinsi kembali.');
                                                });
                                        }
                                    },
                                    selectCity(name) {
                                        // Sync province + city ke Livewire sekaligus — render hanya 1x
                                        $wire.set('province', this.selectedProvinceName);
                                        $wire.set('city', name);
                                    }
                                }" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Provinsi Asal <span class="text-red-500">*</span>
                        </label>
                        <select
                            x-on:change="selectProvince($event.target.value, $event.target.options[$event.target.selectedIndex].text)"
                            :disabled="loadingProvince"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400 disabled:bg-gray-100 @error('province') border-red-300 @enderror">
                            <option value="" x-text="loadingProvince ? 'Memuat provinsi...' : 'Pilih Provinsi'"></option>
                            <template x-for="prov in provinces" :key="prov.id">
                                <option :value="prov.id" x-text="prov.name"></option>
                            </template>
                        </select>
                        @error('province') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Kabupaten/Kota <span class="text-red-500">*</span>
                        </label>
                        <select x-on:change="selectCity($event.target.options[$event.target.selectedIndex].text)"
                            :disabled="!selectedProvinceId || loadingCity"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400 disabled:bg-gray-100 @error('city') border-red-300 @enderror">
                            <option value=""
                                x-text="!selectedProvinceId ? 'Pilih provinsi dahulu' : (loadingCity ? 'Memuat kota...' : 'Pilih Kota/Kabupaten')">
                            </option>
                            <template x-for="city in cities" :key="city.id">
                                <option :value="city.id" x-text="city.name"></option>
                            </template>
                        </select>
                        @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input wire:model="email" type="email" placeholder="email@kamu.com"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400 @error('email') border-red-300 @enderror">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

                <button type="button" wire:click="nextStep"
                    class="w-full bg-teal-600 text-white font-bold py-3 mt-4 rounded-xl hover:bg-teal-700 transition-colors shadow-lg shadow-teal-500/30">
                    <span wire:loading.remove wire:target="nextStep">Selanjutnya</span>
                    <span wire:loading wire:target="nextStep">Memeriksa...</span>
                </button>
            @endif

            {{-- ==================================== --}}
            {{--             LANGKAH 2                --}}
            {{-- ==================================== --}}
            @if($step === 2)
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-bold text-teal-600 bg-teal-50 px-2 py-1 rounded-md">Langkah 2 dari 2</span>
                        <span class="text-xs text-gray-500 font-medium">Keamanan Akun</span>
                    </div>
                    <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-teal-500 w-full rounded-full"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Field Password --}}
                <div x-data="{ show: false }">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input wire:model="password"
                               :type="show ? 'text' : 'password'"
                               placeholder="Min. 8 karakter"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 pr-11 focus:outline-none focus:ring-2 focus:ring-teal-400 @error('password') border-red-300 @enderror">
                        <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Field Konfirmasi Password --}}
                <div x-data="{ show: false }">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <div class="relative">
                        <input wire:model="passwordConfirm"
                               :type="show ? 'text' : 'password'"
                               placeholder="Ulangi password"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 pr-11 focus:outline-none focus:ring-2 focus:ring-teal-400">
                        <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>


            <!-- Business Fields: Only for Mitra flow -->
            @if($role === 'admin_layanan')
                <div class="space-y-4 border-t border-dashed border-yellow-300 pt-4 bg-yellow-50/50 rounded-xl p-4">
                    <div>
                        <h3 class="font-semibold text-gray-800">📋 Data Verifikasi Identitas Usaha</h3>
                        <p class="text-xs text-gray-500 mt-1">Digunakan <strong>khusus untuk verifikasi</strong> oleh Super
                            Admin. <strong>Tidak ditampilkan</strong> ke wisatawan.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Usaha / Badan Usaha <span
                                class="text-red-500">*</span></label>
                        <input wire:model="businessName" type="text" placeholder="Contoh: CV Budi Jaya"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400 @error('businessName') border-red-300 @enderror">
                        @error('businessName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Domisili / Alamat Kantor Usaha <span
                                class="text-red-500">*</span></label>
                        <input wire:model="businessAddress" type="text" placeholder="Alamat fisik tempat usaha beroperasi"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400 @error('businessAddress') border-red-300 @enderror">
                        @error('businessAddress') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Profil Singkat Usaha</label>
                        <textarea wire:model="businessDesc" rows="4" placeholder="Jelaskan jenis usaha dan pengalaman Anda."
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400"></textarea>
                        <p class="text-xs text-gray-400 mt-1">Opsional. Membantu Super Admin mempertimbangkan persetujuan
                            akun Anda.</p>
                    </div>
                    <p class="text-xs text-yellow-700 bg-yellow-100 border border-yellow-200 rounded-lg p-2">
                        ⚠️ Akun Mitra membutuhkan persetujuan Super Admin sebelum bisa digunakan. Anda akan mendapat
                        notifikasi email setelah disetujui.
                    </p>
                </div>
            @endif

                <div class="flex gap-3 mt-6">
                    <button type="button" wire:click="previousStep"
                        class="w-1/3 bg-gray-100 text-gray-600 font-bold py-3 rounded-xl hover:bg-gray-200 transition-colors">
                        Kembali
                    </button>
                    <button type="submit"
                        class="w-2/3 {{ $role === 'admin_layanan' ? 'bg-teal-700 hover:bg-teal-800' : 'bg-teal-600 hover:bg-teal-700' }} text-white font-bold py-3 rounded-xl transition-colors shadow-lg shadow-teal-500/30">
                        <span wire:loading.remove wire:target="register">{{ $role === 'admin_layanan' ? '🤝 Ajukan Pendaftaran Mitra' : 'Daftar Sekarang' }}</span>
                        <span wire:loading wire:target="register">Memproses...</span>
                    </button>
                </div>
            @endif

            @if($role === 'wisatawan')
                <p class="text-center text-xs text-gray-400">
                    Ingin mendaftarkan usaha wisata Anda?
                    <a href="{{ route('register') }}?role=admin_layanan" wire:navigate
                        class="text-teal-600 hover:underline">Daftar sebagai Mitra</a>
                </p>
            @endif
        </form>
    </div>
</div>