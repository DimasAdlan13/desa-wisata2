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
     * =========================================================
     * ALGORITMA: Content-Based Filtering dengan Weighted Scoring
     * METODE   : Simple Additive Weighting (SAW)
     * BOBOT    : Ditentukan dari hasil kuesioner AHP
     * =========================================================
     *
     * Kriteria yang digunakan (3 kriteria):
     *   1. Kategori  → Kemiripan jenis layanan wisata
     *   2. Harga     → Kemiripan segmentasi harga (±50%)
     *   3. Rating    → Kualitas layanan (benefit: makin tinggi makin baik)
     *
     * ⚠️  CATATAN BOBOT AHP:
     *     Setelah kuesioner AHP selesai, ganti konstanta di bawah
     *     dengan hasil perhitungan bobot dari kuesioner Anda.
     */
    protected function getSimilarServices(): \Illuminate\Support\Collection
    {
        // =========================================================
        // ✏️  GANTI NILAI INI SETELAH KUESIONER AHP SELESAI
        //     Total ketiga bobot HARUS = 1.0 (100%)
        //
        //     Contoh jika hasil AHP:
        //       Kategori = 0.60, Harga = 0.25, Rating = 0.15
        // =========================================================
        $bobotKategori = 0.60; // ← GANTI dengan bobot AHP kriteria Kategori
        $bobotHarga    = 0.25; // ← GANTI dengan bobot AHP kriteria Harga
        $bobotRating   = 0.15; // ← GANTI dengan bobot AHP kriteria Rating
        // =========================================================

        $current = $this->service;

        // OPTIMASI: Ambil semua kandidat sekaligus (1 query)
        // withAvg → MySQL hitung rata-rata rating, tidak load semua baris ratings
        $candidates = Service::public()
            ->with(['category', 'primaryPhoto'])
            ->withAvg('ratings', 'rating')
            ->where('id', '!=', $current->id)
            ->get();

        // MIN-MAX SCALING untuk Harga:
        // Dihitung SEKALI di luar loop → hanya 2 nilai, tidak berulang per kandidat
        $allPrices  = $candidates->pluck('price')->push($current->price);
        $minHarga   = $allPrices->min();
        $maxHarga   = $allPrices->max();
        $rangeHarga = $maxHarga - $minHarga;

        // Normalisasi harga layanan yang sedang dilihat (dihitung sekali)
        $normHargaCurrent = ($rangeHarga > 0)
            ? ($current->price - $minHarga) / $rangeHarga
            : 0;

        // HITUNG SKOR SAW untuk setiap kandidat
        return $candidates
            ->map(function (Service $service) use (
                $current, $minHarga, $rangeHarga, $normHargaCurrent,
                $bobotKategori, $bobotHarga, $bobotRating
            ) {
                // --- Kriteria 1: KATEGORI (nilai: 1 atau 0) ---
                // Kemiripan biner: sama kategori = 1, beda = 0
                $nilaiKategori = ($service->category_id === $current->category_id) ? 1 : 0;

                // --- Kriteria 2: HARGA — Min-Max Scaling (nilai: 0.0 – 1.0) ---
                // Rumus: nilaiHarga = 1 - |normCandidate - normCurrent|
                // Makin dekat harganya → nilaiHarga makin mendekati 1
                // Makin jauh harganya  → nilaiHarga makin mendekati 0
                $normHargaCandidate = ($rangeHarga > 0)
                    ? ($service->price - $minHarga) / $rangeHarga
                    : 0;
                $nilaiHarga = round(1 - abs($normHargaCandidate - $normHargaCurrent), 4);

                // --- Kriteria 3: RATING — SAW Benefit (nilai: 0.0 – 1.0) ---
                // Rumus: nilaiRating = avgRating / ratingMaksimal (5)
                // Makin tinggi rating → makin besar nilainya (bukan mencari kemiripan rating)
                $avgRating   = $service->ratings_avg_rating ?? 0;
                $nilaiRating = round($avgRating / 5, 4);

                // --- RUMUS SAW AKHIR ---
                // Skor = (W_kategori × N_kategori) + (W_harga × N_harga) + (W_rating × N_rating)
                $skor = ($bobotKategori * $nilaiKategori)
                      + ($bobotHarga    * $nilaiHarga)
                      + ($bobotRating   * $nilaiRating);

                $service->similarity_score = round($skor, 4);
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
