<?php

use App\Livewire\Task\Index;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Livewire\Livewire;

it('marks a task as overdue and due soon based on its deadline', function () {
    $user = User::factory()->create();
    $taskList = TaskList::factory()->for($user)->create();

    $overdueTask = Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Overdue task',
        'deadline' => now()->subDays(2)->toDateString(),
    ]);

    $upcomingTask = Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Upcoming task',
        'deadline' => now()->addDays(1)->toDateString(),
    ]);

    $futureTask = Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Future task',
        'deadline' => now()->addDays(10)->toDateString(),
    ]);

    Livewire::actingAs($user)
        ->test(Index::class, ['taskList' => $taskList])
        ->assertSee('Overdue task')
        ->assertSee('Overdue', false)
        ->assertSee('Upcoming task')
        ->assertSee('Due soon', false)
        ->assertSee('Future task');
});

it('does not mark a completed task as overdue', function () {
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
        ->assertDontSee('Overdue', false);
});
