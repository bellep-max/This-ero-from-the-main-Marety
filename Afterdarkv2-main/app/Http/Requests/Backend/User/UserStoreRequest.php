<?php

namespace App\Http\Requests\Backend\User;

use App\Constants\RestrictionConstants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
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
            ],
            'username' => [
                'required',
                'string',
                'alpha_dash',
                'unique:users',
            ],
            'email' => [
                'nullable',
                'email',
                'unique:users,email',
            ],
            'blockEmail' => [
                'sometimes',
                'string',
            ],
            'password' => [
                'required',
                'string',
            ],
            'publishRestrict' => [
                'sometimes',
                'required',
                'array',
            ],
            'publishRestrict.*' => [
                'sometimes',
                'required',
                Rule::in(RestrictionConstants::getall(false)),
            ],
            'role' => [
                'required',
                'exists:roles,id',
            ],
            'removeArtwork' => [
                'sometimes',
                'required',
                'string',
            ],
            'deleteComments' => [
                'sometimes',
                'required',
                'string',
            ],
            'blockIps' => [
                'sometimes',
                'required',
                'string',
                'ip',
            ],
            'about' => [
                'nullable',
                'string',
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
