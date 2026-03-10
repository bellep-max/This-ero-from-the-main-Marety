<?php

namespace App\Http\Requests\Frontend\User\UserProfile;

use App\Constants\GenderConstants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserProfileUpdateRequest extends FormRequest
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
                'sometimes',
                'required',
                'string',
                'max:50',
            ],
            'country' => [
                'nullable',
                'string',
                'max:3',
            ],
            'bio' => [
                'nullable',
                'string',
                'max:180',
            ],
            'gender' => [
                'nullable',
                'string',
                Rule::in(GenderConstants::getGenderCodes()),
            ],
            'birth' => [
                'nullable',
                'date_format:m/d/Y',
            ],
            'artwork' => [
                'sometimes',
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:' . config('settings.image_max_file_size'),
            ],
        ];
    }
}
