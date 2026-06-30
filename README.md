# Sistem Informasi Pemesanan Desa Wisata Pulau Pramuka

Sistem informasi berbasis web untuk pengelolaan pemesanan layanan wisata di Kepulauan Seribu. Dibangun sebagai tugas akhir (skripsi) dengan mengimplementasikan algoritma rekomendasi hybrid (CBF + AHP + SAW).

**URL Production:** https://wisata-pulauseribu.com

---

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend Framework | Laravel 11 |
| UI Reactivity | Livewire 3 |
| Admin Panel | Filament v3 |
| CSS Framework | Tailwind CSS |
| JS Interactivity | Alpine.js (bundled dalam Livewire 3) |
| Database | MySQL |
| Image Storage | Laravel Filesystem (public disk) |

---

## Arsitektur Sistem

```
┌─────────────────────────────────────────────────────┐
│                   PUBLIK (tanpa login)               │
│  Homepage · Katalog Layanan · Detail Layanan · Konten│
└──────────────────────┬──────────────────────────────┘
                       │
          ┌────────────┴────────────┐
          │                         │
┌─────────▼──────────┐   ┌──────────▼──────────────────┐
│ WISATAWAN           │   │ ADMIN PANEL (/admin)         │
│ /dashboard          │   │ Filament v3                  │
│ /booking/{slug}     │   │                              │
│ /dashboard/booking  │   │ Super Admin → semua akses    │
│ /dashboard/rating   │   │ Admin Layanan → layanan milik│
└────────────────────┘   │ sendiri + booking masuk      │
                          └─────────────────────────────┘
```

---

## Role & Hak Akses

| Role | Cara Mendapat | Perlu Approval | Akses Panel Admin |
|------|--------------|----------------|-------------------|
| `wisatawan` | Register publik | ❌ Langsung aktif | ❌ |
| `admin_layanan` | Register sebagai Mitra | ✅ Super Admin | ✅ (jika disetujui) |
| `super_admin` | Dibuat via Artisan/Seeder | ❌ | ✅ Penuh |

---

## Algoritma Rekomendasi

Diimplementasikan di `App\Livewire\ServiceDetail.php` — fungsi `getSimilarServices()`.

**Hybrid: Content-Based Filtering + AHP + SAW**

1. **CBF** — Seleksi semua kandidat layanan yang aktif & disetujui
2. **AHP** — Bobot tiap kriteria dari hasil kuesioner:
   - Kategori: `0.43`
   - Harga: `0.37`
   - Rating: `0.20`
3. **SAW** — Hitung skor tiap kandidat:
   ```
   Skor = (0.43 × nilaiKategori) + (0.37 × nilaiHarga) + (0.20 × nilaiRating)
   ```
   - Kategori: binary (1 = sama, 0 = beda)
   - Harga: Min-Max Scaling → `1 - |normCandidate - normCurrent|`
   - Rating: `avgRating / 5`
4. Diurutkan descending, diambil 6 teratas

---

## Alur Status Booking

```
pending → confirmed → completed (otomatis H+1 via Model Event)
       ↘ rejected
       ↘ cancelled
```

Kode booking digenerate otomatis: `DW-YYYYMMDD-00001`

---

## Struktur Folder Penting

```
app/
├── Filament/Resources/     # Panel admin (CRUD via Filament)
│   ├── BookingResource.php
│   ├── ServiceResource.php
│   ├── UserResource.php
│   ├── ContentResource.php
│   └── ServiceCategoryResource.php
├── Livewire/               # Komponen reaktif (frontend ↔ backend)
│   ├── Homepage.php
│   ├── ServiceCatalog.php
│   ├── ServiceDetail.php   # ← Algoritma rekomendasi ada di sini
│   ├── BookingForm.php
│   ├── Auth/Login.php
│   └── Dashboard/
├── Models/                 # Eloquent ORM
│   ├── User.php            # Role: super_admin, admin_layanan, wisatawan
│   ├── Service.php         # SoftDeletes, scope: public()
│   ├── Booking.php         # SoftDeletes, auto-complete via retrieved event
│   ├── Rating.php
│   ├── ServiceCategory.php
│   └── Content.php
└── Services/
    └── BookingService.php  # Logic kuota harian & create booking

resources/views/livewire/  # Blade templates per komponen
routes/web.php             # Semua route aplikasi
```

---

## Setup Lokal

### Prasyarat
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL
- Laragon (recommended) atau XAMPP

### Langkah Instalasi

```bash
# 1. Clone repository
git clone <repo-url> desa-wisata2
cd desa-wisata2

# 2. Install dependencies PHP
composer install

# 3. Install dependencies JS
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate app key
php artisan key:generate

# 6. Konfigurasi database di .env
# DB_DATABASE=desa_wisata2
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Jalankan migrasi & seeder
php artisan migrate --seed

# 8. Link storage publik
php artisan storage:link

# 9. Build assets
npm run dev

# 10. Jalankan server (atau pakai Laragon)
php artisan serve
```

### Akun Default (Seeder)

| Role | Email | Password |
|------|-------|----------|
| Super Admin | superadmin@wisata.com | password |

---

## Konvensi Kode

- **Livewire Component:** Tidak menggunakan Controller terpisah. Logic langsung di class PHP komponen.
- **Alpine.js:** Interaksi UI ditulis inline di Blade dengan atribut `x-data`, `x-show`, `@click`.
- **Filament:** Semua halaman admin adalah Resource Filament, bukan controller biasa.
- **Soft Deletes:** Model `User`, `Service`, `Booking` menggunakan `SoftDeletes` untuk menjaga integritas data historis.
- **No jQuery:** Gunakan Alpine.js untuk semua manipulasi DOM ringan.

---

## Fitur Utama

- ✅ Katalog layanan wisata dengan filter kategori
- ✅ Sistem rekomendasi hybrid (CBF + AHP + SAW)
- ✅ Pemesanan dengan pengecekan kuota harian real-time
- ✅ Dynamic form schema per layanan (pertanyaan kustom mitra)
- ✅ Panel admin terpisah per role (Super Admin & Admin Layanan)
- ✅ Status booking otomatis via Laravel Model Event
- ✅ Rate limiting anti-spam pada form booking
- ✅ Proxy API wilayah Indonesia dengan cache 24 jam
- ✅ Lazy loading gambar untuk optimasi performa
- ✅ Responsive design (mobile-first)

---

## Pengujian Performa

Script pengujian non-fungsional tersedia terpisah di:
`C:\Users\ACER\Documents\selenium-wisata-test\uji_performa.py`

- **Alat:** Python + Selenium 4
- **Simulasi Jaringan:** Slow 4G (1.5 Mbps, latency 150ms)
- **Cache:** Dinonaktifkan via CDP
- **Target:** < 5 detik per halaman (sesuai NFR)
- **Pengulangan:** 10x per halaman

---

## Author

**Dimas Adlan Wiyanto** | NIM: 2207412059
Politeknik Negeri Jakarta — Tugas Akhir Skripsi
