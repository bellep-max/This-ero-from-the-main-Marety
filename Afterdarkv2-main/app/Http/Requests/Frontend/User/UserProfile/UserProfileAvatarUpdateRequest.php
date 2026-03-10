<?php

namespace App\Http\Requests\Frontend\User\UserProfile;

use Illuminate\Foundation\Http\FormRequest;

class UserProfileAvatarUpdateRequest extends FormRequest
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
            'artwork' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:' . config('settings.image_max_file_size'),
            ],
        ];
    }
}
