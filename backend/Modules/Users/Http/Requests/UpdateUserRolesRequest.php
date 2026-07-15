<?php

namespace Modules\Users\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRolesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'roles' => 'required|array',
            'roles.*' => 'string|exists:roles,name'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
