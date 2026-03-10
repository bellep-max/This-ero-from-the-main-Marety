<?php

namespace App\Http\Requests\Frontend\Event;

use Illuminate\Foundation\Http\FormRequest;

class EventEditRequest extends FormRequest
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
                'integer',
                'exists:events,id',
            ],
            'title' => [
                'required',
                'string',
                'max:50',
            ],
            'location' => [
                'required',
                'string',
                'max:100',
            ],
            'link' => [
                'nullable',
                'string',
                'max:100',
            ],
            'started_at' => [
                'nullable',
                'date_format:m/d/Y',
            ],
        ];
    }
}
