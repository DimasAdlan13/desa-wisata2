<?php

namespace App\Livewire\Dashboard;

use App\Models\Booking;
use App\Services\BookingService;
use Livewire\Component;
use Livewire\WithFileUploads;

class BookingDetail extends Component
{
    use WithFileUploads;

    public Booking $booking;
    public $paymentProof = null;

    protected $rules = [
        'paymentProof' => 'required|image|max:2048', // max 2MB
    ];

    public function mount(Booking $booking): void
    {
        // Gate: only the owner can view
        abort_if($booking->user_id !== auth()->id(), 403);
        $this->booking = $booking->load(['service.primaryPhoto', 'rating']);
    }

    public function uploadPaymentProof(): void
    {
        $this->validate();

        (new BookingService())->uploadPaymentProof($this->booking, $this->paymentProof);
        $this->paymentProof = null;
        $this->booking->refresh();

        session()->flash('success', 'Bukti pembayaran berhasil diupload! Menunggu konfirmasi admin.');
    }

    public function cancelBooking(): void
    {
        abort_if(!$this->booking->isPending(), 403);
        (new BookingService())->cancelBooking($this->booking);
        $this->booking->refresh();
        session()->flash('success', 'Booking berhasil dibatalkan.');
    }

    public function render()
    {
        return view('livewire.dashboard.booking-detail')->layout('layouts.app');
    }
}
