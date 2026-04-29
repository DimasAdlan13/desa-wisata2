<?php

namespace App\Notifications;

use App\Models\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceStatusNotification extends Notification
{
    use Queueable;

    public Service $service;
    public bool $approved;

    public function __construct(Service $service, bool $approved)
    {
        $this->service  = $service;
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
                ->subject("Layanan Disetujui: {$this->service->name}")
                ->greeting("Halo, {$notifiable->name}!")
                ->line("Kabar baik! Layanan wisata Anda **\"{$this->service->name}\"** telah **disetujui** oleh Super Admin.")
                ->line('Layanan Anda kini sudah aktif dan dapat ditemukan oleh wisatawan di katalog.')
                ->action('Lihat di Panel Admin', url('/admin/services'))
                ->salutation('Salam, Tim Desa Wisata Kepulauan Seribu');
        }

        return (new MailMessage)
            ->subject("Informasi Layanan: {$this->service->name}")
            ->greeting("Halo, {$notifiable->name}.")
            ->line("Mohon maaf, layanan **\"{$this->service->name}\"** Anda telah **dinonaktifkan** sementara oleh Super Admin.")
            ->line('Layanan tidak akan tampil di katalog wisatawan hingga diaktifkan kembali.')
            ->line('Silakan hubungi Super Admin untuk informasi lebih lanjut atau lakukan perbaikan pada layanan Anda.')
            ->action('Lihat di Panel Admin', url('/admin/services'))
            ->salutation('Salam, Tim Desa Wisata Kepulauan Seribu');
    }
}
