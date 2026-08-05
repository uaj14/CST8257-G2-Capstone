<?php

namespace App\Livewire\TaskList;

use App\Models\TaskList;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Edit extends Component
{
    public bool $showModal = false;

    public ?TaskList $taskList = null;

    public string $name = '';

    /** @var array<string, string> */
    protected $listeners = [
        'open-edit-task-list' => 'open',
    ];

    public function open(int $taskListId): void
    {
        $taskList = TaskList::findOrFail($taskListId);
        $this->authorize('update', $taskList);

        $this->taskList = $taskList;
        $this->name = $taskList->name;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->taskList === null) {
            return;
        }

        $this->authorize('update', $this->taskList);

        $this->validate(['name' => 'required|string|max:255']);

        $this->taskList->update(['name' => $this->name]);

        $this->showModal = false;

        Flux::toast('Task list renamed.');
        $this->dispatch('task-list-updated');
    }

    public function render(): View
    {
        return view('livewire.task-list.edit');
    }
}
