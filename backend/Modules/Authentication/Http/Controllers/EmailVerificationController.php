<?php

namespace Modules\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Authentication\Actions\VerifyEmailAction;
use Modules\Authentication\Actions\ResendVerificationEmailAction;
use Modules\Authentication\Http\Requests\EmailVerificationRequest;
use App\Traits\ApiResponseTrait;

class EmailVerificationController extends Controller
{
    use ApiResponseTrait;

    public function verify(EmailVerificationRequest $request, VerifyEmailAction $action)
    {
        $user = $request->user();

        if ($action->execute($user)) {
            return $this->successResponse(null, 'Xác minh email thành công.');
        }

        return $this->successResponse(null, 'Email đã được xác minh từ trước.');
    }

    public function resend(Request $request, ResendVerificationEmailAction $action)
    {
        $user = $request->user();
        
        if (!$user) {
            $request->validate(['email' => 'required|email|exists:users,email']);
            $user = \App\Models\User::where('email', $request->email)->first();
        }

        if ($action->execute($user)) {
            return $this->successResponse(null, 'Đã gửi lại email xác minh.');
        }

        return $this->errorResponse('Email đã được xác minh từ trước.', 400);
    }
}
