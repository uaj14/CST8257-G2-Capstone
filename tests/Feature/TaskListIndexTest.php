<?php

use App\Livewire\TaskList\Index;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Livewire\Livewire;

it('shows active count and next due date on task list cards', function () {
    $user = User::factory()->create();
    $taskList = TaskList::factory()->for($user)->create();

    Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Active soon task',
        'completed_at' => null,
        'deadline' => now()->addDays(2)->toDateString(),
    ]);

    Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Active later task',
        'completed_at' => null,
        'deadline' => now()->addDays(20)->toDateString(),
    ]);

    Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Completed task',
        'completed_at' => now(),
        'deadline' => now()->subDays(5)->toDateString(),
    ]);

    Task::factory()->for($user)->for($taskList)->create([
        'name' => 'Archived task',
        'completed_at' => null,
        'deadline' => now()->addDays(1)->toDateString(),
    ])->delete();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertViewHas('taskLists', function ($taskLists) use ($taskList) {
            $loaded = $taskLists->firstWhere('id', $taskList->id);

            return $loaded->tasks_count === 3
                && $loaded->active_tasks_count === 2
                && str_starts_with($loaded->next_due, now()->addDays(2)->toDateString());
        })
        ->assertSee('2 active')
        ->assertSee('Next due '.now()->addDays(2)->format('M j'));
});
