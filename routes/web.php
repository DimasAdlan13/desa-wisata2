<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ─── Public Routes ──────────────────────────────────────────────────────────

Route::get('/', \App\Livewire\Homepage::class)->name('home');
Route::get('/layanan', \App\Livewire\ServiceCatalog::class)->name('layanan.index');
Route::get('/layanan/{slug}', \App\Livewire\ServiceDetail::class)->name('layanan.show');
Route::get('/konten/{type?}', \App\Livewire\ContentList::class)->name('konten.index');
Route::get('/konten/artikel/{slug}', \App\Livewire\ContentDetail::class)->name('konten.show');

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
