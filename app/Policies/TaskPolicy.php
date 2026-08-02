<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Allow an authenticated user to view their tasks.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Allow the user to view only a task that belongs to them.
     */
    public function view(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    /**
     * Allow an authenticated user to create a task.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Allow the user to update only their own task.
     */
    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    /**
     * Allow the user to delete only their own task.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    /**
     * Restoring tasks is not supported.
     */
    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Permanently deleting tasks is not supported.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }
}
