<?php

namespace App\Livewire\TaskList;

use App\Models\TaskList;
use Flux\Flux;
use Livewire\Component;

class Index extends Component
{
    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public ?TaskList $editingTaskList = null;

    protected $listeners = [
        'task-list-created' => 'onTaskListCreated',
        'task-list-updated' => 'onTaskListUpdated',
    ];

    public function openCreate(): void
    {
        $this->showCreateModal = true;
    }

    public function openEdit(TaskList $taskList): void
    {
        $this->authorize('update', $taskList);
        $this->editingTaskList = $taskList;
        $this->showEditModal = true;
    }

    public function onTaskListCreated(): void
    {
        $this->showCreateModal = false;
    }

    public function onTaskListUpdated(): void
    {
        $this->showEditModal = false;
        $this->editingTaskList = null;
    }

    public function delete(TaskList $taskList): void
    {
        $this->authorize('delete', $taskList);
        $taskList->delete();
        Flux::toast('Task list deleted.');
    }

    public function render()
    {
        $taskLists = auth()->user()
            ->taskLists()
            ->withCount('tasks')
            ->latest()
            ->get();

        return view('livewire.task-list.index', [
            'taskLists' => $taskLists,
        ]);
    }
}
