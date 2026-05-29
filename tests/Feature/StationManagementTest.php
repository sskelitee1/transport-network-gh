<?php

namespace Tests\Feature;

use App\Models\Line;
use App\Models\Station;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_station(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $line = Line::factory()->create(['type' => 'Автобус']);

        $this->actingAs($admin)->post(route('stations.store'), [
            'name' => 'Central Station',
            'position_station' => '1',
            'line_id' => $line->id,
        ])->assertRedirect(route('stations.index'));

        $station = Station::query()->firstOrFail();

        $this->assertDatabaseHas('stations', [
            'id' => $station->id,
            'name' => 'Central Station',
            'line_id' => $line->id,
        ]);

        $this->actingAs($admin)->patch(route('stations.update', $station), [
            'name' => 'North Station',
            'position_station' => '2',
            'line_id' => $line->id,
        ])->assertRedirect(route('stations.index'));

        $this->assertDatabaseHas('stations', [
            'id' => $station->id,
            'name' => 'North Station',
            'position_station' => '2',
        ]);

        $this->actingAs($admin)->delete(route('stations.destroy', $station))
            ->assertRedirect(route('stations.index'));

        $this->assertDatabaseMissing('stations', ['id' => $station->id]);
    }

    public function test_station_limit_per_line_is_enforced(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $line = Line::factory()->create(['type' => 'Автобус']);
        Station::factory()->count(7)->create(['line_id' => $line->id]);

        $this->actingAs($admin)->from(route('stations.create'))->post(route('stations.store'), [
            'name' => 'Extra Station',
            'position_station' => '8',
            'line_id' => $line->id,
        ])
            ->assertRedirect(route('stations.create'))
            ->assertSessionHasErrors('line_id');
    }
}
