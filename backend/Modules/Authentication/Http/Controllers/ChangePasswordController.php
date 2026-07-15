<?php

namespace Modules\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Authentication\Http\Requests\ChangePasswordRequest;
use Modules\Authentication\Actions\ChangePasswordAction;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class ChangePasswordController extends Controller
{
    use ApiResponseTrait;

    /**
     * Update user password (API-104 & API-203)
     *
     * @param ChangePasswordRequest $request
     * @param ChangePasswordAction $action
     * @return JsonResponse
     */
    public function update(ChangePasswordRequest $request, ChangePasswordAction $action): JsonResponse
    {
        $user = $request->user();
        
        $action->execute($user, $request->validated('password'));

        return $this->successResponse(null, 'Đổi mật khẩu thành công.');
    }
}
