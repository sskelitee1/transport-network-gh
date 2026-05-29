<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Managed User',
            'gender' => 'M',
            'birth_date' => '1995-03-15',
            'email' => 'managed@example.com',
            'login' => 'managed',
            'password' => 'secret123',
            'role' => 'user',
        ])->assertRedirect(route('users.index'));

        $managedUser = User::query()->where('login', 'managed')->firstOrFail();

        $this->assertTrue(Hash::check('secret123', $managedUser->password));

        $this->actingAs($admin)->patch(route('users.update', $managedUser), [
            'name' => 'Updated User',
            'gender' => 'F',
            'birth_date' => '1996-04-16',
            'email' => 'updated@example.com',
            'login' => 'updated',
            'password' => '',
            'role' => 'user',
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $managedUser->id,
            'name' => 'Updated User',
            'login' => 'updated',
            'email' => 'updated@example.com',
        ]);

        $this->actingAs($admin)->delete(route('users.destroy', $managedUser))
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseMissing('users', ['id' => $managedUser->id]);
    }

    public function test_admin_cannot_delete_main_admin_account(): void
    {
        $admin = User::factory()->create([
            'login' => 'admin',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)->delete(route('users.destroy', $admin))
            ->assertRedirect(route('users.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'login' => 'admin',
        ]);
    }

    public function test_user_validation_rejects_duplicate_login(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['login' => 'duplicate']);

        $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Managed User',
            'gender' => 'M',
            'birth_date' => '1995-03-15',
            'email' => 'new@example.com',
            'login' => 'duplicate',
            'password' => 'secret123',
            'role' => 'user',
        ])->assertSessionHasErrors('login');
    }
}
