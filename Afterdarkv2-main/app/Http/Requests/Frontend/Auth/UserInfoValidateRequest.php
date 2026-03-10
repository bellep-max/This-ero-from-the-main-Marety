<?php

namespace App\Http\Requests\Frontend\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UserInfoValidateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
                'unique:users',
            ],
            'name' => [
                'required',
                'string',
                'min:3',
                'max:30',
            ],
            'password' => [
                'required',
                'confirmed',
                'min:6',
            ],
            'over_18' => [
                'required',
                'in:on',
            ],
        ];
    }
}
