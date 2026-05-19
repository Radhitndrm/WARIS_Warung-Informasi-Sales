<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Minuman', 'Makanan Ringan', 'Rokok', 'Sembako',
                'Alat Tulis', 'Kebersihan', 'Kesehatan', 'Frozen Food',
            ]),
        ];
    }
}
