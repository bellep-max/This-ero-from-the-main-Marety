<?php

namespace App\Http\Requests\Backend\Page;

use Illuminate\Foundation\Http\FormRequest;

class PageUpdateRequest extends FormRequest
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
                'unique:pages,title,' . $this->page->id,
            ],
            'alt_name' => [
                'nullable',
                'string',
                'unique:pages,alt_name,' . $this->page->id,
            ],
            'content' => [
                'required',
                'string',
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
                'nullable',
                'string',
                'max:300',
            ],
        ];
    }

    protected function passedValidation()
    {
        $this->merge([
            'alt_name' => $this->input('alt_name')
                ? str_slug($this->input('alt_name'))
                : str_slug($this->input('title')),
        ]);
    }
}
