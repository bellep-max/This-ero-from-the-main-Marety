<?php

namespace App\Http\Requests\Backend\User;

use Illuminate\Foundation\Http\FormRequest;

class UserIndexRequest extends FormRequest
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
            'username' => [
                'sometimes',
                'required',
                'string',
            ],
            'exact_username' => [
                'sometimes',
                'required_with:username',
                'boolean',
            ],
            'email' => [
                'sometimes',
                'required',
                'email',
            ],
            'created_from' => [
                'sometimes',
                'required',
                'date',
            ],
            'created_until' => [
                'sometimes',
                'required',
                'date',
            ],
            'logged_from' => [
                'sometimes',
                'required',
                'date',
            ],
            'logged_until' => [
                'sometimes',
                'required',
                'date',
            ],
            'comment_count_from' => [
                'sometimes',
                'required',
                'integer',
            ],
            'comment_count_until' => [
                'sometimes',
                'required',
                'integer',
            ],
            'banned' => [
                'sometimes',
                'required',
                'boolean',
            ],
            'comment_disabled' => [
                'sometimes',
                'required',
                'boolean',
            ],
            'group' => [
                'sometimes',
                'required',
                'exists:role_users,id',
            ],
            'results_per_page' => [
                'sometimes',
                'required',
                'integer',
            ],
        ];
    }
}
