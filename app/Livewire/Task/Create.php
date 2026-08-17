<?php

namespace App\Livewire\Task;

use App\Models\Task;
use App\Models\TaskList;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Create extends Component
{
    public TaskList $taskList;

    public bool $showModal = false;

    public string $name = '';

    public string $description = '';

    public int $priority = 1;

    public ?string $deadline = null;

    /** @var array<string, string> */
    protected $listeners = [
        'open-create-task' => 'open',
    ];

    public function mount(TaskList $taskList): void
    {
        $this->authorize('view', $taskList);
        $this->taskList = $taskList;
    }

    public function open(): void
    {
        $this->reset(['name', 'description', 'deadline']);
        $this->priority = 1;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function create(): void
    {
        $this->authorize('create', Task::class);

        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'priority' => 'required|integer|min:0|max:2',
            'deadline' => 'nullable|date',
        ]);

        $maxPosition = $this->taskList->tasks()->max('position') ?? 0;

        auth()->user()->tasks()->create([
            'task_list_id' => $this->taskList->id,
            'name' => $this->name,
            'description' => $this->description,
            'priority' => $this->priority,
            'deadline' => $this->deadline,
            'position' => $maxPosition + 1,
        ]);

        $this->showModal = false;

        Flux::toast('Task added.');
        $this->dispatch('task-created');
    }

    public function render(): View
    {
        return view('livewire.task.create');
    }
}
