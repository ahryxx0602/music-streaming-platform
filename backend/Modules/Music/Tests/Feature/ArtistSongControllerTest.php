<?php

namespace Modules\Music\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Music\Models\Genre;
use App\Models\User;
use Modules\Artist\Models\ArtistProfile;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ArtistSongControllerTest extends TestCase
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
            'stage_name' => 'Artist One',
            'bio' => 'A great artist'
        ]);

        Storage::fake('public');
    }

    public function test_artist_can_generate_presigned_url()
    {
        $s3ClientMock = \Mockery::mock('Aws\S3\S3Client');
        $s3ClientMock->shouldReceive('getCommand')->andReturn(new \Aws\Command('PutObject'));
        
        $uriMock = \Mockery::mock('Psr\Http\Message\UriInterface');
        $uriMock->shouldReceive('__toString')->andReturn('http://s3.local/presigned-url');
        
        $requestMock = \Mockery::mock('Psr\Http\Message\RequestInterface');
        $requestMock->shouldReceive('getUri')->andReturn($uriMock);
        
        $s3ClientMock->shouldReceive('createPresignedRequest')->andReturn($requestMock);

        \Illuminate\Support\Facades\Storage::extend('s3_mock', function () use ($s3ClientMock) {
            return new class($s3ClientMock) {
                public function __construct(public $client) {}
                public function getClient() { return $this->client; }
            };
        });
        config(['filesystems.disks.s3.driver' => 's3_mock']);

        $response = $this->actingAs($this->artistUser)->postJson('/api/v1/artist/songs/presigned-url', [
            'file_name' => 'song.mp3',
            'content_type' => 'audio/mpeg'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'url',
                    'path'
                ]
            ]);
            
        $this->assertStringContainsString('songs/raw/', $response->json('data.path'));
    }

    public function test_artist_can_store_song_metadata()
    {
        \Illuminate\Support\Facades\Queue::fake();

        $genre = Genre::create(['name' => 'Pop', 'slug' => 'pop']);
        $coverImage = UploadedFile::fake()->create('cover.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->artistUser)->postJson('/api/v1/artist/songs', [
            'title' => 'My New Hit',
            'genre_id' => $genre->id,
            'audio_path' => 'songs/raw/2026/07/uuid.mp3',
            'cover_image' => $coverImage,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => 'My New Hit',
                    'status' => 'pending',
                    'processing_status' => 'pending',
                ]
            ]);

        $this->assertDatabaseHas('songs', [
            'title' => 'My New Hit',
            'artist_id' => $this->artistProfile->id,
            'genre_id' => $genre->id,
            'original_file_path' => 'songs/raw/2026/07/uuid.mp3',
            'status' => 'pending',
            'processing_status' => 'pending',
            'duration' => 0
        ]);
    }
}
