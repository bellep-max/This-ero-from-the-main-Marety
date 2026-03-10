<?php

namespace App\Http\Requests\Frontend;

use App\Enums\ActivityTypeEnum;
use App\Models\Adventure;
use App\Models\Episode;
use App\Models\Song;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActionStoreRequest extends FormRequest
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
            ],
            'action' => [
                'required',
                'string',
                Rule::in(ActivityTypeEnum::values()),
            ],
            'type' => [
                'required',
                Rule::in(
                    Adventure::class,
                    Song::class,
                    Episode::class,
                ),
            ],
        ];
    }
}
