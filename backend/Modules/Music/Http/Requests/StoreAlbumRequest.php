<?php

namespace Modules\Music\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlbumRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'artist_id' => 'required|integer|exists:artist_profiles,id',
            'release_date' => 'nullable|date',
            'type' => 'nullable|in:Single,EP,Album',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|file|image|max:2048',
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
