<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'name' => fake()->words(3, true),
            'price' => fake()->randomElement([1000, 2000, 3000, 5000, 7000, 8000, 10000, 15000, 20000, 25000]),
            'stock' => fake()->numberBetween(10, 100),
            'image' => null,
            'is_active' => true,
        ];
    }

    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => fake()->numberBetween(1, 4),
        ]);
    }
}
