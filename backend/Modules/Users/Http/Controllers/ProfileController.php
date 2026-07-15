<?php

namespace Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Users\Http\Requests\UpdateProfileRequest;
use Modules\Users\Http\Requests\UpdateAvatarRequest;
use Modules\Users\Actions\UpdateProfileAction;
use Modules\Users\Actions\UploadAvatarAction;
use App\Traits\ApiResponseTrait;

class ProfileController extends Controller
{
    use ApiResponseTrait;

    /**
     * Update user profile info (API-107 & API-204)
     *
     * @param UpdateProfileRequest $request
     * @param UpdateProfileAction $action
     * @return JsonResponse
     */
    public function update(UpdateProfileRequest $request, UpdateProfileAction $action): JsonResponse
    {
        $user = $request->user();
        
        $updatedUser = $action->execute($user, $request->validated());

        // Nếu là artist, eager load profile để trả về đầy đủ
        if ($updatedUser->role === 'artist') {
            $updatedUser->load('artistProfile');
        }

        return $this->successResponse([
            'user' => $updatedUser
        ], 'Cập nhật thông tin thành công.');
    }

    /**
     * Upload user avatar (API-109 & API-205)
     *
     * @param UpdateAvatarRequest $request
     * @param UploadAvatarAction $action
     * @return JsonResponse
     */
    public function updateAvatar(UpdateAvatarRequest $request, UploadAvatarAction $action): JsonResponse
    {
        $user = $request->user();
        
        $updatedUser = $action->execute($user, $request->file('avatar'));

        if ($updatedUser->role === 'artist') {
            $updatedUser->load('artistProfile');
        }

        return $this->successResponse([
            'user' => $updatedUser
        ], 'Cập nhật ảnh đại diện thành công.');
    }
}
