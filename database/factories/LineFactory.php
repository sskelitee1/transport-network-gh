<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LineFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(['Автобус', 'Трамвай', 'Маршрутное такси']);
        $startHour = fake()->numberBetween(4, 8);
        $endHour = fake()->numberBetween(21, 23);

        return [
            'code' => strtoupper(fake()->unique()->bothify('L-##??')),
            'start_time_operation' => sprintf('%02d:00:00', $startHour),
            'end_time_operation' => sprintf('%02d:00:00', $endHour),
            'type' => $type,
            'map' => 'maps/'.fake()->unique()->numberBetween(1, 99).'.png',
        ];
    }
}
