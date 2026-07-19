<?php

namespace Modules\Playlist\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Playlist\Models\Playlist;
use Modules\Music\Models\Song;
use App\Models\User;
use Tests\TestCase;

class AdminPlaylistControllerTest extends TestCase
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
    public function test_admin_can_create_system_playlist_with_songs_in_order()
    {
        $song1 = Song::factory()->create(['artist_id' => $this->artistProfile->id, 'genre_id' => $this->genre->id, 'status' => 'Approved']);
        $song2 = Song::factory()->create(['artist_id' => $this->artistProfile->id, 'genre_id' => $this->genre->id, 'status' => 'Approved']);

        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/playlists', [
            'title' => 'Top 50 V-Pop',
            'privacy' => 'Public',
            'song_ids' => [$song2->id, $song1->id] // song2 ở vị trí 0, song1 ở vị trí 1
        ]);

        $response->assertStatus(201);
        
        $this->assertDatabaseHas('playlists', [
            'title' => 'Top 50 V-Pop',
            'type' => 'system',
            'user_id' => $this->admin->id
        ]);

        $playlistId = $response->json('data.playlist.id');

        // Check pivot table for correct positions
        $this->assertDatabaseHas('playlist_songs', [
            'playlist_id' => $playlistId,
            'song_id' => $song2->id,
            'position' => 0
        ]);

        $this->assertDatabaseHas('playlist_songs', [
            'playlist_id' => $playlistId,
            'song_id' => $song1->id,
            'position' => 1
        ]);
    }

    /** @test */
    public function test_admin_can_update_and_reorder_songs_in_playlist()
    {
        $playlist = Playlist::factory()->create([
            'type' => 'system',
            'user_id' => $this->admin->id,
            'title' => 'Old Title'
        ]);

        $song1 = Song::factory()->create(['artist_id' => $this->artistProfile->id, 'genre_id' => $this->genre->id]);
        $song2 = Song::factory()->create(['artist_id' => $this->artistProfile->id, 'genre_id' => $this->genre->id]);

        // Gán lúc đầu
        $playlist->songs()->attach([
            $song1->id => ['position' => 0],
            $song2->id => ['position' => 1],
        ]);

        // Cập nhật lại, đảo ngược thứ tự và đổi tên
        $response = $this->actingAs($this->admin)->putJson("/api/v1/admin/playlists/{$playlist->id}", [
            'title' => 'New Title',
            'song_ids' => [$song2->id, $song1->id]
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('playlists', [
            'id' => $playlist->id,
            'title' => 'New Title'
        ]);

        // Check positions updated
        $this->assertDatabaseHas('playlist_songs', [
            'playlist_id' => $playlist->id,
            'song_id' => $song2->id,
            'position' => 0
        ]);
        
        $this->assertDatabaseHas('playlist_songs', [
            'playlist_id' => $playlist->id,
            'song_id' => $song1->id,
            'position' => 1
        ]);
    }
}
