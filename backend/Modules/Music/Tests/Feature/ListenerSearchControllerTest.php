<?php

namespace Modules\Music\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Modules\Music\Models\Song;
use Modules\Artist\Models\ArtistProfile;
use Modules\Music\Models\Album;
use Modules\Music\Models\Genre;
use Tests\TestCase;

class ListenerSearchControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_search_returns_empty_when_keyword_too_short()
    {
        $response = $this->getJson('/api/v1/listener/search?q=a');

        $response->assertStatus(200)
            ->assertJsonPath('data.songs', [])
            ->assertJsonPath('data.artists', [])
            ->assertJsonPath('data.albums', []);
    }

    public function test_search_returns_aggregated_results()
    {
        // 1. Seed Artist
        $user1 = User::factory()->create(['name' => 'Michael']);
        $artist1 = ArtistProfile::create(['user_id' => $user1->id, 'stage_name' => 'Jackson']);

        // 2. Seed Album
        $album1 = Album::create([
            'title' => 'Thriller',
            'artist_id' => $artist1->id,
            'status' => 'published'
        ]);

        // 3. Seed Song
        $genre = Genre::create(['name' => 'Pop', 'slug' => 'pop']);
        $song1 = Song::create([
            'title' => 'Billie Jean',
            'artist_id' => $artist1->id,
            'album_id' => $album1->id,
            'genre_id' => $genre->id,
            'status' => 'published'
        ]);

        // Search with keyword that matches all of them indirectly (e.g. "ill" matches "Billie Jean", "Thriller", but not artist "Jackson")
        $response1 = $this->getJson('/api/v1/listener/search?q=ill');

        $response1->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'songs',
                    'artists',
                    'albums'
                ]
            ]);

        // Verify "ill" results
        $this->assertEquals('Billie Jean', $response1->json('data.songs.0.title'));
        $this->assertEmpty($response1->json('data.artists'));
        $this->assertEquals('Thriller', $response1->json('data.albums.0.title'));

        // Search for artist
        $response2 = $this->getJson('/api/v1/listener/search?q=Jack');
        
        // "Jack" matches stage_name "Jackson"
        $this->assertEquals('Jackson', $response2->json('data.artists.0.stage_name'));

        // Verify nested structure for songs
        $this->assertEquals('Jackson', $response1->json('data.songs.0.artist.stage_name'));
        
        // Verify nested structure for albums
        $this->assertEquals('Jackson', $response1->json('data.albums.0.artist.stage_name'));
    }
}
