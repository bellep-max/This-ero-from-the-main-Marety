<?php

namespace App\Http\Requests\Backend\User;

use App\Constants\RestrictionConstants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        if (!$this->input('banned')) {
            $this->offsetUnset('reason');
            $this->offsetUnset('end_at');
        }
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
                'unique:users,username,' . $this->user->id,
            ],
            'email' => [
                'nullable',
                'email',
                'unique:users,email,' . $this->user->id,
            ],
            'blockEmail' => [
                'sometimes',
                'string',
            ],
            'password' => [
                'nullable',
                'string',
            ],
            'banned' => [
                'required',
                'boolean',
            ],
            'reason' => [
                Rule::requiredIf(function () {
                    return $this->input('banned');
                }),
                'string',
            ],
            'end_at' => [
                Rule::requiredIf(function () {
                    return $this->input('banned');
                }),
                'date',
                'after:now',
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
                'exists:groups,id',
            ],
            'removeArtwork' => [
                'sometimes',
                'required',
                'string',
            ],
            'artwork' => [
                'sometimes',
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:' . config('settings.image_max_file_size'),
            ],
            'deleteComments' => [
                'sometimes',
                'required',
                'string',
            ],
            'about' => [
                'nullable',
                'string',
            ],
        ];
    }

    protected function passedValidation()
    {
        if ($this->input('password')) {
            $this->merge(['password' => bcrypt($this->input('password'))]);
        }
    }
}
