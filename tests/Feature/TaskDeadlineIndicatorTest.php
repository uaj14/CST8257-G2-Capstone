<?php

use App\Livewire\Task\Index;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Livewire\Livewire;

it('renders colored due dates and labels based on the deadline', function () {
    $user = User::factory()->create();
    $taskList = TaskList::factory()->for($user)->create();

    Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Overdue task',
        'deadline' => now()->subDays(2)->toDateString(),
    ]);

    Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Upcoming task',
        'deadline' => now()->addDays(1)->toDateString(),
    ]);

    Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Future task',
        'deadline' => now()->addDays(10)->toDateString(),
    ]);

    Livewire::actingAs($user)
        ->test(Index::class, ['taskList' => $taskList])
        ->assertSee('Overdue task')
        ->assertSee('Overdue ·', false)
        ->assertSee('Upcoming task')
        ->assertSee('Due soon ·', false)
        ->assertSee('Future task')
        ->assertSee('Due ', false);
});

it('does not flag a completed task as overdue or due soon', function () {
    $user = User::factory()->create();
    $taskList = TaskList::factory()->for($user)->create();

    Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Completed late task',
        'deadline' => now()->subDays(5)->toDateString(),
        'completed_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(Index::class, ['taskList' => $taskList])
        ->assertSee('Completed late task')
        ->assertDontSee('Overdue ·', false)
        ->assertDontSee('Due soon ·', false);
});

it('excludes tasks without a deadline from overdue and upcoming scopes', function () {
    $user = User::factory()->create();
    $taskList = TaskList::factory()->for($user)->create();

    $noDeadline = Task::factory()->for($user)->for($taskList)->create([
        'name' => 'No deadline task',
        'deadline' => null,
    ]);

    Livewire::actingAs($user)
        ->test(Index::class, ['taskList' => $taskList])
        ->set('filter', 'overdue')
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('id')->all() === [])
        ->set('filter', 'upcoming')
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('id')->all() === [])
        ->set('filter', 'all')
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('id')->all() === [$noDeadline->id]);
});

it('filters overdue and upcoming tasks through the query scopes', function () {
    $user = User::factory()->create();
    $taskList = TaskList::factory()->for($user)->create();

    $overdue = Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Overdue scope task',
        'deadline' => now()->subDays(1)->toDateString(),
    ]);

    $upcoming = Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Upcoming scope task',
        'deadline' => now()->addDays(3)->toDateString(),
    ]);

    $farFuture = Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Far future task',
        'deadline' => now()->addDays(30)->toDateString(),
    ]);

    $completedOverdue = Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Completed overdue task',
        'deadline' => now()->subDays(1)->toDateString(),
        'completed_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(Index::class, ['taskList' => $taskList])
        ->set('filter', 'overdue')
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('id')->all() === [$overdue->id])
        ->set('filter', 'upcoming')
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('id')->all() === [$upcoming->id])
        ->set('filter', 'all')
        ->assertViewHas('tasks', fn ($tasks) => $tasks->pluck('id')->all() === [
            $overdue->id,
            $upcoming->id,
            $farFuture->id,
            $completedOverdue->id,
        ]);
});
