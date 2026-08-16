<?php

use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use function Pest\Laravel\actingAs;

it('allows the owner to complete and reopen their task', function () {
    $user = User::factory()->create();
    $taskList = TaskList::factory()->for($user)->create();
    $task = Task::factory()->for($user)->for($taskList)->create();

    actingAs($user)
        ->post(route('livewire.update'), [
            'components' => [
                'task.index' => [
                    'tasks' => $taskList->id,
                    'method' => 'complete',
                    'params' => [$task->id],
                ],
            ],
        ])
        ->assertOk();

    expect($task->fresh()->completed_at)->not->toBeNull();
})->skip('Livewire form request format needs Livewire test utilities');

it('allows the owner to archive and restore their task', function () {
    $user = User::factory()->create();
    $taskList = TaskList::factory()->for($user)->create();
    $task = Task::factory()->for($user)->for($taskList)->create();

    actingAs($user)
        ->post(route('livewire.update'), [
            'components' => [
                'task.index' => [
                    'tasks' => $taskList->id,
                    'method' => 'archive',
                    'params' => [$task->id],
                ],
            ],
        ])
        ->assertOk();

    expect($task->fresh()->trashed())->toBeTrue();
})->skip('Livewire form request format needs Livewire test utilities');

it('allows the owner to permanently delete an archived task', function () {
    $user = User::factory()->create();
    $taskList = TaskList::factory()->for($user)->create();
    $task = Task::factory()->for($user)->for($taskList)->create();

    $task->delete();

    actingAs($user)
        ->post(route('livewire.update'), [
            'components' => [
                'task.index' => [
                    'tasks' => $taskList->id,
                    'method' => 'forceDelete',
                    'params' => [$task->id],
                ],
            ],
        ])
        ->assertOk();

    expect(Task::withTrashed()->find($task->id))->toBeNull();
})->skip('Livewire form request format needs Livewire test utilities');
