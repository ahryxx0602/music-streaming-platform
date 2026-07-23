<?php

namespace Modules\Music\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Modules\Music\Models\Song;
use Modules\Artist\Models\ArtistProfile;
use Modules\Music\Models\Genre;
use Tests\TestCase;

class ListenerSongControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $artistProfile;
    protected $song;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        
        $this->artistProfile = ArtistProfile::create([
            'user_id' => $user->id,
            'stage_name' => 'The Weekend',
            'total_streams' => 100
        ]);

        $genre = Genre::create(['name' => 'R&B', 'slug' => 'rnb']);

        $this->song = Song::create([
            'title' => 'Blinding Lights',
            'artist_id' => $this->artistProfile->id,
            'genre_id' => $genre->id,
            'play_count' => 500,
            'status' => 'published',
            'duration' => 200,
            'original_file_path' => 'songs/blinding-lights.mp3',
            'cover_image' => 'covers/blinding-lights.jpg'
        ]);
    }

    public function test_listener_can_get_song_details_for_player()
    {
        $response = $this->getJson('/api/v1/listener/songs/' . $this->song->id);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'Blinding Lights')
            ->assertJsonPath('data.artist_name', 'The Weekend')
            ->assertJsonPath('data.play_count', 500);
            
        $this->assertStringContainsString('songs/blinding-lights.mp3', $response->json('data.audio_url'));
        $this->assertStringContainsString('covers/blinding-lights.jpg', $response->json('data.cover_url'));
    }

    public function test_tracking_play_increments_song_and_artist_streams()
    {
        $response = $this->postJson('/api/v1/listener/songs/' . $this->song->id . '/track-play');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.play_count', 501);

        $this->assertDatabaseHas('songs', [
            'id' => $this->song->id,
            'play_count' => 501
        ]);

        $this->assertDatabaseHas('artist_profiles', [
            'id' => $this->artistProfile->id,
            'total_streams' => 101
        ]);
    }

    public function test_rate_limiting_on_track_play()
    {
        // Gửi request đầu tiên (Thành công)
        $response1 = $this->postJson('/api/v1/listener/songs/' . $this->song->id . '/track-play');
        $response1->assertStatus(200);

        // Gửi request thứ 2 ngay lập tức (Bị chặn 429 Too Many Requests)
        $response2 = $this->postJson('/api/v1/listener/songs/' . $this->song->id . '/track-play');
        $response2->assertStatus(429);
    }
}
