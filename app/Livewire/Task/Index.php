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
            foreach ($valid as $position => $taskId) {
                Task::where('id', $taskId)
                    ->where('task_list_id', $this->taskList->id)
                    ->update(['position' => $position + 1]);
            }
        });

        $this->dispatch('task-updated');
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
