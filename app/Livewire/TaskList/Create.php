<?php

namespace App\Livewire\TaskList;

use App\Models\TaskList;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Create extends Component
{
    public bool $showModal = false;

    public string $name = '';

    /** @var array<string, string> */
    protected $listeners = [
        'open-create-task-list' => 'open',
    ];

    public function open(): void
    {
        $this->reset('name');
        $this->resetValidation();
        $this->showModal = true;
    }

    public function create(): void
    {
        $this->authorize('create', TaskList::class);

        $this->validate(['name' => 'required|string|max:255']);

        auth()->user()->taskLists()->create([
            'name' => $this->name,
        ]);

        $this->showModal = false;
        $this->reset('name');

        Flux::toast('Task list created.');
        $this->dispatch('task-list-created');
    }

    public function render(): View
    {
        return view('livewire.task-list.create');
    }
}
