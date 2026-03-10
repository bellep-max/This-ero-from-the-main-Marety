<?php

namespace App\Http\Requests\Backend\Metatag;

use Illuminate\Foundation\Http\FormRequest;

class MetatagSortRequest extends FormRequest
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
            'metaIds' => [
                'required',
                'array',
            ],
            'metaIds.*' => [
                'required',
                'exists:metatags,id',
            ],
        ];
    }
}
