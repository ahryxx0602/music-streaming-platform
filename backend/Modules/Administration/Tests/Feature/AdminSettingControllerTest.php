<?php

namespace Modules\Administration\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Modules\Administration\Models\Setting;
use Modules\Administration\Models\AuditLog;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminSettingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup Roles
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $adminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $permission = Permission::firstOrCreate(['name' => 'manage_settings', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($permission);

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->admin->assignRole($adminRole);
    }

    /** @test */
    public function test_can_get_system_settings()
    {
        Setting::create(['key' => 'site_name', 'value' => 'Harmonia Test', 'description' => 'Tên test']);

        $response = $this->actingAs($this->admin)->getJson('/api/v1/admin/settings');
        
        $response->assertStatus(200);
        $response->assertJsonPath('data.site_name', 'Harmonia Test');
    }

    /** @test */
    public function test_can_bulk_update_settings_and_clear_cache()
    {
        Cache::shouldReceive('forget')->once()->with('system_settings');
        Cache::shouldReceive('get')->once()->with('system_settings', [])->andReturn([]);
        Cache::shouldReceive('rememberForever')->andReturn([]); // for index if needed, but not called here

        $payload = [
            'settings' => [
                'site_name' => 'New Name',
                'maintenance_mode' => true,
                'payout_threshold' => 100
            ]
        ];

        $response = $this->actingAs($this->admin)->putJson('/api/v1/admin/settings', $payload);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('settings', [
            'key' => 'site_name',
            'value' => 'New Name'
        ]);
        
        $this->assertDatabaseHas('settings', [
            'key' => 'maintenance_mode',
            'value' => 'true'
        ]);
        
        // Assert audit log was created manually
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action' => 'updated',
            'entity_type' => Setting::class,
            'entity_id' => 0
        ]);
    }
}
