<?php

namespace App\Http\Requests\Backend\Slide;

use App\Enums\SlideModelEnum;
use App\Helpers\Helper;
use App\Models\Adventure;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Playlist;
use App\Models\Podcast;
use App\Models\Song;
use App\Models\Station;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SlideUpdateRequest extends FormRequest
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

    protected function prepareForValidation(): void
    {
        if ($this->has('object_type')) {
            $this->merge([
                'object_type' => SlideModelEnum::fromName($this->input('object_type')),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'object_type' => [
                'required',
                'string',
                Rule::in([
                    Song::class,
                    Adventure::class,
                    Album::class,
                    Artist::class,
                    Station::class,
                    Playlist::class,
                    Podcast::class,
                    User::class,
                ]),
            ],
            'object_id' => [
                'required',
                'integer',
            ],
            'title' => [
                'required',
                'string',
                'max:100',
            ],
            'title_link' => [
                'nullable',
                'string',
                'url',
                'max:150',
            ],
            'is_visible' => [
                'required',
                'boolean',
            ],
            'allow_home' => [
                'required',
                'boolean',
            ],
            'allow_discover' => [
                'required',
                'boolean',
            ],
            'allow_radio' => [
                'required',
                'boolean',
            ],
            'allow_community' => [
                'required',
                'boolean',
            ],
            'allow_trending' => [
                'required',
                'boolean',
            ],
            'artwork' => [
                'sometimes',
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:' . config('settings.image_max_file_size'),
            ],
        ];
    }

    protected function passedValidation(): void
    {
        if ($this->input('title_link')) {
            $this->merge([
                'title_link' => Helper::clearUrlForMetatags($this->input('title_link')),
            ]);
        }

        if ($this->input('radio')) {
            $this->merge([
                'radio' => implode(',', $this->input('radio')),
            ]);
        }

        $this->merge([
            'user_id' => auth()->id(),
        ]);
    }
}
