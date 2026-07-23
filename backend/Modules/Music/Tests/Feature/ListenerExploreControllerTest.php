<?php

namespace Modules\Music\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use Modules\Music\Models\Song;
use Modules\Artist\Models\ArtistProfile;
use Modules\Administration\Models\Banner;
use Modules\Playlist\Models\Playlist;
use Modules\Music\Models\Genre;
use Tests\TestCase;

class ListenerExploreControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_listener_explore_returns_aggregated_data_with_cache()
    {
        // 1. Seed Banners
        Banner::create(['title' => 'Banner 1', 'image_url' => 'banner1.jpg', 'target_url' => '/link', 'order' => 1, 'is_active' => true]);

        // 2. Seed Artists
        $user1 = User::factory()->create();
        $artist1 = ArtistProfile::create(['user_id' => $user1->id, 'stage_name' => 'Artist A', 'total_streams' => 1000]);

        // 3. Seed Songs
        $genre = Genre::create(['name' => 'Pop', 'slug' => 'pop']);
        Song::create(['title' => 'Song 1', 'artist_id' => $artist1->id, 'genre_id' => $genre->id, 'status' => 'published', 'play_count' => 100]);

        // 4. Seed Playlists
        $user2 = User::factory()->create();
        Playlist::create(['user_id' => $user2->id, 'title' => 'Hot Hits', 'type' => 'system']);

        // Assert Cache is empty initially
        $this->assertFalse(Cache::has('home_explore_data'));

        // First request - Should fetch from DB and save to cache
        $response = $this->getJson('/api/v1/listener/home-explore');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'banners',
                    'new_releases',
                    'trending_artists',
                    'featured_playlists'
                ]
            ]);

        // Verify content
        $this->assertEquals('Banner 1', $response->json('data.banners.0.title'));
        $this->assertEquals('Song 1', $response->json('data.new_releases.0.title'));
        $this->assertEquals('Artist A', $response->json('data.trending_artists.0.stage_name'));
        $this->assertEquals('Hot Hits', $response->json('data.featured_playlists.0.name'));

        // Assert Cache now has the key
        $this->assertTrue(Cache::has('home_explore_data'));

        // Delete records from DB to prove it loads from cache
        Banner::truncate();
        Song::truncate();

        // Second request - Should fetch from Cache
        $cachedResponse = $this->getJson('/api/v1/listener/home-explore');
        $cachedResponse->assertStatus(200);

        // Data should still be 'Banner 1' and 'Song 1' despite DB being empty
        $this->assertEquals('Banner 1', $cachedResponse->json('data.banners.0.title'));
        $this->assertEquals('Song 1', $cachedResponse->json('data.new_releases.0.title'));
    }
}
