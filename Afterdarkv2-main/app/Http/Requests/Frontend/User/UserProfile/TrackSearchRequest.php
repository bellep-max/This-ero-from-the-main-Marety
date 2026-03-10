<?php

namespace App\Http\Requests\Frontend\User\UserProfile;

use Illuminate\Foundation\Http\FormRequest;

class TrackSearchRequest extends FormRequest
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
            'search' => [
                'sometimes',
                'required',
                'nullable',
                'string',
                'max:50',
            ],
        ];
    }
}
