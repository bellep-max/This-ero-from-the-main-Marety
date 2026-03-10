<?php

namespace App\Http\Requests\Frontend\Reaction;

use Illuminate\Foundation\Http\FormRequest;

class ReactionUpdateRequest extends FormRequest
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
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reaction_able_id' => [
                'required',
                'integer',
                'exists:reactions,reactionable_id',
            ],
            'reaction_able_type' => [
                'required',
                'in:App\Models\Activity,App\Models\Comment',
            ],
            'reaction_type' => [
                'required',
                'string',
                'in:' . config('settings.reactions', 'like,love,haha,vow,sad,angry'),
            ],
        ];
    }
}
