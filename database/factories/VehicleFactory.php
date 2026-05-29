<?php

namespace Database\Factories;

use App\Models\Line;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(['Автобус', 'Трамвай', 'Маршрутное такси']);

        return [
            'name' => $type.' '.fake()->unique()->numberBetween(100, 999),
            'capacity' => fake()->numberBetween(20, 180),
            'type' => $type,
            'line_id' => Line::factory(),
        ];
    }
}
