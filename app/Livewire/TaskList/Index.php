<?php

namespace App\Livewire\TaskList;

use App\Models\TaskList;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    /** @var array<string, string> */
    protected $listeners = [
        'task-list-created' => 'refreshList',
        'task-list-updated' => 'refreshList',
    ];

    public function refreshList(): void
    {
        // Receiving the event triggers a re-render of this component.
    }

    public function delete(TaskList $taskList): void
    {
        $this->authorize('delete', $taskList);
        $taskList->delete();
        Flux::toast('Task list deleted.');
    }

    public function render(): View
    {
        $taskLists = auth()->user()
            ->taskLists()
            ->withCount([
                'tasks',
                'tasks as active_tasks_count' => fn ($q) => $q->active(),
            ])
            ->withMin(
                ['tasks as next_due' => fn ($q) => $q->active()->whereNotNull('deadline')->whereDate('deadline', '>=', today())],
                'deadline'
            )
            ->latest()
            ->get();

        return view('livewire.task-list.index', [
            'taskLists' => $taskLists,
        ]);
    }
}
