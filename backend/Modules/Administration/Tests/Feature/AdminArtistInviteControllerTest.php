<?php

namespace Modules\Administration\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Artist\Models\ArtistInvitation;
use Tests\TestCase;

class AdminArtistInviteControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    private function authenticateAdmin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        return $this->actingAs($admin, 'sanctum');
    }

    public function test_admin_can_get_invites_list()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        for ($i = 0; $i < 5; $i++) {
            ArtistInvitation::create([
                'email' => "test$i@example.com",
                'token' => \Illuminate\Support\Str::random(64),
                'expires_at' => now()->addDays(7),
                'created_by' => $admin->id
            ]);
        }

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/artist-invites');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'current_page',
                    'data' => [
                        '*' => [
                            'id',
                            'email',
                            'token',
                            'expires_at',
                            'used_at',
                            'created_by',
                            'status',
                            'created_by' 
                        ]
                    ]
                ]
            ]);

        $this->assertCount(5, $response->json('data.data'));
    }

    public function test_admin_can_create_invite_with_url()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $payload = [
            'email' => 'singer@example.com',
            'expires_in_days' => 7
        ];

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/admin/artist-invites', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'invitation' => [
                        'id',
                        'email',
                        'token',
                        'expires_at'
                    ],
                    'registration_url'
                ]
            ]);

        $this->assertDatabaseHas('artist_invitations', [
            'email' => 'singer@example.com',
            'created_by' => $admin->id
        ]);

        $token = $response->json('data.invitation.token');
        $this->assertEquals(64, strlen($token));
        
        $url = $response->json('data.registration_url');
        $this->assertStringContainsString('/artist-register?token=' . $token, $url);
    }

    public function test_admin_can_revoke_unused_invite()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $invitation = ArtistInvitation::create([
            'email' => 'test@example.com',
            'token' => \Illuminate\Support\Str::random(64),
            'expires_at' => now()->addDays(7),
            'created_by' => $admin->id,
            'used_at' => null
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson('/api/v1/admin/artist-invites/' . $invitation->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Đã thu hồi và xóa mã mời.'
            ]);

        $this->assertDatabaseMissing('artist_invitations', [
            'id' => $invitation->id
        ]);
    }

    public function test_admin_cannot_revoke_used_invite()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $invitation = ArtistInvitation::create([
            'email' => 'test@example.com',
            'token' => \Illuminate\Support\Str::random(64),
            'expires_at' => now()->addDays(7),
            'created_by' => $admin->id,
            'used_at' => now()
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson('/api/v1/admin/artist-invites/' . $invitation->id);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Không thể thu hồi mã mời đã được sử dụng.'
            ]);

        $this->assertDatabaseHas('artist_invitations', [
            'id' => $invitation->id
        ]);
    }
}
