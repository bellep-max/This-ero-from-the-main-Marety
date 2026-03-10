<?php

namespace App\Http\Requests\Backend\Genre;

use Illuminate\Foundation\Http\FormRequest;

class GenreStoreRequest extends FormRequest
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
                'unique:genres',
            ],
            'alt_name' => [
                'nullable',
                'string',
                'unique:genres',
            ],
            'description' => [
                'nullable',
                'string',
                'max:300',
            ],
            'meta_title' => [
                'nullable',
                'string',
                'max:200',
            ],
            'meta_description' => [
                'nullable',
                'string',
                'max:300',
            ],
            'meta_keywords' => [
                'array',
                'max:300',
            ],
            'meta_keywords.*' => [
                'string',
            ],
            'artwork' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:' . config('settings.image_max_file_size', 10240),
            ],
        ];
    }

    protected function passedValidation()
    {
        $this->merge([
            'alt_name' => $this->input('alt_name')
                ? str_slug($this->input('alt_name'))
                : str_slug($this->input('name')),
            'meta_keywords' => $this->input('meta_keywords')
                ? implode(',', $this->input('meta_keywords'))
                : [],
        ]);
    }
}
