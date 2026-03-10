<?php

namespace App\Http\Requests\Backend\Category;

use App\Constants\DefaultConstants;
use App\Constants\NewsSortConstants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryStoreRequest extends FormRequest
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
                'unique:categories',
            ],
            'alt_name' => [
                'nullable',
                'string',
                'unique:categories',
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
            'news_sort' => [
                'sometimes',
                'required',
                Rule::in(NewsSortConstants::getCodesList()),
            ],
            'how_sub' => [
                '2',
            ],
            'disable_main' => [
                'sometimes',
                'required',
                Rule::in(DefaultConstants::getCodesList()),
            ],
            'disable_comments' => [
                'sometimes',
                'required',
                Rule::in(DefaultConstants::getCodesList()),
            ],
            'disable_search' => [
                'sometimes',
                'required',
                Rule::in(DefaultConstants::getCodesList()),
            ],
        ];
    }

    protected function passedValidation()
    {
        $this->merge([
            'alt_name' => $this->input('alt_name') ? str_slug($this->input('alt_name')) : str_slug($this->input('name')),
        ]);

        if ($this->input('meta_keywords')) {
            $this->merge([
                'meta_keywords' => implode(',', $this->input('meta_keywords')),
            ]);
        }
    }
}
