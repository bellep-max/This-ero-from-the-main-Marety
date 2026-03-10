<?php

namespace App\Http\Requests\Frontend\Report;

use App\Enums\LoveableObjectEnum;
use Illuminate\Foundation\Http\FormRequest;

class ReportStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'type' => LoveableObjectEnum::fromName($this->input('type')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'report_type' => [
                'required',
                'integer',
                'in:1,2,3',
            ],
            'type' => [
                'required',
                'string',
            ],
            'uuid' => [
                'required',
                'uuid',
            ],
            'message' => [
                'sometimes',
                'required',
                'string',
                'max:190',
            ],
        ];
    }
}
