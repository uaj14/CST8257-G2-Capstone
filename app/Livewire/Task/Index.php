<?php

namespace App\Livewire\Task;

use App\Models\Task;
use App\Models\TaskList;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public TaskList $taskList;

    /** @var array<string, string> */
    protected $listeners = [
        'task-created' => 'refreshList',
        'task-updated' => 'refreshList',
    ];

    public function mount(TaskList $taskList): void
    {
        $this->authorize('view', $taskList);
        $this->taskList = $taskList;
    }

    public function refreshList(): void
    {
        // Receiving the event triggers a re-render of this component.
    }

    public function delete(Task $task): void
    {
        $this->authorize('delete', $task);

        abort_unless(
            $task->task_list_id === $this->taskList->id,
            404
        );

        $task->delete();

        Flux::toast('Task deleted.');
    }

    /**
     * Reorder tasks in this list based on the new order of IDs.
     *
     * @param  array<int>  $orderedIds
     */
    public function reorder(array $orderedIds): void
    {
        $this->authorize('update', $this->taskList);

        $ownedIds = $this->taskList->tasks()
            ->where('user_id', auth()->id())
            ->pluck('id')
            ->all();

        // Ignore any IDs the client tries to inject that don't belong to this user/list.
        $valid = array_values(array_intersect($orderedIds, $ownedIds));

        DB::transaction(function () use ($valid): void {
            // Free the unique (task_list_id, position) constraint before renumbering
            // by nulling every position in this list, then assigning sequential ones.
            Task::where('task_list_id', $this->taskList->id)
                ->update(['position' => null]);

            foreach ($valid as $position => $taskId) {
                Task::where('id', $taskId)
                    ->where('task_list_id', $this->taskList->id)
                    ->update(['position' => $position + 1]);
            }
        });

        $this->dispatch('task-updated');
    }

    public string $quickName = '';

    public function quickAdd(): void
    {
        $this->validate([
            'quickName' => 'required|string|max:255',
        ]);

        $maxPosition = $this->taskList->tasks()->max('position') ?? 0;

        auth()->user()->tasks()->create([
            'task_list_id' => $this->taskList->id,
            'name' => trim($this->quickName),
            'priority' => 1,
            'position' => $maxPosition + 1,
        ]);

        $this->quickName = '';
        Flux::toast('Task added.');

        $this->dispatch('task-created');
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

    public function complete(Task $task): void
    {
        $this->authorize('update', $task);

        abort_unless(
            $task->task_list_id === $this->taskList->id,
            404
        );

        $task->update(['completed_at' => now()]);

        Flux::toast('Task completed.');
        $this->dispatch('task-updated');
    }

    public function reopen(Task $task): void
    {
        $this->authorize('update', $task);

        abort_unless(
            $task->task_list_id === $this->taskList->id,
            404
        );

        $task->update(['completed_at' => null]);

        Flux::toast('Task reopened.');
        $this->dispatch('task-updated');
    }

    public function archive(Task $task): void
    {
        $this->authorize('update', $task);

        abort_unless(
            $task->task_list_id === $this->taskList->id,
            404
        );

        $task->delete();

        Flux::toast('Task archived.');
        $this->dispatch('task-updated');
    }

    public function restore(int $taskId): void
    {
        $task = Task::onlyTrashed()->findOrFail($taskId);

        $this->authorize('update', $task);
        $this->authorize('view', $this->taskList);

        abort_unless(
            $task->task_list_id === $this->taskList->id,
            404
        );

        $task->restore();

        Flux::toast('Task restored.');
        $this->dispatch('task-updated');
    }

    public function forceDelete(int $taskId): void
    {
        $task = Task::onlyTrashed()->findOrFail($taskId);

        $this->authorize('update', $task);
        $this->authorize('view', $this->taskList);

        abort_unless(
            $task->task_list_id === $this->taskList->id,
            404
        );

        $task->forceDelete();

        Flux::toast('Task permanently deleted.');
        $this->dispatch('task-updated');
    }
}
