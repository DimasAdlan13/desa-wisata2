<?php

namespace App\Livewire;

use App\Models\Content;
use Livewire\Component;
use Livewire\WithPagination;

class ContentList extends Component
{
    use WithPagination;

    public string $type = '';

    public function mount(?string $type = ''): void
    {
        $this->type = $type ?? '';
    }

    public function render()
    {
        $contents = Content::published()
            ->when($this->type, fn($q) => $q->ofType($this->type))
            ->latest()
            ->paginate(9);

        return view('livewire.content-list', compact('contents'))
            ->layout('layouts.app');
    }
}
