<?php

namespace App\Http\Requests\Backend\Service;

use Illuminate\Foundation\Http\FormRequest;

class ServiceUpdateRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
                'max:100',
            ],
            'description' => [
                'required',
                'string',
                'max:300',
            ],
            'price' => [
                'required',
                'string',
            ],
            'active' => [
                'required',
                'boolean',
            ],
            'trial' => [
                'required',
                'boolean',
            ],
            'trial_period' => [
                'required_if:trial,1',
                'nullable',
                'integer',
            ],
            'trial_period_format' => [
                'required_if:trial,1',
                'nullable',
                'string',
                'in:D,W,M,Y',
            ],
            'plan_period' => [
                'required',
                'integer',
            ],
            'plan_period_format' => [
                'required',
                'string',
                'in:D,W,M,Y',
            ],
            'role_id' => [
                'required',
                'numeric',
            ],
        ];
    }
}
