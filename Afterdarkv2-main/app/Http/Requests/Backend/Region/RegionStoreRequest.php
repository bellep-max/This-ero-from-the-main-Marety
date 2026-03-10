<?php

namespace App\Http\Requests\Backend\Region;

use Illuminate\Foundation\Http\FormRequest;

class RegionStoreRequest extends FormRequest
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
            'alt_name' => [
                'sometimes',
                'required',
                'string',
                'unique:categories',
            ],
            'is_visible' => [
                'required',
                'boolean',
            ],
        ];
    }

    protected function passedValidation()
    {
        $this->merge([
            'alt_name' => $this->input('alt_name')
                ? str_slug($this->input('alt_name'))
                : str_slug($this->input('name')),
        ]);
    }
}
