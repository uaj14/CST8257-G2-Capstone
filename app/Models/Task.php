<?php

namespace App\Models;

use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'task_list_id',
        'name',
        'description',
        'priority',
        'deadline',
        'position',
    ];

    // This turns deadline into a carbon date object.
    protected $casts = [
        'deadline' => 'date',
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
