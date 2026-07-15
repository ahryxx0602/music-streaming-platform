<?php

namespace Modules\Users\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        $userId = $this->route('id') ?? $this->route('user');

        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId),
            ]
        ];

        // Nếu là request update artist thì validate thêm stage_name
        if ($this->has('stage_name')) {
            $rules['stage_name'] = 'required|string|max:255';
        }

        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }
}
