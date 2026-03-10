<?php

namespace App\Http\Requests\Settings;

use App\Enums\UserGenderEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
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
            'country_id' => [
                'sometimes',
                'required',
                'exists:countries,id',
            ],
            'gender' => [
                'sometimes',
                'required',
                Rule::in(UserGenderEnum::values()),
            ],
            'bio' => [
                'sometimes',
                'required',
                'string',
                'max:180',
            ],
            'birth' => [
                'sometimes',
                'required',
                'date',
                'before:today',
            ],
        ];
    }
}
