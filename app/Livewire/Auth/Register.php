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

    protected function rules(): array
    {
        $rules = [
            'name'            => 'required|string|max:100',
            'email'           => 'required|email|unique:users,email',
            'phone'           => 'nullable|string|max:20',
            'password'        => 'required|min:8|same:passwordConfirm',
            'role'            => 'required|in:wisatawan,admin_layanan',
        ];

        if ($this->role === 'admin_layanan') {
            $rules['businessName']    = 'required|string|max:100';
            $rules['businessAddress'] = 'required|string|max:255';
        }

        return $rules;
    }

    public function register(): void
    {
        $this->validate();

        $user = User::create([
            'name'                 => $this->name,
            'email'                => $this->email,
            'phone'                => $this->phone,
            'password'             => $this->password,
            'role'                 => $this->role,
            'business_name'        => $this->businessName ?: null,
            'business_address'     => $this->businessAddress ?: null,
            'business_description' => $this->businessDesc ?: null,
            // is_approved: false by default (admin_layanan needs approval)
            // wisatawan: is_approved kept false but isActive() returns true for them
        ]);

        event(new Registered($user));

        if ($user->isWisatawan()) {
            Auth::login($user);
            $intendedUrl = session()->pull('url.intended', route('dashboard'));
            $this->redirect($intendedUrl, navigate: true);
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
