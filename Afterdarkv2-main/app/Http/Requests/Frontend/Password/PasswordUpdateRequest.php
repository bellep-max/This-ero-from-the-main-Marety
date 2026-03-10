<?php

namespace App\Http\Requests\Frontend\Password;

use Illuminate\Foundation\Http\FormRequest;

class PasswordUpdateRequest extends FormRequest
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
            'password' => [
                'required',
                'string',
                'min:6',
                'max:32',
                'confirmed',
            ],
            'token' => [
                'required',
                'string',
                'exists:password_resets,token',
            ],
        ];
    }
}
