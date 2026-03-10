<?php

namespace App\Http\Requests\Backend\Banner;

use Illuminate\Foundation\Http\FormRequest;

class BannerStoreRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        $this->merge([
            'approved' => $this->boolean('approved'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'banner_tag' => [
                'required',
                'string',
                'alpha_dash',
                'regex:/^[a-z0-9_]+$/',
                'min:4',
                'max:30',
            ],
            'description' => [
                'required',
                'string',
            ],
            'started_at' => [
                'nullable',
                'date',
            ],
            'ended_at' => [
                'nullable',
                'date',
                'after:now',
            ],
            'code' => [
                'required',
                'string',
            ],
            'approved' => [
                'sometimes',
                'required',
                'boolean',
            ],
        ];
    }
}
