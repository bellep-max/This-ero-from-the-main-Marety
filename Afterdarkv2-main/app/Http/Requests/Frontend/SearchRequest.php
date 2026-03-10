<?php

namespace App\Http\Requests\Frontend;

use Elegant\Sanitizer\Laravel\SanitizesInput;
use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    use SanitizesInput;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'search' => [
                'required',
                'string',
                'max:50',
            ],
        ];
    }

    public function filters(): array
    {
        return [
            'search' => 'trim|strip_tags',
        ];
    }
}
