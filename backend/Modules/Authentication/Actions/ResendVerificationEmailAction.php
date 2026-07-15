<?php

namespace Modules\Authentication\Actions;

use Illuminate\Foundation\Auth\User as Authenticatable;

class ResendVerificationEmailAction
{
    /**
     * Resend the email verification notification.
     *
     * @param Authenticatable $user
     * @return bool
     */
    public function execute(Authenticatable $user): bool
    {
        if ($user->hasVerifiedEmail()) {
            return false;
        }

        $user->sendEmailVerificationNotification();

        return true;
    }
}
