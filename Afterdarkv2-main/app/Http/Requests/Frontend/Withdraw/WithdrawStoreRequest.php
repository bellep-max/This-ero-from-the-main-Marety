<?php

namespace App\Http\Requests\Frontend\Withdraw;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawStoreRequest extends FormRequest
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
            'amount' => [
                'required',
                'integer',
            ],
        ];
    }
}
