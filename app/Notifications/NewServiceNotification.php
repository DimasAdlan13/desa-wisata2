<?php

namespace App\Notifications;

use App\Models\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewServiceNotification extends Notification
{
    use Queueable;

    public Service $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $owner = $this->service->user;

        return (new MailMessage)
            ->subject("Layanan Baru Menunggu Review: {$this->service->name}")
            ->greeting("Halo, {$notifiable->name}!")
            ->line("Ada **layanan wisata baru** yang dibuat oleh Admin Layanan dan menunggu persetujuan Anda.")
            ->line("Detail Layanan:")
            ->line("- **Nama Layanan:** {$this->service->name}")
            ->line("- **Pemilik:** " . ($owner?->name ?? '-') . " ({$owner?->email})")
            ->line("- **Kategori:** " . ($this->service->category?->name ?? '-'))
            ->line("- **Harga:** {$this->service->formatted_price}")
            ->line("- **Kuota per Hari:** {$this->service->quota_per_day} Pax")
            ->action('Review & Setujui Layanan', url('/admin/services'))
            ->line('Harap tinjau layanan sebelum wisatawan bisa melakukan pemesanan.');
    }
}
