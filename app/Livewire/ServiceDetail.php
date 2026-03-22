<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\Service;
use App\Services\BookingService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ServiceDetail extends Component
{
    public Service $service;
    public string $slug;

    public function mount(string $slug): void
    {
        $this->service = Service::public()
            ->with(['category', 'photos', 'ratings.user', 'user'])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function redirectToLogin()
    {
        session()->put('url.intended', request()->header('Referer') ?? url()->current());
        $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        $availableQuota = null;
        if (auth()->check() && auth()->user()->isWisatawan()) {
            $availableQuota = (new BookingService())->getRemainingQuota($this->service, now()->toDateString());
        }

        $ratings = $this->service->ratings()->with('user')->latest()->take(10)->get();
        $avgRating = $this->service->ratings()->avg('rating');

        return view('livewire.service-detail', compact('availableQuota', 'ratings', 'avgRating'))
            ->layout('layouts.app');
    }
}
