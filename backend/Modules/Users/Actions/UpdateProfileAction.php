<?php

namespace Modules\Users\Actions;

use App\Models\User;
use Illuminate\Support\Arr;

class UpdateProfileAction
{
    /**
     * Handle the profile update.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function execute(User $user, array $data): User
    {
        // Update user core table
        if (isset($data['name'])) {
            $user->name = $data['name'];
            $user->save();
        }

        // Update artist profile if applicable
        if ($user->role === 'artist' && $user->artistProfile) {
            $artistFields = Arr::only($data, [
                'stage_name', 'bio', 'facebook', 'instagram', 'youtube', 'website'
            ]);
            
            if (!empty($artistFields)) {
                $user->artistProfile()->update($artistFields);
            }
        }

        return $user;
    }
}
