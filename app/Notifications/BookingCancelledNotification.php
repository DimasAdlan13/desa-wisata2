<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelledNotification extends Notification
{
    use Queueable;

    public Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $serviceName  = $this->booking->service->name;
        $customerName = $this->booking->user->name;

        return (new MailMessage)
            ->subject("⚠️ Pembatalan Booking: {$serviceName}")
            ->greeting("Halo, {$notifiable->name}!")
            ->line("Wisatawan telah **membatalkan** pesanan untuk layanan **{$serviceName}** Anda.")
            ->line("Detail Pesanan yang Dibatalkan:")
            ->line("- **Kode Booking:** {$this->booking->booking_code}")
            ->line("- **Nama Pemesan:** {$customerName}")
            ->line("- **Tanggal Wisata:** {$this->booking->booking_date->format('d M Y')}")
            ->line("- **Jumlah Orang:** {$this->booking->pax} Pax")
            ->line("- **Total Tagihan:** {$this->booking->formatted_total_price}")
            ->action('Lihat Riwayat Pesanan', url('/admin/bookings'))
            ->line('Slot pada tanggal tersebut kini tersedia kembali untuk wisatawan lain.');
    }
}
