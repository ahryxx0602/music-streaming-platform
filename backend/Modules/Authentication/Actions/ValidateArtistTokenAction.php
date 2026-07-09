<?php

namespace Modules\Authentication\Actions;

use Modules\Artist\Models\ArtistInvitation;

class ValidateArtistTokenAction
{
    /**
     * Validate an artist invitation token.
     *
     * @param string $token
     * @return array
     */
    public function execute(string $token): array
    {
        $invitation = ArtistInvitation::where('token', $token)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation) {
            return [
                'is_valid' => false,
                'email_hint' => null,
            ];
        }

        return [
            'is_valid' => true,
            'email_hint' => $this->maskEmail($invitation->email),
        ];
    }

    /**
     * Mask the email for hint (e.g. son***@mtpent.com)
     *
     * @param string|null $email
     * @return string|null
     */
    private function maskEmail(?string $email): ?string
    {
        if (!$email) {
            return null;
        }

        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return null;
        }

        $name = $parts[0];
        $domain = $parts[1];

        if (strlen($name) <= 3) {
            $maskedName = str_pad(substr($name, 0, 1), strlen($name), '*');
        } else {
            $maskedName = substr($name, 0, 3) . str_repeat('*', strlen($name) - 3);
        }

        return $maskedName . '@' . $domain;
    }
}
