<?php

use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;

it('allows the owner to complete and reopen their task', function () {
    $user = User::factory()->create();
    $taskList = TaskList::factory()->for($user)->create();
    $task = Task::factory()->for($user)->for($taskList)->create(['completed_at' => null]);

    expect($task->completed_at)->toBeNull();
    $task->update(['completed_at' => now()]);
    expect($task->fresh()->completed_at)->not->toBeNull();
    $task->update(['completed_at' => null]);
    expect($task->fresh()->completed_at)->toBeNull();
});

it('allows the owner to archive and restore their task', function () {
    $user = User::factory()->create();
    $taskList = TaskList::factory()->for($user)->create();
    $task = Task::factory()->for($user)->for($taskList)->create();

    $task->delete();
    expect($task->fresh()->trashed())->toBeTrue();

    $task->restore();
    expect($task->fresh()->trashed())->toBeFalse();
});

it('allows the owner to permanently delete an archived task', function () {
    $user = User::factory()->create();
    $taskList = TaskList::factory()->for($user)->create();
    $task = Task::factory()->for($user)->for($taskList)->create();

    $task->delete();
    $task->forceDelete();

    expect(Task::withTrashed()->find($task->id))->toBeNull();
});

it('allows the owner to archive a completed task', function () {
    $user = User::factory()->create();
    $taskList = TaskList::factory()->for($user)->create();
    $task = Task::factory()->for($user)->for($taskList)->create(['completed_at' => now()]);

    $task->delete();

    expect($task->fresh()->trashed())->toBeTrue();
});
