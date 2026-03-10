<?php

namespace App\Http\Requests\Frontend\Feedback;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackStoreRequest extends FormRequest
{
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
            'email' => [
                'required',
                'string',
                'max:50',
            ],
            'feeling' => [
                'required',
                'string',
                'max:50',
            ],
            'about' => [
                'required',
                'string',
                'max:50',
            ],
            'comment' => [
                'required',
                'string',
                'max:1000',
            ],
        ];
    }
}
