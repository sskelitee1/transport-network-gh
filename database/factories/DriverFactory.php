<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'birth_date' => fake()->dateTimeBetween('-55 years', '-21 years')->format('Y-m-d'),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->numerify('+48 #########'),
            'avatar' => null,
            'vehicle_id' => Vehicle::factory(),
        ];
    }
}
