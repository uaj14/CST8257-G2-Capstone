<?php

namespace App\Models;

use Database\Factories\TaskListFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskList extends Model
{
    /** @use HasFactory<TaskListFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'color',
    ];

    public const COLORS = [
        'slate' => '#64748b',
        'red' => '#ef4444',
        'orange' => '#f97316',
        'amber' => '#f59e0b',
        'green' => '#22c55e',
        'blue' => '#3b82f6',
        'violet' => '#8b5cf6',
        'pink' => '#ec4899',
    ];

    public static function defaultColor(): string
    {
        return 'blue';
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Task, $this>
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class)
            ->orderBy('position');
    }
}
