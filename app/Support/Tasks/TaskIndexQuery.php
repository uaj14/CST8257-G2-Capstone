<?php

namespace App\Support\Tasks;

use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TaskIndexQuery
{
    public function __construct(
        private readonly TaskList $taskList,
        private readonly int $userId,
        private readonly string $filter,
        private readonly string $sort,
        private readonly string $search,
    ) {}

    /**
     * @return Collection<int, Task>
     */
    public function get(): Collection
    {
        return $this->query()->get();
    }

    /**
     * @return Builder<Task>
     */
    public function query(): Builder
    {
        $query = $this->taskList->tasks()
            ->getQuery()
            ->reorder()
            ->where('user_id', $this->userId);

        if ($this->filter === 'active') {
            $query->whereNull('completed_at')->whereNull('deleted_at');
        } elseif ($this->filter === 'completed') {
            $query->whereNotNull('completed_at')->whereNull('deleted_at');
        } elseif ($this->filter === 'archived') {
            $query->onlyTrashed();
        } else {
            $query->withTrashed();
        }

        if ($this->search !== '') {
            $query->where(function (Builder $q): void {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }

        match ($this->sort) {
            'priority' => $query->orderByDesc('priority'),
            'deadline' => $query->orderBy('deadline'),
            'name' => $query->orderBy('name'),
            default => $query->orderBy('position'),
        };

        return $query;
    }
}
