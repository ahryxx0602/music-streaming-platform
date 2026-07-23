<?php

namespace Modules\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Modules\Authentication\Actions\LoginAction;
use Modules\Authentication\Actions\RegisterArtistAction;
use Modules\Authentication\Actions\RegisterListenerAction;
use Modules\Authentication\Http\Requests\LoginRequest;
use Modules\Authentication\Http\Requests\RegisterArtistRequest;
use Modules\Authentication\Http\Requests\RegisterListenerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Handle login request (API-002)
     *
     * @param LoginRequest $request
     * @param LoginAction $action
     * @return JsonResponse
     */
    public function login(LoginRequest $request, LoginAction $action): JsonResponse
    {
        $user = $action->execute(
            $request->only('email', 'password'),
            $request->boolean('remember')
        );

        return $this->successResponse([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $user->avatar,
                'status' => $user->status,
            ],
        ], 'Đăng nhập thành công');
    }

    /**
     * Handle listener registration (API-003)
     *
     * @param RegisterListenerRequest $request
     * @param RegisterListenerAction $action
     * @return JsonResponse
     */
    public function registerListener(RegisterListenerRequest $request, RegisterListenerAction $action): JsonResponse
    {
        $action->execute($request->validated());

        return $this->successResponse(null, 'Đăng ký thành công. Vui lòng kiểm tra email để xác thực tài khoản.', 201);
    }

    /**
     * Handle artist registration (API-008)
     *
     * @param RegisterArtistRequest $request
     * @param RegisterArtistAction $action
     * @return JsonResponse
     */
    public function registerArtist(RegisterArtistRequest $request, RegisterArtistAction $action): JsonResponse
    {
        $action->execute($request->validated());

        return $this->successResponse(null, 'Đăng ký không gian nghệ sĩ thành công.', 201);
    }

    /**
     * Validate artist registration token (API-007)
     *
     * @param Request $request
     * @param \Modules\Authentication\Actions\ValidateArtistTokenAction $action
     * @return JsonResponse
     */
    public function validateArtistToken(Request $request, \Modules\Authentication\Actions\ValidateArtistTokenAction $action): JsonResponse
    {
        $token = $request->query('token', '');
        $data = $action->execute($token);

        return $this->successResponse($data);
    }

    /**
     * Get the authenticated User (API-050)
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Load profile if user is artist
        if ($user->role === 'artist') {
            $user->load('artistProfile');
        }

        return $this->successResponse([
            'user' => $user,
        ]);
    }

    /**
     * Log the user out (Invalidate the token) (API-051)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json([], 204);
    }
}
