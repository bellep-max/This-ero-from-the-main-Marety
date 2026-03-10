<?php

namespace App\Http\Requests\Frontend\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginStoreRequest extends FormRequest
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
        return config('settings.authorization_method', 0) == 0
            ? [
                'username' => [
                    'required',
                    'string',
                    'alpha_dash',
                    'exists:users,username',
                ],
                'password' => [
                    'required',
                    'string',
                ],
            ]
            : [
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                ],
                'password' => [
                    'required',
                    'string',
                ],
            ];
    }
}
