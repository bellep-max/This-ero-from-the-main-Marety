<?php

namespace App\Http\Requests\Backend\User;

use App\Constants\UserActionConstants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserMassActionRequest extends FormRequest
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
            'ids' => [
                'required',
                'array',
            ],
            'ids.*' => [
                'required',
                'exists:users,id',
            ],
            'action' => [
                'required',
                'string',
                Rule::in(UserActionConstants::getMassActions()),
            ],
        ];
    }
}
