<?php

namespace App\Http\Requests\Frontend\Auth;

use App\Enums\GroupEnum;
use App\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserSignupRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'min:3',
                'max:30',
            ],
            'username' => [
                'required',
                'string',
                'alpha_dash',
                'min:4',
                'max:50',
                'unique:users,username',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
            ],
            'isArtist' => [
                'sometimes',
                'required',
                'in:on,off',
            ],
            'over_18' => [
                'required',
                'accepted',
            ],
            'role' => [
                'required',
                'string',
                Rule::in(RoleEnum::Creator->value, RoleEnum::Listener->value),
            ],
            'remember_me' => [
                'sometimes',
                'required',
            ],
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'group' => $this->input('role') == RoleEnum::Creator->value
                ? GroupEnum::Creator->name
                : GroupEnum::Member->name,
        ]);
    }
}
