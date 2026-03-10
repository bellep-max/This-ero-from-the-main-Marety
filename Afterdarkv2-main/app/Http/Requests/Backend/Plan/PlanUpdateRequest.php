<?php

namespace App\Http\Requests\Backend\Plan;

use App\Constants\DurationConstants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlanUpdateRequest extends FormRequest
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
            'site' => [
                'required',
                'array',
            ],
            'site.name' => [
                'required',
                'string',
                'max:100',
            ],
            'site.description' => [
                'nullable',
                'string',
                'max:255',
            ],
            'site.price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'site.percentage' => [
                'required',
                'int',
                'min:0',
                'max:100',
            ],
            'site.interval' => [
                'nullable',
                'string',
                Rule::in(DurationConstants::getNames(false)),
            ],
            'user' => [
                'required',
                'array',
            ],
            'user.name' => [
                'required',
                'string',
                'max:100',
            ],
            'user.description' => [
                'nullable',
                'string',
                'max:255',
            ],
            'user.price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'user.interval' => [
                'nullable',
                'string',
                Rule::in(DurationConstants::getNames(false)),
            ],
        ];
    }
}
