<?php

namespace Modules\Artist\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Modules\Artist\Models\ArtistProfile;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ArtistProfileControllerTest extends TestCase
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
            'stage_name' => 'Original Name',
            'bio' => 'Original Bio',
            'social_links' => ['facebook' => 'fb.com']
        ]);

        Storage::fake('public');
    }

    public function test_artist_can_view_own_profile()
    {
        $response = $this->actingAs($this->artistUser)->getJson('/api/v1/artist/profile');

        $response->assertStatus(200)
            ->assertJsonPath('data.stage_name', 'Original Name')
            ->assertJsonPath('data.bio', 'Original Bio');
    }

    public function test_artist_can_update_profile_without_images()
    {
        $response = $this->actingAs($this->artistUser)->postJson('/api/v1/artist/profile', [
            'stage_name' => 'New Stage Name',
            'bio' => 'New Bio Updated',
            'contact_email' => 'booking@artist.com',
            'social_links' => json_encode(['instagram' => 'ig.com', 'youtube' => 'yt.com'])
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.stage_name', 'New Stage Name')
            ->assertJsonPath('data.contact_email', 'booking@artist.com');

        $this->assertDatabaseHas('artist_profiles', [
            'user_id' => $this->artistUser->id,
            'stage_name' => 'New Stage Name',
            'contact_email' => 'booking@artist.com'
        ]);

        $this->assertEquals(['instagram' => 'ig.com', 'youtube' => 'yt.com'], $response->json('data.social_links'));
    }

    public function test_artist_can_upload_avatar_and_banner()
    {
        $avatar = UploadedFile::fake()->create('avatar.jpg', 500, 'image/jpeg');
        $banner = UploadedFile::fake()->create('banner.jpg', 1500, 'image/jpeg');

        $response = $this->actingAs($this->artistUser)->postJson('/api/v1/artist/profile', [
            'stage_name' => 'Image Uploader',
            'avatar' => $avatar,
            'banner' => $banner
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.stage_name', 'Image Uploader');

        $profile = ArtistProfile::where('user_id', $this->artistUser->id)->first();
        
        $this->assertNotNull($profile->avatar_url);
        $this->assertNotNull($profile->banner_url);
        
        Storage::disk('public')->assertExists($profile->avatar_url);
        Storage::disk('public')->assertExists($profile->banner_url);
    }
}
