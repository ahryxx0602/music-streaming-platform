<?php

namespace Modules\Administration\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\SystemSetting;
use App\Helpers\Setting;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminSettingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;
    protected $regularAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $this->superAdmin = User::factory()->create(['role' => 'super-admin']);
        $this->superAdmin->assignRole('super-admin');

        $this->regularAdmin = User::factory()->create(['role' => 'admin']);
        $this->regularAdmin->assignRole('admin');

        // Seed basic settings
        SystemSetting::insert([
            ['key' => 'site_name', 'value' => 'MusicStream', 'type' => 'string', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'artist_revenue_share', 'value' => '70', 'type' => 'integer', 'group' => 'financial', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_upload_size', 'value' => '52428800', 'type' => 'integer', 'group' => 'uploads', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function test_super_admin_can_get_settings()
    {
        $response = $this->actingAs($this->superAdmin)->getJson('/api/v1/admin/settings');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data']);
    }

    public function test_regular_admin_cannot_access_settings()
    {
        $response = $this->actingAs($this->regularAdmin)->getJson('/api/v1/admin/settings');

        $response->assertStatus(403);
    }

    public function test_super_admin_can_bulk_update_settings()
    {
        $response = $this->actingAs($this->superAdmin)->putJson('/api/v1/admin/settings', [
            'settings' => [
                'site_name' => 'New Platform Name',
                'artist_revenue_share' => '75',
            ]
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('system_settings', [
            'key' => 'site_name',
            'value' => 'New Platform Name',
        ]);

        $this->assertDatabaseHas('system_settings', [
            'key' => 'artist_revenue_share',
            'value' => '75',
        ]);
    }

    public function test_cache_is_cleared_after_update()
    {
        // Warm up the cache first
        Cache::put('system_settings', ['site_name' => 'OldCache'], 3600);

        $this->actingAs($this->superAdmin)->putJson('/api/v1/admin/settings', [
            'settings' => ['site_name' => 'AfterUpdate']
        ]);

        // Cache must be cleared
        $this->assertFalse(Cache::has('system_settings'));
    }

    public function test_setting_helper_get_works()
    {
        // Helper phải đọc từ DB qua Cache
        $value = Setting::get('site_name', 'default');

        $this->assertEquals('MusicStream', $value);
    }

    public function test_setting_helper_returns_default_for_unknown_key()
    {
        $value = Setting::get('non_existent_key', 'fallback');

        $this->assertEquals('fallback', $value);
    }

    public function test_audit_log_is_created_on_update()
    {
        $this->actingAs($this->superAdmin)->putJson('/api/v1/admin/settings', [
            'settings' => ['site_name' => 'AuditTest']
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->superAdmin->id,
            'action' => 'updated',
            'auditable_type' => SystemSetting::class,
        ]);
    }
}
