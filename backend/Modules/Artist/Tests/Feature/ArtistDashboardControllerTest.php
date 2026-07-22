<?php

namespace Modules\Artist\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Modules\Music\Models\Song;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ArtistDashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $artist;
    protected $artistProfileId;

    protected function setUp(): void
    {
        parent::setUp();
        
        $artistRole = Role::firstOrCreate(['name' => 'artist', 'guard_name' => 'web']);
        $this->artist = User::factory()->create(['role' => 'artist']);
        $this->artist->assignRole($artistRole);

        // Create artist profile
        $this->artistProfileId = DB::table('artist_profiles')->insertGetId([
            'user_id' => $this->artist->id,
            'stage_name' => 'Test Artist',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function test_artist_can_get_dashboard_stats()
    {
        $genreId = DB::table('genres')->insertGetId(['name' => 'Pop', 'slug' => 'pop']);
        Song::factory()->count(3)->create([
            'artist_id' => $this->artistProfileId,
            'genre_id' => $genreId,
            'play_count' => 1000
        ]);

        $response = $this->actingAs($this->artist)->getJson('/api/v1/artist/dashboard/stats');

        $response->assertStatus(200);
        $response->assertJsonPath('data.total_tracks', 3);
        $response->assertJsonPath('data.total_streams', 3000);
        $response->assertJsonPath('data.balance_usd', 30);
    }

    public function test_artist_can_get_top_tracks()
    {
        $genreId = DB::table('genres')->insertGetId(['name' => 'Pop', 'slug' => 'pop']);
        Song::factory()->create(['artist_id' => $this->artistProfileId, 'genre_id' => $genreId, 'title' => 'Track 1', 'play_count' => 10]);
        Song::factory()->create(['artist_id' => $this->artistProfileId, 'genre_id' => $genreId, 'title' => 'Track 2', 'play_count' => 50]);
        Song::factory()->create(['artist_id' => $this->artistProfileId, 'genre_id' => $genreId, 'title' => 'Track 3', 'play_count' => 30]);

        $response = $this->actingAs($this->artist)->getJson('/api/v1/artist/dashboard/top-tracks');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $this->assertEquals('Track 2', $response->json('data.0.title')); // Highest streams first
        $this->assertEquals('Track 3', $response->json('data.1.title'));
    }

    public function test_artist_can_get_streams_chart()
    {
        $response = $this->actingAs($this->artist)->getJson('/api/v1/artist/dashboard/streams-chart');

        $response->assertStatus(200);
        $response->assertJsonCount(30, 'data');
    }
}
