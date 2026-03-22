<?php

namespace App\Livewire;

use App\Models\Content;
use Livewire\Component;

class ContentDetail extends Component
{
    public Content $content;

    public function mount(string $slug): void
    {
        $this->content = Content::published()->where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.content-detail')
            ->layout('layouts.app');
    }
}
