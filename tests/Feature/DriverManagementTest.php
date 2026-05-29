<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Line;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_driver(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $line = Line::factory()->create(['type' => 'Автобус']);
        $vehicle = Vehicle::factory()->create([
            'line_id' => $line->id,
            'type' => 'Автобус',
        ]);
        $newVehicle = Vehicle::factory()->create([
            'line_id' => $line->id,
            'type' => 'Автобус',
        ]);

        $this->actingAs($admin)->post(route('drivers.store'), [
            'name' => 'Ivan Driver',
            'birth_date' => '1990-01-01',
            'email' => 'ivan@example.com',
            'phone' => '+48111111111',
            'vehicle_id' => $vehicle->id,
        ])->assertRedirect(route('drivers.index'));

        $driver = Driver::query()->firstOrFail();

        $this->assertDatabaseHas('drivers', [
            'id' => $driver->id,
            'name' => 'Ivan Driver',
            'vehicle_id' => $vehicle->id,
        ]);

        $this->actingAs($admin)->patch(route('drivers.update', $driver), [
            'name' => 'Petr Driver',
            'birth_date' => '1988-05-10',
            'email' => 'petr@example.com',
            'phone' => '+48222222222',
            'vehicle_id' => $newVehicle->id,
        ])->assertRedirect(route('drivers.index'));

        $this->assertDatabaseHas('drivers', [
            'id' => $driver->id,
            'name' => 'Petr Driver',
            'vehicle_id' => $newVehicle->id,
        ]);

        $this->actingAs($admin)->delete(route('drivers.destroy', $driver))
            ->assertRedirect(route('drivers.index'));

        $this->assertDatabaseMissing('drivers', ['id' => $driver->id]);
    }

    public function test_driver_email_must_be_unique(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Driver::factory()->create(['email' => 'duplicate@example.com']);

        $this->actingAs($admin)->post(route('drivers.store'), [
            'name' => 'Ivan Driver',
            'birth_date' => '1990-01-01',
            'email' => 'duplicate@example.com',
            'phone' => '+48111111111',
            'vehicle_id' => null,
        ])->assertSessionHasErrors('email');
    }
}
