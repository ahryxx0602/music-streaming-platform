<?php

namespace Modules\Authentication\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterArtistRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'token' => 'required|string|exists:artist_invitations,token',
            'name' => 'required|string|max:255',
            'stage_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'string', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ];
    }
}
