<?php

namespace App\Livewire\Task;

use App\Models\Task;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Edit extends Component
{
    public Task $task;

    public string $name = '';

    public string $description = '';

    public int $priority = 1;

    public ?string $deadline = null;

    public function mount(Task $task): void
    {
        $this->authorize('update', $task);
        $this->task = $task;
        $this->name = $task->name;
        $this->description = $task->description ?? '';
        $this->priority = $task->priority;
        $this->deadline = $task->deadline?->format('Y-m-d');
    }

    public function save(): void
    {
        $this->authorize('update', $this->task);

        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'priority' => 'required|integer|min:0|max:2',
            'deadline' => 'nullable|date',
        ]);

        $this->task->update([
            'name' => $this->name,
            'description' => $this->description,
            'priority' => $this->priority,
            'deadline' => $this->deadline,
        ]);

        Flux::toast('Task updated.');
        $this->dispatch('task-updated');
    }

    public function render(): View
    {
        return view('livewire.task.edit');
    }
}
