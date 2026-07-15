<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends VerifyEmail
{
    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable);
        }

        $frontendUrl = config('app.frontend_url');

        $id = $notifiable->getKey();
        $hash = sha1($notifiable->getEmailForVerification());

        $verifyUrl = URL::temporarySignedRoute(
            'api.verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $id,
                'hash' => $hash,
            ]
        );

        // Parse query string from standard signed route
        $query = parse_url($verifyUrl, PHP_URL_QUERY);

        // Trả về frontend URL kèm ID, Hash và các parameter của signed route (expires, signature)
        return $frontendUrl . '/verify-email/' . $id . '/' . $hash . '?' . $query;
    }
}
