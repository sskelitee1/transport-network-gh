<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Line;
use App\Models\Station;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_dashboard_statistics(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $line = Line::factory()->create(['code' => 'A1', 'type' => 'Автобус']);
        Station::factory()->create(['line_id' => $line->id, 'name' => 'Central']);
        $vehicle = Vehicle::factory()->create([
            'line_id' => $line->id,
            'name' => 'Автобус A1-1',
            'type' => 'Автобус',
        ]);
        Driver::factory()->create([
            'vehicle_id' => $vehicle->id,
            'name' => 'Ivan Driver',
        ]);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Добро пожаловать в систему')
            ->assertSee('Маршрутов')
            ->assertSee('Остановок')
            ->assertSee('Транспортных средств')
            ->assertSee('Водителей')
            ->assertSee('A1')
            ->assertSee('Ivan Driver');
    }
}
