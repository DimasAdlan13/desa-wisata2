<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Register extends Component
{
    public string $name             = '';
    public string $email            = '';
    public string $phone            = '';
    public string $password         = '';
    public string $passwordConfirm  = '';
    public string $role             = 'wisatawan';
    public string $province         = '';
    public string $city             = '';

    public function mount(): void
    {
        // Pre-select role from query param, e.g. /register?role=admin_layanan
        $roleParam = request()->query('role');
        if (in_array($roleParam, ['wisatawan', 'admin_layanan'])) {
            $this->role = $roleParam;
        }
    }

    // Business fields (admin_layanan)
    public string $businessName     = '';
    public string $businessAddress  = '';
    public string $businessDesc     = '';

    public int $step = 1;

    public function nextStep(): void
    {
        // Pasang satpam di sini juga
        $throttleKey = 'register|' . request()->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Terlalu banyak percobaan. Tunggu $seconds detik.");
            return;
        }

        // TAMBAHKAN BARIS INI: Catat ketukan pintu
        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);

        $rules = [
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role'  => 'required|in:wisatawan,admin_layanan',
        ];

        if ($this->role === 'wisatawan') {
            $rules['province'] = 'required|string|max:100';
            $rules['city']     = 'required|string|max:100';
        }

        $this->validate($rules);
        $this->step = 2;
    }

    public function previousStep(): void
    {
        $this->step = 1;
    }

    protected function rules(): array
    {
        $rules = [
            'name'            => 'required|string|max:100',
            'email'           => 'required|email|unique:users,email',
            'phone'           => 'nullable|string|max:20',
            'password'        => 'required|min:8|same:passwordConfirm',
            'role'            => 'required|in:wisatawan,admin_layanan',
        ];

        if ($this->role === 'wisatawan') {
            $rules['province'] = 'required|string|max:100';
            $rules['city']     = 'required|string|max:100';
        }

        if ($this->role === 'admin_layanan') {
            $rules['businessName']    = 'required|string|max:100';
            $rules['businessAddress'] = 'required|string|max:255';
        }

        return $rules;
    }

    public function register(): void
    {
        // Tentukan kunci unik berdasarkan IP untuk mencegah spam pendaftaran
        $throttleKey = 'register|' . request()->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Terlalu banyak percobaan pendaftaran. Silakan coba lagi dalam $seconds detik.");
            return;
        }

        $this->validate();

        // Catat hit setiap kali melewati validasi (baik di nextStep atau register)
        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);

        $user = User::create([
            'name'                 => $this->name,
            'email'                => $this->email,
            'phone'                => $this->phone,
            'password'             => $this->password,
            'role'                 => $this->role,
            'province'             => $this->province ?: null,
            'city'                 => $this->city ?: null,
            'business_name'        => $this->businessName ?: null,
            'business_address'     => $this->businessAddress ?: null,
            'business_description' => $this->businessDesc ?: null,
        ]);

        event(new Registered($user));

        if ($user->isWisatawan()) {
            Auth::login($user);
            // Hapus url.intended — user baru tidak punya booking,
            // jangan diarahkan ke URL sesi browser lama yang bisa milik user lain
            session()->forget('url.intended');
            $this->redirect(route('home'), navigate: true);

        } else {
            // admin_layanan → notify all super admins
            \App\Models\User::where('role', \App\Models\User::ROLE_SUPER_ADMIN)
                ->get()
                ->each(fn($superAdmin) => $superAdmin->notify(
                    new \App\Notifications\NewAdminRegistrationNotification($user)
                ));

            session()->flash('info', 'Pendaftaran berhasil! Akun Anda sedang menunggu persetujuan Super Admin. Anda akan mendapat email saat akun disetujui.');
            $this->redirect(route('login'), navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.auth.register')->layout('layouts.guest');
    }
}
