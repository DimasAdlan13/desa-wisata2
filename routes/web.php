<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ─── Public Routes ──────────────────────────────────────────────────────────

Route::get('/', \App\Livewire\Homepage::class)->name('home');
Route::get('/layanan', \App\Livewire\ServiceCatalog::class)->name('layanan.index');
Route::get('/layanan/{slug}', \App\Livewire\ServiceDetail::class)->name('layanan.show');
Route::get('/konten/{type?}', \App\Livewire\ContentList::class)->name('konten.index');
Route::get('/konten/artikel/{slug}', \App\Livewire\ContentDetail::class)->name('konten.show');

// ─── Wilayah Proxy (CORS-safe, server-cached) ────────────────────────────────
// Browser fetch ke sini, bukan langsung ke emsifa.github.io
Route::get('/api/wilayah/regencies/{provinceId}', function (string $provinceId) {
    // Validasi: hanya angka, max 2 digit (ID provinsi BPS)
    if (!preg_match('/^\d{1,2}$/', $provinceId)) {
        return response()->json(['error' => 'Invalid province ID'], 400);
    }

    $cacheKey = 'wilayah_regencies_' . $provinceId;

    $data = cache()->remember($cacheKey, now()->addHours(24), function () use ($provinceId) {
        $response = \Illuminate\Support\Facades\Http::timeout(10)
            ->get("https://emsifa.github.io/api-wilayah-indonesia/api/regencies/{$provinceId}.json");

        if (!$response->successful()) {
            return null;
        }
        return $response->json();
    });

    if (!$data) {
        return response()->json(['error' => 'Gagal mengambil data wilayah'], 502);
    }

    return response()->json($data)
        ->header('Cache-Control', 'public, max-age=86400');
})->name('wilayah.regencies')->middleware('throttle:10,1');


// ─── Auth Routes ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');
    Route::get('/register', \App\Livewire\Auth\Register::class)->name('register');
});

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// ─── Authenticated User Routes ────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Booking flow (wisatawan)
    Route::get('/booking/{service:slug}', \App\Livewire\BookingForm::class)
        ->name('booking.create');

    // User Dashboard
    Route::get('/dashboard', \App\Livewire\Dashboard\UserDashboard::class)->name('dashboard');
    Route::get('/dashboard/booking/{booking}', \App\Livewire\Dashboard\BookingDetail::class)->name('dashboard.booking');
    Route::get('/dashboard/rating/{booking}', \App\Livewire\Dashboard\RatingForm::class)->name('dashboard.rating');
});
