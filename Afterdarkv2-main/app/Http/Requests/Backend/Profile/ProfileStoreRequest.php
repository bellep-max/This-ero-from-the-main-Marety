<?php

namespace App\Http\Requests\Backend\Profile;

use Illuminate\Foundation\Http\FormRequest;

class ProfileStoreRequest extends FormRequest
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
            'username' => [
                'required',
                'string',
                'alpha_dash',
                'regex:/^[A-Za-z0-9_]+$/',
                'min:4',
                'max:30',
                'unique:users,username,' . $this->user()->id,
            ],
            'name' => [
                'required',
                'string',
            ],
            'email' => [
                'email',
                'unique:users,email,' . $this->user()->id,
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
            'password' => [
                'string',
                'nullable',
            ],
            'banned' => [
                'sometimes',
                'required',
                'boolean',
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

    protected function passedValidation()
    {
        if ($this->input('password')) {
            $this->merge([
                'password' => bcrypt($this->input('password')),
            ]);
        }
    }
}
