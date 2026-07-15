<?php

namespace Modules\Authentication\Actions;

use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ChangePasswordAction
{
    /**
     * Handle changing the user's password.
     *
     * @param User $user
     * @param string $newPassword
     * @return void
     */
    public function execute(User $user, string $newPassword): void
    {
        $user->forceFill([
            'password' => Hash::make($newPassword)
        ])->save();
    }
}
