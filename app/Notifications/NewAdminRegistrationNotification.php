<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAdminRegistrationNotification extends Notification
{
    use Queueable;

    public User $applicant;

    public function __construct(User $applicant)
    {
        $this->applicant = $applicant;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pengajuan Akun Admin Layanan Baru Masuk')
            ->greeting("Halo, {$notifiable->name}!")
            ->line("Ada pengajuan akun **Admin Layanan** baru yang menunggu persetujuan Anda.")
            ->line("Detail Pemohon:")
            ->line("- **Nama:** {$this->applicant->name}")
            ->line("- **Email:** {$this->applicant->email}")
            ->line("- **Nama Usaha:** " . ($this->applicant->business_name ?? '-'))
            ->line("- **Alamat Usaha:** " . ($this->applicant->business_address ?? '-'))
            ->line("- **Profil Usaha:** " . ($this->applicant->business_description ?? '-'))
            ->action('Tinjau & Setujui di Panel Admin', url('/admin/users'))
            ->line('Harap tinjau data pemohon sebelum memberikan persetujuan.');
    }
}
