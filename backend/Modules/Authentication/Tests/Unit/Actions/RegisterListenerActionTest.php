<?php

namespace Modules\Authentication\Tests\Unit\Actions;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Authentication\Actions\RegisterListenerAction;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegisterListenerActionTest extends TestCase
{
    use RefreshDatabase;

    private RegisterListenerAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new RegisterListenerAction();
        Role::create(['name' => 'listener']);
    }

    public function test_it_creates_listener_user_and_assigns_role()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $user = $this->action->execute($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('listener', $user->role);
        $this->assertTrue($user->hasRole('listener'));
    }
}
