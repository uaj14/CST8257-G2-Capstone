<?php

namespace App\Livewire\Task;

use App\Models\Task;
use App\Models\TaskList;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class Trash extends Component
{
    public function restore(int $taskId): void
    {
        $task = Task::onlyTrashed()->findOrFail($taskId);

        $this->authorize('restore', $task);

        $task->restore();

        Flux::toast('Task restored.');
    }

    public function forceDelete(int $taskId): void
    {
        $task = Task::onlyTrashed()->findOrFail($taskId);

        $this->authorize('forceDelete', $task);

        $task->forceDelete();

        Flux::toast('Task permanently deleted.');
    }

    public function render(): View
    {
        $trashedTasks = Task::onlyTrashed()
            ->where('user_id', auth()->id())
            ->with('taskList')
            ->latest('deleted_at')
            ->get();

        /** @var Collection<int, TaskList> $taskLists */
        $taskLists = $trashedTasks
            ->groupBy(fn (Task $task) => $task->task_list_id ?? 0)
            ->map(function (Collection $tasks) {
                $first = $tasks->first();

                return [
                    'taskList' => $first->taskList,
                    'tasks' => $tasks,
                ];
            })
            ->values();

        return view('livewire.task.trash', [
            'groups' => $taskLists,
            'count' => $trashedTasks->count(),
        ]);
    }
}
