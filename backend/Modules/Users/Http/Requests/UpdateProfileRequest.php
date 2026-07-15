<?php

namespace Modules\Users\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
        ];

        // Nếu user là artist, cho phép validate thêm các trường profile
        if ($this->user() && $this->user()->role === 'artist') {
            $rules = array_merge($rules, [
                'stage_name' => ['sometimes', 'required', 'string', 'max:100'],
                'bio' => ['nullable', 'string', 'max:1000'],
                'facebook' => ['nullable', 'url', 'max:255'],
                'instagram' => ['nullable', 'url', 'max:255'],
                'youtube' => ['nullable', 'url', 'max:255'],
                'website' => ['nullable', 'url', 'max:255'],
            ]);
        }

        return $rules;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
