<?php

namespace Tests\Feature;

use App\Models\Line;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_vehicle(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $line = Line::factory()->create(['type' => 'Автобус']);

        $this->actingAs($admin)->post(route('vehicles.store'), [
            'name' => 'Автобус A1-1',
            'capacity' => 80,
            'type' => 'Автобус',
            'line_id' => $line->id,
        ])->assertRedirect(route('vehicles.index'));

        $vehicle = Vehicle::query()->firstOrFail();

        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'name' => 'Автобус A1-1',
            'capacity' => 80,
        ]);

        $this->actingAs($admin)->patch(route('vehicles.update', $vehicle), [
            'name' => 'Автобус A1-2',
            'capacity' => 90,
            'type' => 'Автобус',
            'line_id' => $line->id,
        ])->assertRedirect(route('vehicles.index'));

        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'name' => 'Автобус A1-2',
            'capacity' => 90,
        ]);

        $this->actingAs($admin)->delete(route('vehicles.destroy', $vehicle))
            ->assertRedirect(route('vehicles.index'));

        $this->assertDatabaseMissing('vehicles', ['id' => $vehicle->id]);
    }

    public function test_vehicle_type_must_match_line_type(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $line = Line::factory()->create(['type' => 'Автобус']);

        $this->actingAs($admin)->from(route('vehicles.create'))->post(route('vehicles.store'), [
            'name' => 'Трамвай T1-1',
            'capacity' => 120,
            'type' => 'Трамвай',
            'line_id' => $line->id,
        ])
            ->assertRedirect(route('vehicles.create'))
            ->assertSessionHasErrors('type');
    }

    public function test_vehicle_limit_per_line_is_enforced(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $line = Line::factory()->create(['type' => 'Автобус']);
        Vehicle::factory()->count(10)->create([
            'line_id' => $line->id,
            'type' => 'Автобус',
        ]);

        $this->actingAs($admin)->from(route('vehicles.create'))->post(route('vehicles.store'), [
            'name' => 'Автобус A1-11',
            'capacity' => 80,
            'type' => 'Автобус',
            'line_id' => $line->id,
        ])
            ->assertRedirect(route('vehicles.create'))
            ->assertSessionHasErrors('line_id');
    }
}
