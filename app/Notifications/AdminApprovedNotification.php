<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminApprovedNotification extends Notification
{
    use Queueable;

    public bool $approved;

    public function __construct(bool $approved = true)
    {
        $this->approved = $approved;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        if ($this->approved) {
            return (new MailMessage)
                ->subject('Selamat! Akun Admin Layanan Anda Telah Disetujui')
                ->greeting("Halo, {$notifiable->name}!")
                ->line('Kabar baik! Pengajuan akun Admin Layanan Anda telah **disetujui** oleh Super Admin.')
                ->line('Anda sekarang sudah bisa login dan mulai mengelola layanan wisata Anda.')
                ->action('Login Sekarang', url(route('login')))
                ->line('Setelah login, langsung menuju Panel Admin untuk membuat layanan pertama Anda!')
                ->salutation('Salam, Tim Desa Wisata Kepulauan Seribu');
        }

        return (new MailMessage)
            ->subject('Informasi: Akun Admin Layanan Anda Dinonaktifkan')
            ->greeting("Halo, {$notifiable->name}.")
            ->line('Mohon maaf, akun Admin Layanan Anda telah **dinonaktifkan** oleh Super Admin.')
            ->line('Jika Anda merasa ini adalah kesalahan, silakan hubungi tim kami untuk informasi lebih lanjut.')
            ->action('Hubungi Kami', url(route('home')))
            ->salutation('Salam, Tim Desa Wisata Kepulauan Seribu');
    }
}
