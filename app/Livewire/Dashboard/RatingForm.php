<?php

namespace App\Livewire\Dashboard;

use App\Models\Booking;
use App\Models\Rating;
use Livewire\Component;

class RatingForm extends Component
{
    public Booking $booking;
    public int $rating = 5;
    public string $review = '';

    protected $rules = [
        'rating' => 'required|integer|min:1|max:5',
        'review' => 'nullable|string|max:1000',
    ];

    public function mount(Booking $booking): void
    {
        abort_if($booking->user_id !== auth()->id(), 403);
        abort_if(!$booking->isRateable(), 403, 'Booking ini tidak dapat diberi rating.');

        $this->booking = $booking->load('service');
    }

    public function setRating(int $value): void
    {
        $this->rating = $value;
    }

    public function submit(): void
    {
        // Pasang Satpam: Cegah spam ulasan (Maksimal 2 ulasan per menit)
        $throttleKey = 'rating|' . auth()->id();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 2)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            $this->addError('review', "Anda mengirim ulasan terlalu cepat. Tunggu $seconds detik.");
            return;
        }

        // Catat setiap pengiriman ulasan SEBELUM validasi
        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);

        $this->validate();

        Rating::create([
            'booking_id' => $this->booking->id,
            'user_id' => auth()->id(),
            'service_id' => $this->booking->service_id,
            'rating' => $this->rating,
            'review' => $this->review,
        ]);

        session()->flash('success', 'Terima kasih atas ulasan Anda!');
        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.dashboard.rating-form')->layout('layouts.app', ['title' => 'Beri Ulasan']);
    }
}
