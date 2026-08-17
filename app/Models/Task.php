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

    public const PRIORITY_FLUX_COLORS = [
        0 => 'zinc',
        1 => 'amber',
        2 => 'red',
    ];

    public const PRIORITY_BADGE_CLASSES = [
        0 => 'bg-zinc-50 text-zinc-700 dark:bg-zinc-400/10 dark:text-zinc-200',
        1 => 'bg-amber-50 text-amber-700 dark:bg-amber-400/10 dark:text-amber-200',
        2 => 'bg-rose-50 text-rose-700 dark:bg-rose-400/10 dark:text-rose-200',
    ];

    public const UPCOMING_WINDOW_DAYS = 3;

    /**
     * Not completed and not archived (soft-deleted).
     *
     * @param  Builder<Task>  $query
     * @return Builder<Task>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('completed_at');
    }

    /**
     * Active tasks whose deadline is before today.
     *
     * @param  Builder<Task>  $query
     * @return Builder<Task>
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->active()
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<', today());
    }

    /**
     * Active tasks due today through the next days (inclusive).
     *
     * @param  Builder<Task>  $query
     * @return Builder<Task>
     */
    public function scopeDueWithin(Builder $query, int $days = self::UPCOMING_WINDOW_DAYS): Builder
    {
        return $query->active()
            ->whereNotNull('deadline')
            ->whereDate('deadline', '>=', today())
            ->whereDate('deadline', '<=', today()->addDays($days));
    }

    public function getIsOverdueAttribute(): bool
    {
        return ! $this->completed_at
            && ! $this->trashed()
            && $this->deadline
            && $this->deadline->lt(today());
    }

    public function getIsUpcomingAttribute(): bool
    {
        return ! $this->completed_at
            && ! $this->trashed()
            && $this->deadline
            && ! $this->is_overdue
            && $this->deadline->lte(today()->addDays(self::UPCOMING_WINDOW_DAYS));
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
