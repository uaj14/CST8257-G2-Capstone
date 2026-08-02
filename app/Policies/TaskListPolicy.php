<?php

namespace App\Policies;

use App\Models\TaskList;
use App\Models\User;

class TaskListPolicy
{
    /**
     * Allow an authenticated user to view their task lists.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Allow the user to view only a task list that belongs to them.
     */
    public function view(User $user, TaskList $taskList): bool
    {
        return $user->id === $taskList->user_id;
    }

    /**
     * Allow an authenticated user to create a task list.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Allow the user to update only their own task list.
     */
    public function update(User $user, TaskList $taskList): bool
    {
        return $user->id === $taskList->user_id;
    }

    /**
     * Allow the user to delete only their own task list.
     */
    public function delete(User $user, TaskList $taskList): bool
    {
        return $user->id === $taskList->user_id;
    }

    /**
     * Restoring task lists is not supported.
     */
    public function restore(User $user, TaskList $taskList): bool
    {
        return false;
    }

    /**
     * Permanently deleting task lists is not supported.
     */
    public function forceDelete(User $user, TaskList $taskList): bool
    {
        return false;
    }
}
