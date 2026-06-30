<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPassword extends Component
{
    public string $email = '';
    public bool $sent = false;

    protected array $rules = [
        'email' => 'required|email',
    ];

    protected array $messages = [
        'email.required' => 'Email wajib diisi.',
        'email.email'    => 'Format email tidak valid.',
    ];

    public function sendResetLink(): void
    {
        $this->validate();

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->sent = true;
            $this->email = '';
        } else {
            $this->addError('email', 'Email tidak ditemukan dalam sistem kami.');
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')
            ->layout('layouts.guest', ['title' => 'Lupa Password']);
    }
}
