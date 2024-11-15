<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class OrderFactory extends Factory
{

    public function definition(): array
    {
        return [
            'user_id'       => function () {
                return \App\Models\User::query()->inRandomOrder()->first()->id;
            },
            'total'         => fake()->randomFloat(2, 20, 1000),
            'status'        => fake()->randomElement(['pendiente', 'completado']),
        ];
    }

}
