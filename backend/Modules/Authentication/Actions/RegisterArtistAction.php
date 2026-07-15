<?php

namespace Modules\Authentication\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Modules\Artist\Models\ArtistInvitation;
use Illuminate\Auth\Events\Registered;

class RegisterArtistAction
{
    /**
     * Handle the artist registration with invitation token.
     *
     * @param array $data
     * @return User
     * @throws ValidationException|\Exception
     */
    public function execute(array $data): User
    {
        $invitation = ArtistInvitation::where('token', $data['token'])
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation) {
            throw ValidationException::withMessages([
                'token' => 'The invitation token is invalid or has expired.',
            ]);
        }

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'artist',
                'status' => 'Active',
            ]);

            $user->assignRole('artist');

            $user->artistProfile()->create([
                'stage_name' => $data['stage_name'],
                // other default fields can be set here if needed
            ]);

            // Mark invitation as used
            $invitation->update(['used_at' => now()]);

            DB::commit();

            event(new Registered($user));

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
