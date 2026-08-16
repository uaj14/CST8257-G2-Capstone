<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // This assigns new task_list and tasks to pre-existing users.
        $users = User::factory(5)->create();
        $task_lists = TaskList::factory(10)
            ->state(function () use ($users) {  // state() overrides definitions from the original factory files.
                return [
                    'user_id' => $users->random()->id,
                ];
            })
            ->create();

        // Assign a stable position per (user, list) so ORDER BY position is deterministic.
        foreach ($users as $user) {
            $userLists = $task_lists->where('user_id', $user->id)->values();
            foreach ($userLists as $list) {
                // Insert with a unique high offset per row so the (task_list_id, position)
                // unique constraint never collides while seeding; we'll overwrite position below.
                $baseOffset = ($list->id * 1000) + 100000;

                $created = collect();
                for ($i = 0; $i < 5; $i++) {
                    $created->push(
                        Task::factory()
                            ->state([
                                'user_id' => $user->id,
                                'task_list_id' => $list->id,
                                'position' => $baseOffset + $i,
                            ])
                            ->create()
                    );
                }

                $created->each(function (Task $task, int $i) {
                    // Position is 1-indexed within (task_list_id).
                    $task->position = $i + 1;
                    $task->save();
                });
            }
        }
    }
}
