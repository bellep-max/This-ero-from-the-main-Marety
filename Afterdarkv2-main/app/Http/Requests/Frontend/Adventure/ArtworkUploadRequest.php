<?php

namespace App\Http\Requests\Frontend\Adventure;

use Illuminate\Foundation\Http\FormRequest;

class ArtworkUploadRequest extends FormRequest
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
            'id' => [
                'required',
            ],
            'type' => [
                'required',
                'string',
            ],
            'order' => [
                'integer',
            ],
            'child_order' => [
                'integer',
            ],
            'artwork' => [
                'sometimes',
                'file',
            ],
        ];
    }
}
