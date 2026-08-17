<?php

use App\Livewire\Task\Index;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Livewire\Livewire;

test('it filters tasks by active, completed, archived, and all views', function () {
    $user = User::factory()->create();
    $taskList = TaskList::factory()->for($user)->create();

    $activeTask = Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Active task for filter',
        'completed_at' => null,
    ]);

    $completedTask = Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Completed task for filter',
        'completed_at' => now(),
    ]);

    $archivedTask = Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Archived task for filter',
        'completed_at' => null,
    ]);
    $archivedTask->delete();

    Livewire::actingAs($user)
        ->test(Index::class, ['taskList' => $taskList])
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('id')->all() === [
            $activeTask->id,
            $completedTask->id,
            $archivedTask->id,
        ])
        ->set('filter', 'active')
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('id')->all() === [$activeTask->id])
        ->set('filter', 'completed')
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('id')->all() === [$completedTask->id])
        ->set('filter', 'archived')
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('id')->all() === [$archivedTask->id])
        ->set('filter', 'all')
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('id')->all() === [
            $activeTask->id,
            $completedTask->id,
            $archivedTask->id,
        ]);
});

test('it sorts tasks by priority, deadline, and name', function () {
    $user = User::factory()->create();
    $taskList = TaskList::factory()->for($user)->create();

    Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Zulu task',
        'priority' => 0,
        'deadline' => '2026-12-20',
        'position' => 3,
    ]);

    Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Alpha task',
        'priority' => 2,
        'deadline' => '2026-12-10',
        'position' => 1,
    ]);

    Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Bravo task',
        'priority' => 1,
        'deadline' => '2026-12-15',
        'position' => 2,
    ]);

    Livewire::actingAs($user)
        ->test(Index::class, ['taskList' => $taskList])
        ->set('sort', 'priority')
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('name')->all() === ['Alpha task', 'Bravo task', 'Zulu task'])
        ->set('sort', 'deadline')
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('name')->all() === ['Alpha task', 'Bravo task', 'Zulu task'])
        ->set('sort', 'name')
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('name')->all() === ['Alpha task', 'Bravo task', 'Zulu task']);
});

test('it searches task names and descriptions', function () {
    $user = User::factory()->create();
    $taskList = TaskList::factory()->for($user)->create();

    $invoiceTask = Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Pay hydro invoice',
        'description' => 'Submit payment before Friday',
    ]);

    $groceryTask = Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Buy groceries',
        'description' => 'Milk and eggs',
    ]);

    Livewire::actingAs($user)
        ->test(Index::class, ['taskList' => $taskList])
        ->set('search', 'invoice')
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('id')->all() === [$invoiceTask->id])
        ->set('search', 'Milk')
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('id')->all() === [$groceryTask->id]);
});

test('it manages complete, reopen, archive, restore, and permanent delete actions', function () {
    $user = User::factory()->create();
    $taskList = TaskList::factory()->for($user)->create();

    $task = Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Task lifecycle check',
        'completed_at' => null,
    ]);

    Livewire::actingAs($user)
        ->test(Index::class, ['taskList' => $taskList])
        ->call('complete', $task->id);

    expect($task->fresh()->completed_at)->not->toBeNull();

    Livewire::actingAs($user)
        ->test(Index::class, ['taskList' => $taskList])
        ->set('filter', 'completed')
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('id')->all() === [$task->id])
        ->call('reopen', $task->id);

    expect($task->fresh()->completed_at)->toBeNull();

    Livewire::actingAs($user)
        ->test(Index::class, ['taskList' => $taskList])
        ->call('archive', $task->id)
        ->set('filter', 'archived')
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('id')->all() === [$task->id])
        ->call('restore', $task->id);

    expect($task->fresh()->trashed())->toBeFalse();

    Livewire::actingAs($user)
        ->test(Index::class, ['taskList' => $taskList])
        ->call('archive', $task->id)
        ->call('forceDelete', $task->id);

    expect(Task::withTrashed()->find($task->id))->toBeNull();
});
