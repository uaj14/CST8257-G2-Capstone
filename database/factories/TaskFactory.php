<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // return [
        //     //
        // ];
        return [
            'user_id' => User::factory(),
            'task_list_id' => null,
            'name' => fake()->sentence(),
            'priority' => fake()->numberBetween(0, 2),
            'deadline' => fake()->optional()->dateTimeBetween('now', '+1 year'),
            'position' => null,
        ];
    }
}
