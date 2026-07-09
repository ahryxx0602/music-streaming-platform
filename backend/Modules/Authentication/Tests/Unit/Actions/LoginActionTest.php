<?php

namespace Modules\Authentication\Tests\Unit\Actions;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Authentication\Actions\LoginAction;
use Tests\TestCase;

class LoginActionTest extends TestCase
{
    use RefreshDatabase;

    private LoginAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new LoginAction();
    }

    public function test_it_returns_user_on_valid_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'status' => 'Active',
        ]);

        $result = $this->action->execute([
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertEquals($user->id, $result->id);
    }

    public function test_it_throws_validation_exception_on_invalid_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $this->expectException(ValidationException::class);

        $this->action->execute([
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);
    }

    public function test_it_throws_validation_exception_if_user_is_not_active()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'status' => 'Suspended',
        ]);

        $this->expectException(ValidationException::class);

        $this->action->execute([
            'email' => $user->email,
            'password' => 'password123',
        ]);
        
        $this->assertGuest();
    }
}
