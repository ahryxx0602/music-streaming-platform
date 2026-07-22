<?php

namespace Modules\Music\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Music\Models\Album;
use Modules\Music\Models\Song;
use Modules\Music\Models\Genre;
use App\Models\User;
use Modules\Artist\Models\ArtistProfile;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ArtistAlbumControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $artistUser;
    protected $artistProfile;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::firstOrCreate(['name' => 'artist', 'guard_name' => 'web']);
        
        $this->artistUser = User::factory()->create([
            'role' => 'artist'
        ]);
        $this->artistUser->assignRole('artist');

        $this->artistProfile = ArtistProfile::create([
            'user_id' => $this->artistUser->id,
            'stage_name' => 'The Great Artist',
            'bio' => 'Bio'
        ]);

        Storage::fake('public');
    }

    public function test_artist_can_create_album()
    {
        $genre = Genre::create(['name' => 'Pop', 'slug' => 'pop']);
        $song1 = Song::create(['artist_id' => $this->artistProfile->id, 'genre_id' => $genre->id, 'title' => 'Song 1', 'duration' => 120]);
        $song2 = Song::create(['artist_id' => $this->artistProfile->id, 'genre_id' => $genre->id, 'title' => 'Song 2', 'duration' => 180]);

        $coverImage = UploadedFile::fake()->create('album_cover.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->artistUser)->postJson('/api/v1/artist/albums', [
            'title' => 'My First Album',
            'type' => 'Album',
            'cover_image' => $coverImage,
            'song_ids' => [$song1->id, $song2->id]
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'My First Album');

        $this->assertDatabaseHas('albums', [
            'title' => 'My First Album',
            'type' => 'Album',
            'artist_id' => $this->artistProfile->id
        ]);

        $this->assertDatabaseHas('songs', [
            'id' => $song1->id,
            'track_number' => 1
        ]);
        
        $this->assertDatabaseHas('songs', [
            'id' => $song2->id,
            'track_number' => 2
        ]);
    }

    public function test_artist_can_reorder_songs()
    {
        $album = Album::create([
            'artist_id' => $this->artistProfile->id,
            'title' => 'Test Album',
            'type' => 'Album'
        ]);

        $genre = Genre::create(['name' => 'Pop', 'slug' => 'pop']);
        $song1 = Song::create(['artist_id' => $this->artistProfile->id, 'album_id' => $album->id, 'track_number' => 1, 'genre_id' => $genre->id, 'title' => 'Song 1']);
        $song2 = Song::create(['artist_id' => $this->artistProfile->id, 'album_id' => $album->id, 'track_number' => 2, 'genre_id' => $genre->id, 'title' => 'Song 2']);
        $song3 = Song::create(['artist_id' => $this->artistProfile->id, 'album_id' => $album->id, 'track_number' => 3, 'genre_id' => $genre->id, 'title' => 'Song 3']);

        $response = $this->actingAs($this->artistUser)->putJson("/api/v1/artist/albums/{$album->id}/reorder-songs", [
            'song_ids' => [$song3->id, $song1->id, $song2->id]
        ]);

        $response->assertStatus(200);

        // Verify the new track numbers
        $this->assertDatabaseHas('songs', ['id' => $song3->id, 'track_number' => 1]);
        $this->assertDatabaseHas('songs', ['id' => $song1->id, 'track_number' => 2]);
        $this->assertDatabaseHas('songs', ['id' => $song2->id, 'track_number' => 3]);
    }

    public function test_artist_can_delete_album()
    {
        $album = Album::create([
            'artist_id' => $this->artistProfile->id,
            'title' => 'To Be Deleted',
            'type' => 'EP'
        ]);

        $genre = Genre::create(['name' => 'Pop', 'slug' => 'pop']);
        $song = Song::create(['artist_id' => $this->artistProfile->id, 'album_id' => $album->id, 'track_number' => 1, 'genre_id' => $genre->id, 'title' => 'Song inside album']);

        $response = $this->actingAs($this->artistUser)->deleteJson("/api/v1/artist/albums/{$album->id}");

        $response->assertStatus(200);

        // The album should be soft deleted
        $this->assertSoftDeleted('albums', ['id' => $album->id]);

        // The song's album_id should be null and track_number reset to 0
        $this->assertDatabaseHas('songs', [
            'id' => $song->id,
            'album_id' => null,
            'track_number' => 0
        ]);
    }
}
