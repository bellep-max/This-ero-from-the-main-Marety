<?php

namespace App\Http\Requests\Backend\Channel;

use Illuminate\Foundation\Http\FormRequest;

class ChannelSortRequest extends FormRequest
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
            'object_ids' => [
                'required',
                'array',
            ],
            'object_ids.*' => [
                'required',
                'exists:channels,id',
            ],
        ];
    }
}
