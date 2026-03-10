<?php

namespace App\Http\Requests\Backend\Country;

use Illuminate\Foundation\Http\FormRequest;

class CountryStoreRequest extends FormRequest
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
            'code' => [
                'required',
                'string',
                'max:3',
            ],
            'region_id' => [
                'sometimes',
                'required',
                'exists:regions,id',
            ],
            'local_name' => [
                'nullable',
                'string',
            ],
            'fixed' => [
                'required',
                'boolean',
            ],
            'government_form' => [
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
