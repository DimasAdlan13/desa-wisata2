<?php

namespace App\Livewire\Dashboard;

use App\Models\Booking;
use Livewire\Component;
use Livewire\WithPagination;

class UserDashboard extends Component
{
    use WithPagination;

    public string $activeTab = 'active'; // active | history | profile

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        $userId = auth()->id();

        $activeBookings = Booking::forUser($userId)
            ->active()
            ->with(['service.primaryPhoto'])
            ->latest()
            ->paginate(5, pageName: 'activePage');

        $historyBookings = Booking::forUser($userId)
            ->whereIn('status', [Booking::STATUS_COMPLETED, Booking::STATUS_CANCELLED, Booking::STATUS_REJECTED])
            ->with(['service.primaryPhoto', 'rating'])
            ->latest()
            ->paginate(5, pageName: 'historyPage');

        return view('livewire.dashboard.user-dashboard', compact('activeBookings', 'historyBookings'))
            ->layout('layouts.app', ['title' => 'Dashboard']);
    }
}
