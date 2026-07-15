<?php

namespace Modules\Authentication\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_sends_reset_link()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/guest/auth/password/forgot', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => __('passwords.sent'),
        ]);
    }

    public function test_reset_password_updates_password()
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $response = $this->postJson('/api/v1/guest/auth/password/reset', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => __('passwords.reset'),
        ]);
    }
}
