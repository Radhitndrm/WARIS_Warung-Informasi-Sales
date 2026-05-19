<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::first()?->id ?? User::factory(),
            'role' => 'user',
            'message' => fake()->sentence(),
        ];
    }
}
