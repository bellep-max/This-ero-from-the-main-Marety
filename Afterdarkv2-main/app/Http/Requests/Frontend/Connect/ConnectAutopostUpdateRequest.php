<?php

namespace App\Http\Requests\Frontend\Connect;

use Illuminate\Foundation\Http\FormRequest;

class ConnectAutopostUpdateRequest extends FormRequest
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
            'autopost' => [
                'required',
                'boolean',
            ],
        ];
    }
}
