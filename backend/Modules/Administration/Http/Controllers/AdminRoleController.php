<?php

namespace Modules\Administration\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Administration\Http\Requests\RoleRequest;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminRoleController extends Controller
{
    protected array $systemRoles = ['super-admin', 'admin', 'artist', 'user'];

    /**
     * [API-ADM-33] Lấy danh sách Roles
     */
    public function index(): JsonResponse
    {
        // Get all roles with permissions and users count
        $roles = Role::with('permissions')
            ->withCount('users')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $roles
        ]);
    }

    /**
     * [API-ADM-34] Tạo Role mới
     */
    public function store(RoleRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web' // assuming web guard for users
        ]);

        if (isset($validated['permission_ids'])) {
            $permissions = Permission::whereIn('id', $validated['permission_ids'])->get();
            $role->syncPermissions($permissions);
        }

        // Return role with permissions for response consistency
        $role->load('permissions');

        return response()->json([
            'success' => true,
            'message' => 'Tạo Vai trò thành công.',
            'data' => $role
        ], 201);
    }

    /**
     * [API-ADM-35] Cập nhật Role
     */
    public function update(RoleRequest $request, $id): JsonResponse
    {
        $role = Role::findOrFail($id);

        if (in_array($role->name, $this->systemRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể chỉnh sửa các vai trò hệ thống.'
            ], 403);
        }

        $validated = $request->validated();
        
        // Update name
        $role->update([
            'name' => $validated['name']
        ]);

        // Sync permissions
        if (isset($validated['permission_ids'])) {
            $permissions = Permission::whereIn('id', $validated['permission_ids'])->get();
            $role->syncPermissions($permissions);
        } else {
            // If empty array passed or null, maybe we clear it, or keep it depending on API design.
            // Let's clear if it's explicitly passed as empty array. Wait, request validation 'nullable|array'. 
            // If it's provided in payload, sync it (even if empty). If not provided, don't change.
            if ($request->has('permission_ids')) {
                $role->syncPermissions([]);
            }
        }

        $role->load('permissions');

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật Vai trò thành công.',
            'data' => $role
        ]);
    }

    /**
     * [API-ADM-36] Xóa Role
     */
    public function destroy($id): JsonResponse
    {
        $role = Role::withCount('users')->findOrFail($id);

        if (in_array($role->name, $this->systemRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa các vai trò hệ thống.'
            ], 403);
        }

        if ($role->users_count > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa vai trò đang có người dùng.'
            ], 400); // Bad Request
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa Vai trò thành công.'
        ]);
    }

    /**
     * [API-ADM-37] Lấy danh sách Permissions
     */
    public function permissions(): JsonResponse
    {
        $permissions = Permission::all();

        return response()->json([
            'success' => true,
            'data' => $permissions
        ]);
    }
}
