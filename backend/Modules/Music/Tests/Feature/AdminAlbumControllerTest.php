<?php

namespace Modules\Music\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Artist\Models\ArtistProfile;
use Modules\Music\Models\Album;
use Modules\Music\Models\Genre;
use Modules\Music\Models\Song;
use Tests\TestCase;

class AdminAlbumControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $artistProfile;
    protected $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $artistUser = User::factory()->create(['role' => 'artist']);
        $this->artistProfile = ArtistProfile::create([
            'user_id' => $artistUser->id,
            'stage_name' => 'John Doe',
            'bio' => 'A famous artist.',
            'country' => 'Vietnam',
            'verified_at' => now(),
        ]);
        $this->genre = Genre::create(['name' => 'Pop', 'slug' => 'pop']);
        
        Storage::fake('public');
    }

    /** @test */
    public function test_admin_can_get_albums_list()
    {
        Album::create([
            'artist_id' => $this->artistProfile->id,
            'title' => 'Test Album',
            'type' => 'Album',
            'release_date' => '2023-01-01',
            'status' => 'Approved'
        ]);

        $response = $this->actingAs($this->admin)->getJson('/api/v1/admin/albums');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => ['data']]);
    }

    /** @test */
    public function test_admin_can_create_album()
    {
        $payload = [
            'artist_id' => $this->artistProfile->id,
            'title' => 'New Album',
            'type' => 'Album',
            'release_date' => '2023-05-05',
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/albums', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('albums', [
            'title' => 'New Album',
            'artist_id' => $this->artistProfile->id,
            'status' => 'Approved'
        ]);
    }

    /** @test */
    public function test_admin_can_update_album_and_assign_songs()
    {
        $album = Album::create([
            'artist_id' => $this->artistProfile->id,
            'title' => 'Old Album Title',
            'type' => 'Album',
            'release_date' => '2023-01-01',
            'status' => 'Approved'
        ]);

        $song = Song::factory()->create([
            'artist_id' => $this->artistProfile->id,
            'genre_id' => $this->genre->id,
            'status' => 'Approved'
        ]);

        $payload = [
            'title' => 'Updated Album Title',
            'song_ids' => [$song->id],
            '_method' => 'PUT'
        ];

        $response = $this->actingAs($this->admin)->postJson("/api/v1/admin/albums/{$album->id}", $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('albums', [
            'id' => $album->id,
            'title' => 'Updated Album Title',
        ]);
        $this->assertDatabaseHas('songs', [
            'id' => $song->id,
            'album_id' => $album->id,
        ]);
    }

    /** @test */
    public function test_admin_can_delete_album_and_reset_songs()
    {
        $album = Album::create([
            'artist_id' => $this->artistProfile->id,
            'title' => 'Album to delete',
            'type' => 'Album',
            'release_date' => '2023-01-01',
            'status' => 'Approved'
        ]);

        $song = Song::factory()->create([
            'artist_id' => $this->artistProfile->id,
            'genre_id' => $this->genre->id,
            'album_id' => $album->id,
            'status' => 'Approved'
        ]);

        $response = $this->actingAs($this->admin)->deleteJson("/api/v1/admin/albums/{$album->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('albums', [
            'id' => $album->id,
        ]);
        $this->assertDatabaseHas('songs', [
            'id' => $song->id,
            'album_id' => null,
        ]);
    }
}
