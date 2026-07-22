<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Clear cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 14 permissions matrix
        $permissions = [
            'view_dashboard',
            'view_audit_logs',
            'view_users',
            'manage_users',
            'delete_users',
            'manage_invites',
            'view_inventory',
            'manage_inventory',
            'moderate_songs',
            'manage_genres',
            'manage_playlists',
            'manage_banners',
            'manage_settings',
            'manage_roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // The Super Admin gets everything
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // Normal Admin gets most things but not role management or deleting users
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminPermissions = Permission::whereNotIn('name', ['delete_users', 'manage_roles'])->get();
        $adminRole->syncPermissions($adminPermissions);

        // A Moderator gets content management
        $moderatorRole = Role::firstOrCreate(['name' => 'Content Moderator', 'guard_name' => 'web']);
        $moderatorRole->syncPermissions([
            'view_dashboard',
            'view_inventory',
            'manage_inventory',
            'moderate_songs',
            'manage_genres',
            'manage_playlists',
            'manage_banners',
        ]);
    }
}
