<?php

namespace App\Http\Requests\Backend\Radio;

use Illuminate\Foundation\Http\FormRequest;

class RadioUpdateRequest extends FormRequest
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
                'unique:radio,name,' . $this->radio->id,
            ],
            'alt_name' => [
                'nullable',
                'string',
                'unique:radio,alt_name,' . $this->radio->id,
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
            ],
            'meta_keywords.*' => [
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

    protected function passedValidation()
    {
        $this->merge([
            'meta_keywords' => implode(',', $this->input('meta_keywords') ?? []),
            'alt_name' => $this->input('alt_name')
                ? str_slug($this->input('alt_name'))
                : str_slug($this->input('name')),
        ]);
    }
}
