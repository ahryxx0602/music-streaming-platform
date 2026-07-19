<?php

namespace Modules\Playlist\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminPlaylistRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'privacy' => 'required|in:Public,Private',
            'song_ids' => 'nullable|array',
            'song_ids.*' => 'integer|exists:songs,id',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
