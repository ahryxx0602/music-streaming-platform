<?php

namespace Modules\Authentication\Actions;

use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\User as Authenticatable;

class VerifyEmailAction
{
    /**
     * Mark the given user's email as verified.
     *
     * @param Authenticatable $user
     * @return bool
     */
    public function execute(Authenticatable $user): bool
    {
        if ($user->hasVerifiedEmail()) {
            return false;
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            return true;
        }

        return false;
    }
}
