<?php

namespace Database\Factories;

use App\Models\Line;
use Illuminate\Database\Eloquent\Factories\Factory;

class StationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->streetName(),
            'position_station' => (string) fake()->numberBetween(1, 50),
            'line_id' => Line::factory(),
        ];
    }
}
