<?php

namespace Modules\Administration\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminRoleControllerTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateSuperAdmin()
    {
        $superAdmin = \App\Models\User::factory()->create(['role' => 'super-admin']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->assignRole('super-admin');
        return $this->actingAs($superAdmin, 'sanctum');
    }

    public function test_superadmin_can_get_roles()
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        
        $response = $this->authenticateSuperAdmin()
            ->getJson('/api/v1/admin/roles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'guard_name',
                        'permissions',
                        'users_count'
                    ]
                ]
            ]);
    }

    public function test_superadmin_can_create_role_with_permissions()
    {
        $permission = Permission::firstOrCreate(['name' => 'manage_users', 'guard_name' => 'web']);
        
        $payload = [
            'name' => 'Marketing Team',
            'permission_ids' => [$permission->id]
        ];

        $response = $this->authenticateSuperAdmin()
            ->postJson('/api/v1/admin/roles', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Marketing Team');

        $this->assertDatabaseHas('roles', ['name' => 'Marketing Team']);
        
        $role = Role::where('name', 'Marketing Team')->first();
        $this->assertTrue($role->hasPermissionTo('manage_users'));
    }

    public function test_superadmin_cannot_update_system_roles()
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        
        $payload = [
            'name' => 'new_admin'
        ];

        $response = $this->authenticateSuperAdmin()
            ->putJson('/api/v1/admin/roles/' . $adminRole->id, $payload);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Không thể chỉnh sửa các vai trò hệ thống.'
            ]);
            
        $this->assertDatabaseHas('roles', ['name' => 'admin']);
    }

    public function test_superadmin_cannot_delete_system_roles()
    {
        $artistRole = Role::firstOrCreate(['name' => 'artist', 'guard_name' => 'web']);
        
        $response = $this->authenticateSuperAdmin()
            ->deleteJson('/api/v1/admin/roles/' . $artistRole->id);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Không thể xóa các vai trò hệ thống.'
            ]);
    }

    public function test_superadmin_cannot_delete_role_in_use()
    {
        $role = Role::firstOrCreate(['name' => 'moderator', 'guard_name' => 'web']);
        
        // Assign role to a user
        $user = \App\Models\User::factory()->create();
        $user->assignRole($role);

        $response = $this->authenticateSuperAdmin()
            ->deleteJson('/api/v1/admin/roles/' . $role->id);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Không thể xóa vai trò đang có người dùng.'
            ]);
    }
}
