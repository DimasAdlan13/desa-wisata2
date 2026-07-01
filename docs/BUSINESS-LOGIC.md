# Business Logic & Workflow

Dokumen ini menjelaskan aturan bisnis utama sistem. Baca ini sebelum menyentuh kode `Models/` atau `Filament/Resources/`.

---

## Approval Flow

Sistem punya dua kolom approval yang berbeda tujuannya:

| Kolom | Ada di tabel | Artinya |
|-------|-------------|---------|
| `is_approved` | `users`, `services` | Sudah disetujui Super Admin untuk beroperasi |
| `is_active` | `services` | Aktif/tidak sementara (bisa toggle oleh mitra sendiri) |

Layanan baru tayang di website publik hanya jika **keduanya** `true`. Lihat `Service::scopePublic()`.

---

## Alur Pendaftaran Mitra (Admin Layanan)

```
Register via /register (pilih role mitra)
        ↓
Akun tersimpan: is_approved = false
        ↓
Email notifikasi ke Super Admin
        ↓
Super Admin klik "Setujui" di panel
        ↓
is_approved = true → mitra bisa login ke /admin
        ↓
Mitra buat layanan → layanan masuk antrean (is_approved = false)
        ↓
Super Admin setujui layanan → layanan tayang di website
```

**Pengecualian:** Jika Super Admin yang langsung membuat akun/layanan dari panel, otomatis `is_approved = true` tanpa perlu langkah persetujuan. Logika ini ada di `User::booted()` dan `Service::booted()`.

---

## Alur Booking

```
Status: pending → confirmed → completed
               ↘ rejected
               ↘ cancelled (oleh wisatawan)
```

- `completed` — diset otomatis H+1 setelah tanggal booking lewat (via Laravel Model Event di `Booking::booted()`)
- Format kode booking: `DW-YYYYMMDD-00001` (urut per hari)
- Kuota harian dicek real-time di `BookingService::checkDailyQuota()`

---

## Soft Deletes

Model yang pakai `SoftDeletes`: `User`, `Service`, `Booking`.

Efeknya: data yang "dihapus" tidak benar-benar hilang dari database, hanya kolom `deleted_at` diisi timestamp. Ini menjaga integritas histori booking.

> **Jangan** hapus `SoftDeletes` dari model-model ini. Kalau layanan dihapus saat masih ada booking aktif, query di `UserDashboard` bisa error karena relasi `service` jadi `null`.

---

## Forgot Password

- URL: `/forget-password`
- Bekerja untuk semua role (wisatawan, admin layanan, super admin)
- Token reset berlaku 60 menit (konfigurasi di `config/auth.php`)
- Email dikirim via SMTP (konfigurasi `MAIL_*` di `.env`)
- Throttle bawaan Laravel: 1 request per 60 detik per email

---

## File-file yang Sering Jadi Titik Masalah

| File | Kenapa Perlu Diperhatikan |
|------|--------------------------|
| `app/Livewire/Dashboard/UserDashboard.php` | Query pakai `with(['service.primaryPhoto'])` — bisa null pointer jika service di-soft delete |
| `app/Services/BookingService.php` | Cek kuota harian ada di sini, bukan di controller |
| `app/Models/User.php` | Event `creating` menentukan auto-approve |
| `app/Models/Service.php` | Event `creating` menentukan auto-approve + auto-generate slug |
