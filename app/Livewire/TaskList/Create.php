<?php

namespace App\Livewire\TaskList;

use App\Models\TaskList;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Create extends Component
{
    public string $name = '';

    public function create(): void
    {
        $this->authorize('create', TaskList::class);

        $this->validate(['name' => 'required|string|max:255']);

        auth()->user()->taskLists()->create([
            'name' => $this->name,
        ]);

        Flux::toast('Task list created.');
        $this->dispatch('task-list-created');
        $this->reset('name');
    }

    public function render(): View
    {
        return view('livewire.task-list.create');
    }
}
