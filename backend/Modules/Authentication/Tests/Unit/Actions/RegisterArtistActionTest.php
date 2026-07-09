<?php

namespace Modules\Authentication\Tests\Unit\Actions;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Artist\Models\ArtistInvitation;
use Modules\Authentication\Actions\RegisterArtistAction;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegisterArtistActionTest extends TestCase
{
    use RefreshDatabase;

    private RegisterArtistAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new RegisterArtistAction();
        Role::create(['name' => 'artist']);
    }

    public function test_it_creates_artist_user_and_profile_with_valid_token()
    {
        // Admin user for created_by
        $admin = User::factory()->create();
        
        $invitation = ArtistInvitation::create([
            'token' => 'valid-token-123',
            'expires_at' => now()->addDays(1),
            'created_by' => $admin->id,
        ]);

        $data = [
            'token' => 'valid-token-123',
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'stage_name' => 'Jane The Artist',
        ];

        $user = $this->action->execute($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('jane@example.com', $user->email);
        $this->assertEquals('artist', $user->role);
        $this->assertTrue($user->hasRole('artist'));
        
        $this->assertDatabaseHas('artist_profiles', [
            'user_id' => $user->id,
            'stage_name' => 'Jane The Artist',
        ]);

        $this->assertNotNull($invitation->fresh()->used_at);
    }

    public function test_it_throws_exception_if_token_is_invalid_or_expired()
    {
        $admin = User::factory()->create();
        
        // Expired token
        ArtistInvitation::create([
            'token' => 'expired-token',
            'expires_at' => now()->subDays(1),
            'created_by' => $admin->id,
        ]);

        $this->expectException(ValidationException::class);

        $this->action->execute([
            'token' => 'expired-token',
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'stage_name' => 'Jane The Artist',
        ]);
    }
}
