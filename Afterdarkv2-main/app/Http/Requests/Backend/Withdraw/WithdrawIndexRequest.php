<?php

namespace App\Http\Requests\Backend\Withdraw;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawIndexRequest extends FormRequest
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
            'term' => [
                'sometimes',
                'required',
                'string',
            ],
        ];
    }
}
