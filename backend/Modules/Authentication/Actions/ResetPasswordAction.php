<?php

namespace Modules\Authentication\Actions;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class ResetPasswordAction
{
    /**
     * Reset the user's password.
     *
     * @param array $data
     * @return string The response from the password broker (e.g. Password::PASSWORD_RESET)
     */
    public function execute(array $data): string
    {
        return Password::broker()->reset(
            $data,
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );
    }
}
