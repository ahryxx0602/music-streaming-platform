<?php

namespace Modules\Authentication\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProtectedAuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_me_api_returns_unauthorized_if_guest()
    {
        $response = $this->getJson('/api/v1/listener/auth/me');
        $response->assertStatus(401);
    }

    public function test_me_api_returns_user_data_if_authenticated()
    {
        $user = User::factory()->create([
            'role' => 'listener',
            'status' => 'Active',
        ]);

        $response = $this->actingAs($user)->getJson('/api/v1/listener/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('data.user.email', $user->email);
    }

    public function test_me_api_loads_artist_profile_if_role_is_artist()
    {
        $user = User::factory()->create([
            'role' => 'artist',
            'status' => 'Active',
        ]);

        $user->artistProfile()->create([
            'stage_name' => 'The Great Artist',
        ]);

        $response = $this->actingAs($user)->getJson('/api/v1/artist/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('data.user.email', $user->email)
            ->assertJsonPath('data.user.artist_profile.stage_name', 'The Great Artist');
    }

    public function test_logout_api_invalidates_session()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'web')
            ->withHeader('referer', 'http://localhost:5173')
            ->postJson('/api/v1/listener/auth/logout');

        $response->assertStatus(204);
        $this->assertGuest('web');
    }
}
