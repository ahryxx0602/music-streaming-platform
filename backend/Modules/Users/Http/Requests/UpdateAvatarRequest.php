<?php

namespace Modules\Users\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvatarRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'], // Max 2MB
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.required' => 'Vui lòng chọn một ảnh để tải lên.',
            'avatar.image' => 'File tải lên phải là một bức ảnh.',
            'avatar.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif, hoặc webp.',
            'avatar.max' => 'Kích thước ảnh tối đa là 2MB.',
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
