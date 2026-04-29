<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email    = '';
    public string $password = '';
    public bool   $remember = false;

    protected $rules = [
        'email'    => 'required|email',
        'password' => 'required|string',
    ];

    public function login(): void
    {
        $this->validate();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', 'Email atau password salah.');
            return;
        }

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
