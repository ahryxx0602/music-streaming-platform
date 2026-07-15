<?php

namespace Modules\Authentication\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class RegisterListenerAction
{
    /**
     * Handle the listener registration.
     *
     * @param array $data
     * @return User
     */
    public function execute(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'listener',
            'status' => 'Active',
        ]);

        $user->assignRole('listener');

        event(new Registered($user));

        return $user;
    }
}
