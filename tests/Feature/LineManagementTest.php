<?php

namespace Tests\Feature;

use App\Models\Line;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LineManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_line(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('lines.store'), [
            'code' => 'A1',
            'start_time_operation' => '06:00',
            'end_time_operation' => '23:00',
            'type' => 'Автобус',
            'map' => 'maps/a1.png',
        ])->assertRedirect(route('lines.index'));

        $line = Line::query()->firstOrFail();

        $this->assertDatabaseHas('lines', [
            'id' => $line->id,
            'code' => 'A1',
            'type' => 'Автобус',
        ]);

        $this->actingAs($admin)->patch(route('lines.update', $line), [
            'code' => 'T2',
            'start_time_operation' => '05:30',
            'end_time_operation' => '22:45',
            'type' => 'Трамвай',
            'map' => 'maps/t2.png',
        ])->assertRedirect(route('lines.index'));

        $this->assertDatabaseHas('lines', [
            'id' => $line->id,
            'code' => 'T2',
            'type' => 'Трамвай',
        ]);

        $this->actingAs($admin)->delete(route('lines.destroy', $line))
            ->assertRedirect(route('lines.index'));

        $this->assertDatabaseMissing('lines', ['id' => $line->id]);
    }

    public function test_line_validation_requires_allowed_type(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('lines.store'), [
            'code' => 'A1',
            'start_time_operation' => '06:00',
            'end_time_operation' => '23:00',
            'type' => 'Metro',
        ])->assertSessionHasErrors('type');
    }
}
