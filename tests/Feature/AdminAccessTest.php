<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_dashboard_to_login(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_open_admin_pages(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('users.index'))
            ->assertForbidden();

        $this->actingAs($user)->get(route('users.create'))
            ->assertForbidden();
    }

    public function test_regular_user_cannot_write_admin_resources(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->post(route('lines.store'), [
            'code' => 'A1',
            'start_time_operation' => '06:00',
            'end_time_operation' => '23:00',
            'type' => 'Автобус',
            'map' => 'maps/a1.png',
        ])->assertForbidden();
    }
}
