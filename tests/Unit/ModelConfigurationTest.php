<?php

namespace Tests\Unit;

use App\Models\Driver;
use App\Models\Line;
use App\Models\Station;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Tests\TestCase;

class ModelConfigurationTest extends TestCase
{
    public function test_user_uses_login_as_auth_identifier_and_detects_admin_role(): void
    {
        $admin = new User(['role' => 'admin']);
        $user = new User(['role' => 'user']);

        $this->assertSame('login', $admin->getAuthIdentifierName());
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($user->isAdmin());
    }

    public function test_model_relationships_are_configured(): void
    {
        $line = new Line;
        $station = new Station;
        $vehicle = new Vehicle;
        $driver = new Driver;

        $this->assertInstanceOf(HasMany::class, $line->stations());
        $this->assertInstanceOf(HasMany::class, $line->vehicles());
        $this->assertInstanceOf(BelongsTo::class, $station->line());
        $this->assertInstanceOf(BelongsTo::class, $vehicle->line());
        $this->assertInstanceOf(HasOne::class, $vehicle->driver());
        $this->assertInstanceOf(BelongsTo::class, $driver->vehicle());
    }
}
