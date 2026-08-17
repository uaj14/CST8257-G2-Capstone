<?php

use App\Livewire\Task\Index;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Livewire\Livewire;

test('it defaults to the active view and filters by active, completed, archived, and all', function () {
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
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('id')->all() === [$activeTask->id])
        ->set('filter', 'all')
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('id')->all() === [
            $activeTask->id,
            $completedTask->id,
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
        ]);
});

test('it sorts tasks by position, priority, deadline, and name', function () {
    $user = User::factory()->create();
    $taskList = TaskList::factory()->for($user)->create();

    // Fixture where every sort option disagrees:
    // Zulu: position 1, priority Medium, deadline latest
    // Bravo: position 2, priority Low, deadline earliest
    // Alpha: position 3, priority High, deadline middle
    Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Zulu',
        'priority' => 1,
        'deadline' => '2025-12-20',
        'position' => 1,
    ]);

    Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Bravo',
        'priority' => 0,
        'deadline' => '2025-12-10',
        'position' => 2,
    ]);

    Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Alpha',
        'priority' => 2,
        'deadline' => '2025-12-15',
        'position' => 3,
    ]);

    Livewire::actingAs($user)
        ->test(Index::class, ['taskList' => $taskList])
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('name')->all() === ['Zulu', 'Bravo', 'Alpha'])
        ->set('sort', 'priority')
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('name')->all() === ['Alpha', 'Zulu', 'Bravo'])
        ->set('sort', 'deadline')
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('name')->all() === ['Bravo', 'Alpha', 'Zulu'])
        ->set('sort', 'name')
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('name')->all() === ['Alpha', 'Bravo', 'Zulu']);
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

test('it renders the correct priority label on task cards', function () {
    $user = User::factory()->create();
    $taskList = TaskList::factory()->for($user)->create();

    Task::factory()->for($user)->for($taskList)->create([
        'name' => 'High priority task',
        'priority' => 2,
    ]);

    Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Low priority task',
        'priority' => 0,
    ]);

    Livewire::actingAs($user)
        ->test(Index::class, ['taskList' => $taskList])
        ->assertSee('High priority task')
        ->assertSee('High', false)
        ->assertSee('Low priority task')
        ->assertSee('Low', false);
});

test('it manages complete, reopen, archive, restore, and delete actions', function () {
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

    $duplicateNameTaskA = Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Duplicate name task',
    ]);
    $duplicateNameTaskB = Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Duplicate name task',
    ]);

    Livewire::actingAs($user)
        ->test(Index::class, ['taskList' => $taskList])
        ->call('delete', $duplicateNameTaskA->id);

    expect(Task::withTrashed()->find($duplicateNameTaskA->id))->toBeNull()
        ->and(Task::find($duplicateNameTaskB->id))->not->toBeNull();
});
