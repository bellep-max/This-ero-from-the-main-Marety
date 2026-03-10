<?php

namespace App\Http\Requests\Backend\CountryLanguage;

use Illuminate\Foundation\Http\FormRequest;

class CountryLanguageStoreRequest extends FormRequest
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
            'country_id' => [
                'required',
                'exists:countries,id',
            ],
            'is_official' => [
                'required',
                'boolean',
            ],
            'fixed' => [
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
}
