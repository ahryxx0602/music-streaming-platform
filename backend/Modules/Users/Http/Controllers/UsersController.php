<?php

namespace Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use App\Traits\ApiResponseTrait;
use Modules\Users\Filters\UserSearchFilter;
use Illuminate\Http\JsonResponse;
use Modules\Users\Http\Requests\StoreArtistRequest;
use Modules\Users\Http\Requests\StoreStaffRequest;
use Modules\Users\Http\Requests\UpdateUserRequest;
use Modules\Users\Http\Requests\UpdateUserStatusRequest;
use Modules\Users\Http\Requests\UpdateUserRolesRequest;
use Illuminate\Support\Facades\DB;
use Modules\Artist\Models\ArtistProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    use ApiResponseTrait;

    /**
     * Lấy danh sách Users cho Admin CMS (API-320)
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 15);

        $users = QueryBuilder::for(User::class)
            ->with('artistProfile')
            ->when($request->input('filter.role') === 'artist', function ($query) {
                $query->withCount('songs');
            })
            ->when($request->input('filter.role') === 'admin', function ($query) {
                $query->with('roles');
            })
            ->allowedFilters(
                'role',
                'status',
                AllowedFilter::custom('search', new UserSearchFilter())
            )
            ->allowedSorts(
                'name',
                'email',
                'created_at',
                AllowedSort::field('oldest', 'created_at')
            )
            ->defaultSort('-created_at') 
            ->paginate($perPage)
            ->appends($request->query());

        return $this->successResponse([
            'items' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ]
        ], 'Lấy danh sách người dùng thành công.');
    }

    /**
     * Lấy chi tiết user
     */
    public function show($id): JsonResponse
    {
        $user = User::with(['artistProfile', 'roles'])->findOrFail($id);

        return $this->successResponse([
            'user' => $user
        ], 'Lấy thông tin người dùng thành công.');
    }

    /**
     * Tạo nhanh Artist (API-321)
     */
    public function storeArtist(StoreArtistRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'artist',
                'status' => 'Active',
                'email_verified_at' => now(), 
            ]);
            
            $user->assignRole('Artist');

            ArtistProfile::create([
                'user_id' => $user->id,
                'stage_name' => $request->stage_name,
                'is_verified' => true,
                'verified_at' => now(),
            ]);

            DB::commit();

            return $this->successResponse(['user' => $user->load('artistProfile')], 'Tạo Artist thành công.', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Tạo Người nghe (Listener) (Admin cấp)
     */
    public function storeListener(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'listener',
                'status' => 'Active',
                'email_verified_at' => now(),
            ]);

            $user->assignRole('Listener');

            DB::commit();

            return $this->successResponse(['user' => $user], 'Tạo Người nghe thành công.', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Tạo Tài khoản Nhân viên (Staff) (API-327)
     */
    public function storeStaff(StoreStaffRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin',
                'status' => 'Active',
                'email_verified_at' => now(),
            ]);

            // Assign multiple roles
            $user->assignRole($request->roles);

            DB::commit();

            return $this->successResponse([
                'user' => $user->load('roles')
            ], 'Tạo tài khoản nhân viên thành công.', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Cập nhật thông tin User (Đa vai trò) (API-326)
     */
    public function update(UpdateUserRequest $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();

            // Nếu role là artist và có truyền stage_name
            if ($user->role === 'artist' && $request->has('stage_name')) {
                $artistProfile = $user->artistProfile;
                if ($artistProfile) {
                    $artistProfile->stage_name = $request->stage_name;
                    $artistProfile->save();
                }
            }

            DB::commit();

            return $this->successResponse([
                'user' => $user->load('artistProfile', 'roles')
            ], 'Cập nhật thông tin thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Cập nhật trạng thái người dùng (Khóa/Mở khóa - API-322)
     */
    public function updateStatus(UpdateUserStatusRequest $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->status = $request->status;
        $user->save();

        if (in_array($request->status, ['Suspended', 'Banned'])) {
            $user->tokens()->delete();
        }

        return $this->successResponse(['user' => $user], "Đã cập nhật trạng thái thành {$request->status}.");
    }

    /**
     * Phân quyền cho nhân viên (API-325)
     */
    public function updateRoles(UpdateUserRolesRequest $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->syncRoles($request->roles);

        return $this->successResponse([
            'roles' => $user->getRoleNames()
        ], 'Đã cập nhật quyền thành công.');
    }

    /**
     * Xóa Người dùng (Soft Delete) (API-328)
     */
    public function destroy($id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Chặn không cho tự xóa
        if (Auth::id() == $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể tự xóa tài khoản của chính mình.'
            ], 403);
        }

        $user->tokens()->delete(); // Thu hồi session
        $user->delete(); // Soft delete

        return $this->successResponse([], 'Đã xóa người dùng thành công.');
    }
}
