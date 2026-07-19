<?php

namespace Modules\Administration\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\Administration\Models\Banner;
use App\Models\User;
use Tests\TestCase;

class AdminBannerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
        Storage::fake('public');
    }

    /** @test */
    public function test_admin_can_create_banner_with_image_and_cache_is_cleared()
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('explore_banners');

        $file = UploadedFile::fake()->create('banner.jpg', 10, 'image/jpeg');

        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/banners', [
            'title' => 'Summer Sale',
            'image' => $file,
            'is_active' => true,
        ]);

        $response->assertStatus(201);
        
        $this->assertDatabaseHas('banners', [
            'title' => 'Summer Sale',
            'is_active' => 1,
            'order' => 0
        ]);

        $banner = Banner::first();
        Storage::disk('public')->assertExists($banner->image_url);
    }

    /** @test */
    public function test_admin_can_reorder_banners_and_cache_is_cleared()
    {
        $banner1 = Banner::factory()->create(['order' => 0]);
        $banner2 = Banner::factory()->create(['order' => 1]);
        $banner3 = Banner::factory()->create(['order' => 2]);

        Cache::shouldReceive('forget')
            ->once()
            ->with('explore_banners');

        $response = $this->actingAs($this->admin)->putJson('/api/v1/admin/banners/reorder', [
            'banner_ids' => [$banner3->id, $banner1->id, $banner2->id] // 3 lên đầu
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('banners', ['id' => $banner3->id, 'order' => 0]);
        $this->assertDatabaseHas('banners', ['id' => $banner1->id, 'order' => 1]);
        $this->assertDatabaseHas('banners', ['id' => $banner2->id, 'order' => 2]);
    }
}
