<?php

namespace App\Http\Requests\Backend\Playlist;

use App\Constants\ActionConstants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlaylistTrackMassActionRequest extends FormRequest
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
            'action' => [
                'sometimes',
                'required',
                'string',
                Rule::in(ActionConstants::getPlaylistTrackMassActions()),
            ],
        ];
    }
}
