<?php

namespace Modules\Authentication\Actions;

use Illuminate\Support\Facades\Password;

class ForgotPasswordAction
{
    /**
     * Send a password reset link to the given user.
     *
     * @param string $email
     * @return string The response from the password broker (e.g. Password::RESET_LINK_SENT)
     */
    public function execute(string $email): string
    {
        return Password::broker()->sendResetLink(['email' => $email]);
    }
}
