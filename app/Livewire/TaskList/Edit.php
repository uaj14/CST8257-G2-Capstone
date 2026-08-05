<?php

namespace App\Livewire\TaskList;

use App\Models\TaskList;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Edit extends Component
{
    public TaskList $taskList;

    public string $name = '';

    public function mount(TaskList $taskList): void
    {
        $this->authorize('update', $taskList);
        $this->taskList = $taskList;
        $this->name = $taskList->name;
    }

    public function save(): void
    {
        $this->authorize('update', $this->taskList);

        $this->validate(['name' => 'required|string|max:255']);

        $this->taskList->update(['name' => $this->name]);

        Flux::toast('Task list renamed.');
        $this->dispatch('task-list-updated');
    }

    public function render(): View
    {
        return view('livewire.task-list.edit');
    }
}
