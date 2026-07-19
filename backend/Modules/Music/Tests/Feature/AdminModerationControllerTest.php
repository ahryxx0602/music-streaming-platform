<?php

namespace Modules\Music\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Music\Models\Song;
use App\Models\User;
use Tests\TestCase;

class AdminModerationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $artistProfile;
    protected $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
        
        $artistUser = User::factory()->create(['role' => 'artist']);
        $this->artistProfile = \Modules\Artist\Models\ArtistProfile::create([
            'user_id' => $artistUser->id,
            'stage_name' => 'Artist 1'
        ]);

        $this->genre = \Modules\Music\Models\Genre::create([
            'name' => 'Pop',
            'slug' => 'pop'
        ]);
    }

    /** @test */
    public function test_admin_can_get_pending_songs()
    {
        // Bài chờ duyệt, đã xử lý xong audio
        Song::factory()->create([
            'artist_id' => $this->artistProfile->id,
            'genre_id' => $this->genre->id,
            'status' => 'Pending',
            'processing_status' => 'completed',
            'title' => 'Pending Song 1'
        ]);

        // Bài chờ duyệt nhưng chưa xử lý xong audio -> Không được hiện
        Song::factory()->create([
            'artist_id' => $this->artistProfile->id,
            'genre_id' => $this->genre->id,
            'status' => 'Pending',
            'processing_status' => 'processing',
        ]);

        // Bài đã duyệt -> Không được hiện
        Song::factory()->create([
            'artist_id' => $this->artistProfile->id,
            'genre_id' => $this->genre->id,
            'status' => 'Approved',
            'processing_status' => 'completed',
        ]);

        $response = $this->actingAs($this->admin)->getJson('/api/v1/admin/moderation/songs');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data.data')
                 ->assertJsonPath('data.data.0.title', 'Pending Song 1');
    }

    /** @test */
    public function test_admin_can_approve_a_pending_song()
    {
        $song = Song::factory()->create([
            'artist_id' => $this->artistProfile->id,
            'genre_id' => $this->genre->id,
            'status' => 'Pending',
            'processing_status' => 'completed'
        ]);

        $response = $this->actingAs($this->admin)->putJson("/api/v1/admin/moderation/songs/{$song->id}/approve");

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('songs', [
            'id' => $song->id,
            'status' => 'Approved'
        ]);
    }

    /** @test */
    public function test_admin_can_reject_a_pending_song_with_reason()
    {
        $song = Song::factory()->create([
            'artist_id' => $this->artistProfile->id,
            'genre_id' => $this->genre->id,
            'status' => 'Pending',
            'processing_status' => 'completed'
        ]);

        $response = $this->actingAs($this->admin)->putJson("/api/v1/admin/moderation/songs/{$song->id}/reject", [
            'reject_reason' => 'Âm thanh bị rè, không đạt chất lượng.'
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('songs', [
            'id' => $song->id,
            'status' => 'Rejected',
            'rejected_reason' => 'Âm thanh bị rè, không đạt chất lượng.'
        ]);
    }

    /** @test */
    public function test_admin_cannot_approve_a_song_that_is_not_pending()
    {
        $song = Song::factory()->create([
            'artist_id' => $this->artistProfile->id,
            'genre_id' => $this->genre->id,
            'status' => 'Approved',
            'processing_status' => 'completed'
        ]);

        $response = $this->actingAs($this->admin)->putJson("/api/v1/admin/moderation/songs/{$song->id}/approve");

        $response->assertStatus(400); // Bad Request
    }
}
