<?php

namespace App\Livewire\Dashboard;

use App\Models\Booking;
use App\Services\BookingService;
use Livewire\Component;

class BookingDetail extends Component
{
    public Booking $booking;

    public function mount(Booking $booking): void
    {
        // Gate: only the owner can view
        abort_if($booking->user_id !== auth()->id(), 403);
        $this->booking = $booking->load(['service.primaryPhoto', 'service.user', 'rating']);
    }

    public function cancelBooking(): void
    {
        abort_if(!$this->booking->isPending(), 403);

        (new BookingService())->cancelBooking($this->booking);
        $this->booking->refresh();

        // Kirim notifikasi ke Admin Layanan pemilik layanan
        $adminLayanan = $this->booking->service->user;
        if ($adminLayanan) {
            $adminLayanan->notify(new \App\Notifications\BookingCancelledNotification($this->booking));
        }

        session()->flash('success', 'Booking berhasil dibatalkan.');
    }

    public function render()
    {
        return view('livewire.dashboard.booking-detail')->layout('layouts.app', ['title' => 'Detail Pemesanan']);
    }
}
