<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome'  => fake()->word(),
            'valor' => fake()->randomFloat(2, 10, 500),
        ];
    }
}