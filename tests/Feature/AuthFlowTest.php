<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_login_page(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Авторизация')
            ->assertSee('Тестовый доступ: admin / admin12345');
    }

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create([
            'login' => 'admin',
            'password' => Hash::make('admin12345'),
            'role' => 'admin',
        ]);

        $this->post(route('login.attempt'), [
            'login' => 'admin',
            'password' => 'admin12345',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_login_fails_with_invalid_password(): void
    {
        User::factory()->create([
            'login' => 'admin',
            'password' => Hash::make('admin12345'),
        ]);

        $this->from(route('login'))->post(route('login.attempt'), [
            'login' => 'admin',
            'password' => 'wrong-password',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }
}
