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
