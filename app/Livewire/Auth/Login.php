<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|string',
    ];

    public function login(): void
    {
        $this->validate();

        // Tentukan kunci unik (Email + IP) untuk melacak percobaan login
        $throttleKey = strtolower($this->email) . '|' . request()->ip();

        // Cek apakah user sudah melampaui batas percobaan (misal 5 kali)
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Terlalu banyak percobaan login. Silakan coba lagi dalam $seconds detik.");
            return;
        }

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            // Jika gagal, catat satu kali percobaan (hit)
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey);
            
            $this->addError('email', 'Email atau password salah.');
            return;
        }

        // Jika berhasil, bersihkan catatan kegagalan agar user bisa login normal lagi nanti
        \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);

        $user = Auth::user();

        // Block admin_layanan yang belum diapprove
        if ($user->isAdminLayanan() && !$user->is_approved) {
            Auth::logout();
            $this->addError('email', 'Akun Anda sedang menunggu persetujuan Super Admin.');
            return;
        }

        $intendedUrl = session()->pull('url.intended', route('home'));

        // Untuk wisatawan: pastikan url.intended bukan URL admin (sisa session lama)
        if ($user->isWisatawan() && str_starts_with($intendedUrl, '/admin')) {
            $intendedUrl = route('home');
        }

        $this->redirect(
            $user->isSuperAdmin() || $user->isAdminLayanan()
            ? '/admin'
            : $intendedUrl,
            navigate: true
        );
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.guest');
    }
}
