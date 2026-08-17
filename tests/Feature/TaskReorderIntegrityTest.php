<?php

use App\Livewire\Task\Index;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Livewire\Livewire;

it('reorders owned tasks into contiguous positions', function () {
    $user = User::factory()->create();
    $taskList = TaskList::factory()->for($user)->create();

    $first = Task::factory()->for($user)->for($taskList)->create(['position' => 1]);
    $second = Task::factory()->for($user)->for($taskList)->create(['position' => 2]);
    $third = Task::factory()->for($user)->for($taskList)->create(['position' => 3]);

    Livewire::actingAs($user)
        ->test(Index::class, ['taskList' => $taskList])
        ->call('reorder', [$third->id, $first->id, $second->id]);

    expect($taskList->tasks()->pluck('id')->all())
        ->toBe([$third->id, $first->id, $second->id])
        ->and($taskList->tasks()->pluck('position')->all())
        ->toBe([1, 2, 3]);
});

it('preserves omitted owned tasks and ignores duplicate and foreign ids', function () {
    $user = User::factory()->create();
    $taskList = TaskList::factory()->for($user)->create();
    $otherList = TaskList::factory()->for($user)->create();
    $otherUser = User::factory()->create();
    $otherUsersList = TaskList::factory()->for($otherUser)->create();

    $first = Task::factory()->for($user)->for($taskList)->create(['position' => 1]);
    $second = Task::factory()->for($user)->for($taskList)->create(['position' => 2]);
    $third = Task::factory()->for($user)->for($taskList)->create(['position' => 3]);
    $archived = Task::factory()->for($user)->for($taskList)->create(['position' => 4]);
    $archived->delete();
    $foreignListTask = Task::factory()->for($user)->for($otherList)->create(['position' => 1]);
    $foreignUserTask = Task::factory()->for($otherUser)->for($otherUsersList)->create(['position' => 1]);

    Livewire::actingAs($user)
        ->test(Index::class, ['taskList' => $taskList])
        ->call('reorder', [
            $third->id,
            $third->id,
            $foreignListTask->id,
            $foreignUserTask->id,
        ]);

    expect($taskList->tasks()->pluck('id')->all())
        ->toBe([$third->id, $first->id, $second->id])
        ->and($taskList->tasks()->pluck('position')->all())
        ->toBe([1, 2, 3])
        ->and($archived->fresh()->position)
        ->toBeNull()
        ->and($foreignListTask->fresh()->position)
        ->toBe(1)
        ->and($foreignUserTask->fresh()->position)
        ->toBe(1);
});
