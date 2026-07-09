<?php

namespace Modules\Authentication\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Artist\Models\ArtistInvitation;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthRegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'listener']);
        Role::create(['name' => 'artist']);
    }

    public function test_listener_can_register_with_valid_data()
    {
        $response = $this->postJson('/api/v1/guest/auth/register', [
            'name' => 'John Listener',
            'email' => 'listener@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('users', [
            'email' => 'listener@example.com',
            'role' => 'listener',
        ]);
    }

    public function test_listener_register_validates_unique_email()
    {
        User::factory()->create(['email' => 'duplicate@example.com']);

        $response = $this->postJson('/api/v1/guest/auth/register', [
            'name' => 'John',
            'email' => 'duplicate@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    public function test_artist_can_register_with_valid_token()
    {
        $admin = User::factory()->create();
        
        $invitation = ArtistInvitation::create([
            'token' => 'secret-artist-token',
            'expires_at' => now()->addDays(1),
            'created_by' => $admin->id,
        ]);

        $response = $this->postJson('/api/v1/guest/auth/artist-register', [
            'token' => 'secret-artist-token',
            'name' => 'John Artist',
            'stage_name' => 'DJ John',
            'email' => 'artist@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => 'artist@example.com',
            'role' => 'artist',
        ]);

        $user = User::where('email', 'artist@example.com')->first();
        $this->assertDatabaseHas('artist_profiles', [
            'user_id' => $user->id,
            'stage_name' => 'DJ John',
        ]);
    }

    public function test_artist_register_validates_token()
    {
        $response = $this->postJson('/api/v1/guest/auth/artist-register', [
            'token' => 'invalid-token',
            'name' => 'John Artist',
            'stage_name' => 'DJ John',
            'email' => 'artist@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['token']);
    }

    public function test_artist_can_validate_token()
    {
        $admin = User::factory()->create();
        
        ArtistInvitation::create([
            'token' => 'secret-artist-token',
            'email' => 'sontung@mtpent.com',
            'expires_at' => now()->addDays(1),
            'created_by' => $admin->id,
        ]);

        $response = $this->getJson('/api/v1/guest/auth/artist-register/validate?token=secret-artist-token');

        $response->assertStatus(200)
            ->assertJsonPath('data.is_valid', true)
            ->assertJsonPath('data.email_hint', 'son****@mtpent.com');

        $response_invalid = $this->getJson('/api/v1/guest/auth/artist-register/validate?token=invalid');
        $response_invalid->assertStatus(200)
            ->assertJsonPath('data.is_valid', false)
            ->assertJsonPath('data.email_hint', null);
    }
}
