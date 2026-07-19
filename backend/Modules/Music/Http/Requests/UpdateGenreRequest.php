<?php

namespace Modules\Music\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGenreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $genreId = $this->route('genre') ?: $this->route('id');
        
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:genres,id', new \Modules\Music\Rules\NotDescendantOrSelf($genreId)],
            'is_active' => ['sometimes', 'boolean'],
            'cover_image' => ['nullable', 'string', 'max:255'],
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
