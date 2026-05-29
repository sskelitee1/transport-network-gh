<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Line;
use App\Models\Station;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['login' => 'admin'],
            [
                'name' => 'Administrator',
                'gender' => 'M',
                'birth_date' => '1990-01-01',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin12345'),
                'role' => 'admin',
            ]
        );

        User::factory()->count(12)->create(['role' => 'user']);

        $lines = Line::factory()->count(6)->create();

        foreach ($lines as $line) {
            $stationCount = fake()->numberBetween(3, 7);
            for ($i = 1; $i <= $stationCount; $i++) {
                Station::factory()->create([
                    'line_id' => $line->id,
                    'position_station' => (string) $i,
                ]);
            }

            $vehicleCount = fake()->numberBetween(2, 6);
            $vehicles = collect();
            for ($i = 1; $i <= $vehicleCount; $i++) {
                $vehicles->push(Vehicle::factory()->create([
                    'line_id' => $line->id,
                    'type' => $line->type,
                    'name' => $line->type.' '.$line->code.'-'.$i,
                ]));
            }

            foreach ($vehicles as $vehicle) {
                Driver::factory()->create([
                    'vehicle_id' => $vehicle->id,
                ]);
            }
        }
    }
}
