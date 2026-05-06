<?php

namespace App\Livewire;

use App\Models\Content;
use App\Models\Rating;
use App\Models\Service;
use App\Models\ServiceCategory;
use Livewire\Component;

class Homepage extends Component
{
    public function render()
    {
        $featuredServices = Service::public()
            ->with(['category', 'primaryPhoto', 'ratings'])
            ->withCount('bookings')
            ->orderByDesc('bookings_count')
            ->take(6)
            ->get();

        $categories = ServiceCategory::active()->withCount(['services' => fn($q) => $q->approved()->active()])->get();

        $latestContents = Content::published()->latest()->take(4)->get();

        $infoWisata = Content::published()
            ->where('type', 'info_wisata')
            ->orderByDesc('is_featured') // yang is_featured=true naik ke atas
            ->latest()
            ->take(4)
            ->get();

        $stats = [
            'total_services'  => Service::public()->count(),
            'total_bookings'  => \App\Models\Booking::where('status', 'completed')->count(),
            'avg_rating'      => round(Rating::avg('rating') ?? 0, 1),
            'total_wisatawan' => \App\Models\User::where('role', 'wisatawan')->count(),
        ];

        return view('livewire.homepage', compact('featuredServices', 'categories', 'latestContents', 'infoWisata', 'stats'))
            ->layout('layouts.app');
    }
}
