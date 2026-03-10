<?php

namespace App\Http\Requests\Backend\Upgrade;

use Illuminate\Foundation\Http\FormRequest;

class UpgradeProcessRequest extends FormRequest
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
            'license' => [
                'required',
                'string',
                'regex:/^([a-f0-9]{8})-(([a-f0-9]{4})-){3}([a-f0-9]{12})$/i',
            ],
        ];
    }
}
