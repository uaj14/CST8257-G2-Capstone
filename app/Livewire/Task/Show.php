<?php

namespace App\Livewire\Task;

use App\Models\Task;
use App\Models\TaskList;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Show extends Component
{
    public TaskList $taskList;

    public Task $task;

    public function mount(TaskList $taskList, int $task): void
    {
        $this->authorize('view', $taskList);

        $task = Task::withTrashed()->findOrFail($task);
        $this->authorize('view', $task);

        abort_unless(
            $task->task_list_id === $taskList->id,
            404
        );

        $this->taskList = $taskList;
        $this->task = $task;
    }

    public function complete(): void
    {
        $this->authorize('update', $this->task);

        abort_unless(! $this->task->trashed(), 404);

        $this->task->update(['completed_at' => now()]);

        Flux::toast('Task completed.');
        $this->dispatch('task-updated');
    }

    public function reopen(): void
    {
        $this->authorize('update', $this->task);

        abort_unless(! $this->task->trashed(), 404);

        $this->task->update(['completed_at' => null]);

        Flux::toast('Task reopened.');
        $this->dispatch('task-updated');
    }

    public function delete(): void
    {
        $this->authorize('delete', $this->task);

        abort_unless(! $this->task->trashed(), 404);

        $this->task->delete();

        Flux::toast('Task moved to Trash.');
        $this->redirectRoute('tasks.index', $this->taskList);
    }

    public function restore(): void
    {
        $this->authorize('restore', $this->task);

        abort_unless($this->task->trashed(), 404);

        $this->task->restore();

        Flux::toast('Task restored.');
        $this->dispatch('task-updated');
    }

    public function forceDelete(): void
    {
        $this->authorize('forceDelete', $this->task);

        abort_unless($this->task->trashed(), 404);

        $this->task->forceDelete();

        Flux::toast('Task permanently deleted.');
        $this->redirectRoute('tasks.index', $this->taskList);
    }

    public function render(): View
    {
        return view('livewire.task.show', [
            'task' => $this->task,
            'taskList' => $this->taskList,
        ]);
    }
}
