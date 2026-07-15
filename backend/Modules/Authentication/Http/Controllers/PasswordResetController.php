<?php

namespace Modules\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Modules\Authentication\Actions\ForgotPasswordAction;
use Modules\Authentication\Actions\ResetPasswordAction;
use Modules\Authentication\Http\Requests\ForgotPasswordRequest;
use Modules\Authentication\Http\Requests\ResetPasswordRequest;
use App\Traits\ApiResponseTrait;

class PasswordResetController extends Controller
{
    use ApiResponseTrait;

    public function forgotPassword(ForgotPasswordRequest $request, ForgotPasswordAction $action)
    {
        $status = $action->execute($request->email);

        if ($status === Password::RESET_LINK_SENT) {
            return $this->successResponse(null, __($status));
        }

        return $this->errorResponse(__($status), 400);
    }

    public function resetPassword(ResetPasswordRequest $request, ResetPasswordAction $action)
    {
        $status = $action->execute($request->validated());

        if ($status === Password::PASSWORD_RESET) {
            return $this->successResponse(null, __($status));
        }

        return $this->errorResponse(__($status), 400);
    }
}
