<?php

namespace App\Livewire;

use App\Models\Service;
use App\Models\ServiceCategory;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceCatalog extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $categoryId  = '';
    public string $sortBy      = 'latest'; // latest | price_asc | price_desc | rating

    protected $queryString = ['search', 'categoryId', 'sortBy'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryId(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Service::public()
            ->with(['category', 'primaryPhoto'])
            ->withAvg('ratings', 'rating')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->categoryId, fn($q) => $q->where('category_id', $this->categoryId));

        $query = match ($this->sortBy) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'rating'     => $query->orderByDesc('ratings_avg_rating'),
            default      => $query->latest(),
        };

        $services   = $query->paginate(9);
        $categories = ServiceCategory::active()->get();

        return view('livewire.service-catalog', compact('services', 'categories'))
            ->layout('layouts.app');
    }
}
