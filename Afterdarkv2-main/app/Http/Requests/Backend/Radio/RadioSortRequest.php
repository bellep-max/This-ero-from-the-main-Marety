<?php

namespace App\Http\Requests\Backend\Radio;

use Illuminate\Foundation\Http\FormRequest;

class RadioSortRequest extends FormRequest
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
            'radioIds' => [
                'required',
                'array',
            ],
            'radioIds.*' => [
                'required',
                'exists:radio,id',
            ],
        ];
    }
}
