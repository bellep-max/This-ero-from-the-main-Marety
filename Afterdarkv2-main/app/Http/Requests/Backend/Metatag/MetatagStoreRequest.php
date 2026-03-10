<?php

namespace App\Http\Requests\Backend\Metatag;

use Illuminate\Foundation\Http\FormRequest;

class MetatagStoreRequest extends FormRequest
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
            'url' => [
                'required',
                'string',
                'min:1',
                'unique:metatags,url',
            ],
            'page_title' => [
                'required',
                'string',
            ],
            'page_description' => [
                'required',
                'string',
            ],
            'info' => [
                'required',
                'string',
            ],
            'page_keywords' => [
                'sometimes',
                'required',
                'array',
            ],
            'auto_keyword' => [
                'required',
                'boolean',
            ],
            'artwork' => [
                'sometimes',
                'required',
                'file',
            ],
        ];
    }

    protected function passedValidation()
    {
        $this->merge([
            'url' => clearUrlForMetatags($this->input('url')),
            'page_keywords' => implode(',', $this->input('page_keywords') ?? []),
        ]);
    }
}
