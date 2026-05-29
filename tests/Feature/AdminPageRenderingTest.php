<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Line;
use App\Models\Station;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPageRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_index_create_and_edit_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $line = Line::factory()->create(['code' => 'A1', 'type' => 'Автобус']);
        $station = Station::factory()->create(['line_id' => $line->id, 'name' => 'Central']);
        $vehicle = Vehicle::factory()->create([
            'line_id' => $line->id,
            'name' => 'Автобус A1-1',
            'type' => 'Автобус',
        ]);
        $driver = Driver::factory()->create(['vehicle_id' => $vehicle->id, 'name' => 'Ivan Driver']);
        $managedUser = User::factory()->create(['role' => 'user']);

        $this->actingAs($admin)->get(route('lines.index'))->assertOk()->assertSee('Маршруты');
        $this->actingAs($admin)->get(route('lines.create'))->assertOk();
        $this->actingAs($admin)->get(route('lines.edit', $line))->assertOk();

        $this->actingAs($admin)->get(route('stations.index'))->assertOk()->assertSee('Остановки');
        $this->actingAs($admin)->get(route('stations.create'))->assertOk();
        $this->actingAs($admin)->get(route('stations.edit', $station))->assertOk();

        $this->actingAs($admin)->get(route('vehicles.index'))->assertOk()->assertSee('Транспорт');
        $this->actingAs($admin)->get(route('vehicles.create'))->assertOk();
        $this->actingAs($admin)->get(route('vehicles.edit', $vehicle))->assertOk();

        $this->actingAs($admin)->get(route('drivers.index'))->assertOk()->assertSee('Водители');
        $this->actingAs($admin)->get(route('drivers.create'))->assertOk();
        $this->actingAs($admin)->get(route('drivers.edit', $driver))->assertOk();

        $this->actingAs($admin)->get(route('users.index'))->assertOk()->assertSee('Пользователи');
        $this->actingAs($admin)->get(route('users.create'))->assertOk();
        $this->actingAs($admin)->get(route('users.edit', $managedUser))->assertOk();
    }

    public function test_regular_user_can_view_public_resource_indexes(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('lines.index'))->assertOk();
        $this->actingAs($user)->get(route('stations.index'))->assertOk();
        $this->actingAs($user)->get(route('vehicles.index'))->assertOk();
        $this->actingAs($user)->get(route('drivers.index'))->assertOk();
    }
}
