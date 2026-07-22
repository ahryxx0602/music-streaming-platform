<?php

namespace Modules\Administration\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\AuditLog;
use Modules\Music\Models\Song;
use Modules\Music\Models\Genre;
use Modules\Artist\Models\ArtistProfile;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminAuditLogControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $artistUser;
    protected $artistProfile;
    protected $genre;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'artist', 'guard_name' => 'web']);
        
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->admin->assignRole('admin');

        $this->artistUser = User::factory()->create(['role' => 'artist']);
        $this->artistUser->assignRole('artist');

        $this->artistProfile = ArtistProfile::create([
            'user_id' => $this->artistUser->id,
            'stage_name' => 'Test Artist'
        ]);

        $this->genre = Genre::create(['name' => 'Pop', 'slug' => 'pop']);
    }

    public function test_creating_and_updating_song_generates_audit_logs()
    {
        // Act as artist to create song
        $this->actingAs($this->artistUser);

        $song = Song::create([
            'title' => 'My Original Title',
            'artist_id' => $this->artistProfile->id,
            'genre_id' => $this->genre->id,
            'duration' => 200
        ]);

        // Check if audit log was created for 'created' event
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->artistUser->id,
            'action' => 'created',
            'auditable_type' => Song::class,
            'auditable_id' => $song->id
        ]);

        // Update the song
        $song->update(['title' => 'My Updated Title']);

        // Check if audit log was created for 'updated' event
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->artistUser->id,
            'action' => 'updated',
            'auditable_type' => Song::class,
            'auditable_id' => $song->id
        ]);

        // Verify old_values and new_values for the update
        $updateLog = AuditLog::where('action', 'updated')->where('auditable_id', $song->id)->first();
        $this->assertEquals('My Original Title', $updateLog->old_values['title']);
        $this->assertEquals('My Updated Title', $updateLog->new_values['title']);
    }

    public function test_admin_can_view_audit_logs()
    {
        // Generate a log
        $this->actingAs($this->artistUser);
        Song::create([
            'title' => 'Audit me',
            'artist_id' => $this->artistProfile->id,
            'genre_id' => $this->genre->id
        ]);

        // Act as admin to view logs
        $this->actingAs($this->admin);
        
        $response = $this->getJson('/api/v1/admin/audit-logs');

        $response->assertStatus(200)
            ->assertJsonPath('data.data.0.action', 'created')
            ->assertJsonPath('data.data.0.auditable_type', Song::class);
    }
}
