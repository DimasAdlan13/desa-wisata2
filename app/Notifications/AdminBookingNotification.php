<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminBookingNotification extends Notification
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
        $serviceName = $this->booking->service->name;
        $customerName = $this->booking->user->name;
        
        return (new MailMessage)
            ->subject("Booking Baru Masuk: {$serviceName}")
            ->greeting("Halo, {$notifiable->name}!")
            ->line("Ada pesanan baru masuk dari wisatawan untuk layanan **{$serviceName}** Anda.")
            ->line("Detail Pesanan:")
            ->line("- **Kode Booking:** {$this->booking->booking_code}")
            ->line("- **Nama Pemesan:** {$customerName}")
            ->line("- **Tanggal Wisata:** {$this->booking->booking_date->format('d M Y')}")
            ->line("- **Jumlah Orang:** {$this->booking->pax} Pax")
            ->line("- **Total Tagihan:** {$this->booking->formatted_total_price}")
            ->action('Lihat Detail di Admin Panel', url('/admin/bookings'))
            ->line('Harap bersiap untuk mengecek verifikasi pembayaran jika wisatawan sudah mengunggah bukti.');
    }
}
