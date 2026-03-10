<?php

namespace App\Http\Requests\Backend\Song;

use App\Constants\ActionConstants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SongMassActionRequest extends FormRequest
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
                'exists:songs,id',
            ],
            'albumIds' => [
                'sometimes',
                'required',
                'array',
            ],
            'albumIds.*' => [
                'sometimes',
                'required',
                'exists:albums,id',
            ],
            'artistIds' => [
                'sometimes',
                'required',
                'array',
            ],
            'artistIds.*' => [
                'sometimes',
                'required',
                'exists:artists,id',
            ],
            'action' => [
                'required',
                'string',
                Rule::in(ActionConstants::getSongMassActions()),
            ],
        ];
    }
}
