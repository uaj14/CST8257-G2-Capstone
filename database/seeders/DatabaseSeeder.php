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
        TaskList::factory(10)
            ->state(function () use ($users) {  //state() overrides definitions from the original factory files.
                return [
                    "user_id" => $users->random()->id,
                ];
            })
            ->create();

        Task::factory(25)
            ->state(function () use ($users) {
                return [
                    "user_id" => $users->random()->id,
                ];
            })
            ->create();
    }
}
