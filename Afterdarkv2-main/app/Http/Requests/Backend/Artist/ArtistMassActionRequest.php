<?php

namespace App\Http\Requests\Backend\Artist;

use App\Constants\ActionConstants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArtistMassActionRequest extends FormRequest
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
                'exists:artists,id',
            ],
            'action' => [
                'required',
                'string',
                Rule::in(ActionConstants::getMassActions()),
            ],
        ];
    }
}
