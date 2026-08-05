<?php

namespace App\Livewire\Task;

use App\Models\Task;
use App\Models\TaskList;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    public TaskList $taskList;

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public ?Task $editingTask = null;

    /** @var array<string, string> */
    protected $listeners = [
        'task-created' => 'onTaskCreated',
        'task-updated' => 'onTaskUpdated',
    ];

    public function mount(TaskList $taskList): void
    {
        $this->authorize('view', $taskList);
        $this->taskList = $taskList;
    }

    public function openCreate(): void
    {
        $this->showCreateModal = true;
    }

    public function openEdit(Task $task): void
    {
        $this->authorize('update', $task);
        $this->editingTask = $task;
        $this->showEditModal = true;
    }

    public function onTaskCreated(): void
    {
        $this->showCreateModal = false;
    }

    public function onTaskUpdated(): void
    {
        $this->showEditModal = false;
        $this->editingTask = null;
    }

    public function delete(Task $task): void
    {
        $this->authorize('delete', $task);
        $task->delete();
        Flux::toast('Task deleted.');
    }

    public function render(): View
    {
        $tasks = $this->taskList->tasks()
            ->where('user_id', auth()->id())
            ->orderBy('position')
            ->get();

        return view('livewire.task.index', [
            'tasks' => $tasks,
        ]);
    }
}
