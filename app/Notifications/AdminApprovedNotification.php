<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminApprovedNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Selamat! Akun Admin Layanan Anda Telah Disetujui')
            ->greeting("Halo, {$notifiable->name}!")
            ->line('Kabar baik! Pengajuan akun Admin Layanan Anda telah **disetujui** oleh Super Admin.')
            ->line('Anda sekarang sudah bisa login dan mulai mengelola layanan wisata Anda di sistem kami.')
            ->action('Login Sekarang', url(route('login')))
            ->line('Setelah login, langsung menuju Panel Admin untuk membuat layanan pertama Anda!')
            ->salutation('Salam, Tim Desa Wisata Kepulauan Seribu');
    }
}
