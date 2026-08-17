<?php

namespace App\Models;

use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    use SoftDeletes;

    public const PRIORITY_LABELS = [
        0 => 'Low',
        1 => 'Medium',
        2 => 'High',
    ];

    public const PRIORITY_COLORS = [
        0 => 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300',
        1 => 'bg-amber-100 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300',
        2 => 'bg-red-100 text-red-700 dark:bg-red-400/10 dark:text-red-300',
    ];

    public const DUE_SOON_DAYS = 7;

    /**
     * Scope the query to incomplete tasks whose deadline is in the past.
     *
     * @param  Builder<Task>  $query
     * @return Builder<Task>
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereNotNull('deadline')
            ->whereNull('completed_at')
            ->whereDate('deadline', '<', today());
    }

    /**
     * Scope the query to incomplete tasks whose deadline is today or within the next days.
     *
     * @param  Builder<Task>  $query
     * @return Builder<Task>
     */
    public function scopeUpcoming(Builder $query, int $days = self::DUE_SOON_DAYS): Builder
    {
        return $query->whereNotNull('deadline')
            ->whereNull('completed_at')
            ->whereDate('deadline', '>=', today())
            ->whereDate('deadline', '<=', today()->addDays($days));
    }

    public function isOverdue(): bool
    {
        return $this->deadline !== null
            && $this->completed_at === null
            && $this->deadline->isBefore(today());
    }

    public function isDueSoon(int $days = self::DUE_SOON_DAYS): bool
    {
        return $this->deadline !== null
            && $this->completed_at === null
            && ! $this->isOverdue()
            && $this->deadline->lte(today()->addDays($days));
    }

    protected $fillable = [
        'user_id',
        'task_list_id',
        'name',
        'description',
        'priority',
        'deadline',
        'position',
        'completed_at',
    ];

    protected $casts = [
        'deadline' => 'date',
        'completed_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<TaskList, $this>
     */
    public function taskList(): BelongsTo
    {
        return $this->belongsTo(TaskList::class);
    }
}
