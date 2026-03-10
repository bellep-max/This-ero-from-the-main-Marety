<?php

namespace App\Http\Requests\Frontend\User\UserLinktree;

use Illuminate\Foundation\Http\FormRequest;

class UserLinktreeUpdateRequest extends FormRequest
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
            'linktree_link' => [
                'required',
                'string',
                'max:100',
                'url',
            ],
        ];
    }
}
