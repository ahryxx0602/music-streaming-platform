<?php

namespace Modules\Authentication\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class EmailVerificationRequest extends FormRequest
{
    protected $verifyUser;

    public function authorize(): bool
    {
        $this->verifyUser = User::find($this->route('id'));

        if (! $this->verifyUser) {
            return false;
        }

        if (! hash_equals((string) $this->route('hash'), sha1($this->verifyUser->getEmailForVerification()))) {
            return false;
        }

        return URL::hasValidSignature($this);
    }

    public function rules(): array
    {
        return [];
    }

    public function user($guard = null)
    {
        return $this->verifyUser ?? parent::user($guard);
    }
}
