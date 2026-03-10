<?php

namespace App\Http\Requests\Frontend\User\Favorite;

use App\Enums\LoveableObjectEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserFavoriteUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'uuid' => [
                'required',
                'uuid',
            ],
            'type' => [
                'required',
                'string',
                Rule::in(LoveableObjectEnum::names()),
            ],
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'type' => LoveableObjectEnum::fromName($this->input('type')),
        ]);
    }
}
