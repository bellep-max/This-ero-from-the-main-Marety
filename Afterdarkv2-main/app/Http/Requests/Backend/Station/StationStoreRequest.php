<?php

namespace App\Http\Requests\Backend\Station;

use Illuminate\Foundation\Http\FormRequest;

class StationStoreRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
            ],
            'category' => [
                'sometimes',
                'required',
                'array',
            ],
            'category.*' => [
                'exists:categories,id',
            ],
            'artwork' => [
                'sometimes',
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:' . config('settings.image_max_file_size'),
            ],
            'description' => [
                'required',
                'nullable',
                'string',
            ],
            'country_id' => [
                'sometimes',
                'required',
                'exists:countries,id',
            ],
            'city' => [
                'sometimes',
                'required_with:country',
                'exists:city,id',
            ],
            'stream_url' => [
                'required',
                'string',
                'url',
            ],
        ];
    }

    protected function passedValidation()
    {
        if ($this->input('category')) {
            $this->merge([
                'category' => implode(',', $this->input('category')),
            ]);
        }
    }
}
