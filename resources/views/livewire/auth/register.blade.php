<div>
    <div class="space-y-4">
        @if($role === 'admin_layanan')
        <!-- Mitra Header -->
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="bg-teal-100 text-teal-700 text-xs font-bold px-3 py-1 rounded-full">🤝 Pendaftaran Mitra</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar sebagai Pengelola Wisata</h2>
            <p class="text-gray-500 text-sm mt-1">Sudah punya akun? <a href="{{ route('login') }}" wire:navigate class="text-teal-600 hover:underline">Masuk</a></p>
        </div>
        @else
        <!-- Wisatawan Header -->
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Akun</h2>
            <p class="text-gray-500 text-sm mt-1">Sudah punya akun? <a href="{{ route('login') }}" wire:navigate class="text-teal-600 hover:underline">Masuk</a></p>
        </div>
        @endif

        <form wire:submit.prevent="register" class="space-y-4">

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

            {{-- Dropdown Provinsi & Kota — Hanya untuk Wisatawan --}}
            @if($role === 'wisatawan')
            <div
                x-data="{
                    provinces: [],
                    cities: [],
                    selectedProvinceId: '',
                    loadingProvince: true,
                    loadingCity: false,
                    init() {
                        fetch('https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json')
                            .then(r => r.json())
                            .then(data => {
                                this.provinces = data;
                                this.loadingProvince = false;
                            });
                    },
                    selectProvince(id, name) {
                        this.selectedProvinceId = id;
                        this.cities = [];
                        this.loadingCity = true;
                        $wire.set('province', name);
                        $wire.set('city', '');
                        fetch('https://emsifa.github.io/api-wilayah-indonesia/api/regencies/' + id + '.json')
                            .then(r => r.json())
                            .then(data => {
                                this.cities = data;
                                this.loadingCity = false;
                            });
                    },
                    selectCity(name) {
                        $wire.set('city', name);
                    }
                }"
                class="grid grid-cols-1 md:grid-cols-2 gap-4"
            >
                {{-- Dropdown Provinsi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Provinsi Asal <span class="text-red-500">*</span>
                    </label>
                    <select
                        x-on:change="selectProvince($event.target.value, $event.target.options[$event.target.selectedIndex].text)"
                        :disabled="loadingProvince"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400 disabled:bg-gray-100 @error('province') border-red-300 @enderror"
                    >
                        <option value="" x-text="loadingProvince ? 'Memuat provinsi...' : 'Pilih Provinsi'"></option>
                        <template x-for="prov in provinces" :key="prov.id">
                            <option :value="prov.id" x-text="prov.name"></option>
                        </template>
                    </select>
                    @error('province') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Dropdown Kota/Kabupaten --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Kabupaten/Kota <span class="text-red-500">*</span>
                    </label>
                    <select
                        x-on:change="selectCity($event.target.options[$event.target.selectedIndex].text)"
                        :disabled="!selectedProvinceId || loadingCity"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400 disabled:bg-gray-100 @error('city') border-red-300 @enderror"
                    >
                        <option value=""
                            x-text="!selectedProvinceId ? 'Pilih provinsi dahulu' : (loadingCity ? 'Memuat kota...' : 'Pilih Kota/Kabupaten')"
                        ></option>
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

            <!-- Business Fields: Only for Mitra flow -->
            @if($role === 'admin_layanan')
            <div class="space-y-4 border-t border-dashed border-yellow-300 pt-4 bg-yellow-50/50 rounded-xl p-4">
                <div>
                    <h3 class="font-semibold text-gray-800">📋 Data Verifikasi Identitas Usaha</h3>
                    <p class="text-xs text-gray-500 mt-1">Digunakan <strong>khusus untuk verifikasi</strong> oleh Super Admin. <strong>Tidak ditampilkan</strong> ke wisatawan.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Usaha / Badan Usaha <span class="text-red-500">*</span></label>
                    <input wire:model="businessName" type="text" placeholder="Contoh: CV Budi Jaya"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400 @error('businessName') border-red-300 @enderror">
                    @error('businessName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Domisili / Alamat Kantor Usaha <span class="text-red-500">*</span></label>
                    <input wire:model="businessAddress" type="text" placeholder="Alamat fisik tempat usaha beroperasi"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400 @error('businessAddress') border-red-300 @enderror">
                    @error('businessAddress') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profil Singkat Usaha</label>
                    <textarea wire:model="businessDesc" rows="4" placeholder="Jelaskan jenis usaha dan pengalaman Anda."
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400"></textarea>
                    <p class="text-xs text-gray-400 mt-1">Opsional. Membantu Super Admin mempertimbangkan persetujuan akun Anda.</p>
                </div>
                <p class="text-xs text-yellow-700 bg-yellow-100 border border-yellow-200 rounded-lg p-2">
                    ⚠️ Akun Mitra membutuhkan persetujuan Super Admin sebelum bisa digunakan. Anda akan mendapat notifikasi email setelah disetujui.
                </p>
            </div>
            @endif

            <button type="submit"
                    class="w-full {{ $role === 'admin_layanan' ? 'bg-teal-700 hover:bg-teal-800' : 'bg-teal-600 hover:bg-teal-700' }} text-white font-bold py-3 rounded-xl transition-colors">
                <span wire:loading.remove>{{ $role === 'admin_layanan' ? '🤝 Ajukan Pendaftaran Mitra' : 'Daftar Sekarang' }}</span>
                <span wire:loading>Mendaftarkan...</span>
            </button>

            @if($role === 'wisatawan')
            <p class="text-center text-xs text-gray-400">
                Ingin mendaftarkan usaha wisata Anda?
                <a href="{{ route('register') }}?role=admin_layanan" wire:navigate class="text-teal-600 hover:underline">Daftar sebagai Mitra</a>
            </p>
            @endif
        </form>
    </div>
</div>
