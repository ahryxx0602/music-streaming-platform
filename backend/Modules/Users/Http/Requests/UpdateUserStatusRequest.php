<?php

namespace Modules\Users\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => 'required|in:Active,Suspended,Banned'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
