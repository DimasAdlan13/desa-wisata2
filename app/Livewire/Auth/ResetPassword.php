<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Component;

class ResetPassword extends Component
{
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected array $rules = [
        'email'                 => 'required|email',
        'password'              => 'required|min:8|confirmed',
        'password_confirmation' => 'required',
    ];

    protected array $messages = [
        'email.required'                 => 'Email wajib diisi.',
        'email.email'                    => 'Format email tidak valid.',
        'password.required'              => 'Password baru wajib diisi.',
        'password.min'                   => 'Password minimal 8 karakter.',
        'password.confirmed'             => 'Konfirmasi password tidak cocok.',
        'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
    ];

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    public function resetPassword(): void
    {
        $this->validate();

        $status = Password::reset(
            [
                'email'                 => $this->email,
                'password'              => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token'                 => $this->token,
            ],
            function ($user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('status', 'Password berhasil diubah! Silakan masuk.');
            $this->redirect(route('login'), navigate: true);
        } else {
            $this->addError('email', 'Link reset password tidak valid atau sudah kadaluarsa.');
        }
    }

    public function render()
    {
        return view('livewire.auth.reset-password')
            ->layout('layouts.guest', ['title' => 'Reset Password']);
    }
}
