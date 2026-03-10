<?php

namespace App\Http\Requests\Backend\Slide;

use Illuminate\Foundation\Http\FormRequest;

class SlideSortRequest extends FormRequest
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
            'slideshowIds' => [
                'required',
                'array',
            ],
            'slideshowIds.*' => [
                'exists:slides,id',
            ],
        ];
    }
}
