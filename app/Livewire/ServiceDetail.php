<?php

namespace App\Livewire;

use App\Models\Service;
use App\Services\BookingService;
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

    /**
     * Algoritma Content-Based Filtering — Weighted Scoring
     *
     * Setiap layanan lain diberi skor berdasarkan kemiripannya
     * dengan layanan yang sedang dilihat:
     *   +60  → Kategori sama (sinyal terkuat)
     *   +25  → Harga dalam rentang ±50% dari harga layanan ini
     *   +15  → Skor bonus dari rating rata-rata (rating × 3, maks 15)
     *   +10  → Lokasi mengandung kata kunci yang sama (misal "Pramuka")
     *
     * Hasilnya diurutkan dari skor tertinggi, diambil 4 teratas.
     */
    protected function getSimilarServices(): \Illuminate\Support\Collection
    {
        $current      = $this->service;
        $priceMin     = $current->price * 0.5;
        $priceMax     = $current->price * 1.5;

        // Ambil kata-kata penting dari lokasi layanan ini (lebih dari 3 karakter)
        $locationWords = collect(explode(' ', strtolower($current->location ?? '')))
            ->filter(fn($w) => strlen($w) > 3)
            ->values();

        // Ambil semua layanan publik kecuali layanan yang sedang dilihat
        $candidates = Service::public()
            ->with(['category', 'primaryPhoto', 'ratings'])
            ->where('id', '!=', $current->id)
            ->get();

        return $candidates
            ->map(function (Service $service) use ($current, $priceMin, $priceMax, $locationWords) {
                $score = 0;

                // +60: Kategori sama
                if ($service->category_id === $current->category_id) {
                    $score += 60;
                }

                // +25: Harga dalam rentang ±50%
                if ($service->price >= $priceMin && $service->price <= $priceMax) {
                    $score += 25;
                }

                // +maks 15: Bonus rating rata-rata (rating 5.0 → +15 poin)
                $avgRating = $service->ratings->avg('rating') ?? 0;
                $score += round($avgRating * 3);

                // +10: Lokasi mengandung kata kunci yang sama
                $serviceLoc = strtolower($service->location ?? '');
                foreach ($locationWords as $word) {
                    if (str_contains($serviceLoc, (string) $word)) {
                        $score += 10;
                        break;
                    }
                }

                $service->similarity_score = $score;
                return $service;
            })
            ->sortByDesc('similarity_score')
            ->take(4)
            ->values();
    }

    public function render()
    {
        $availableQuota = null;
        if (auth()->check() && auth()->user()->isWisatawan()) {
            $availableQuota = (new BookingService())->getRemainingQuota($this->service, now()->toDateString());
        }

        $ratings        = $this->service->ratings()->with('user')->latest()->take(10)->get();
        $avgRating      = $this->service->ratings()->avg('rating');
        $similarServices = $this->getSimilarServices();

        return view('livewire.service-detail', compact('availableQuota', 'ratings', 'avgRating', 'similarServices'))
            ->layout('layouts.app');
    }
}
