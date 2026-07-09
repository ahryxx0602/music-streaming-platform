<?php

namespace Modules\Authentication\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginAction
{
    /**
     * Handle the login attempt.
     *
     * @param array $credentials
     * @param bool $remember
     * @return \App\Models\User
     * @throws ValidationException
     */
    public function execute(array $credentials, bool $remember = false)
    {
        if (!Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Optional: Check status (e.g. if banned)
        if ($user->status !== 'Active' && $user->status !== 'active') {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Your account is suspended or banned.',
            ]);
        }

        return $user;
    }
}
