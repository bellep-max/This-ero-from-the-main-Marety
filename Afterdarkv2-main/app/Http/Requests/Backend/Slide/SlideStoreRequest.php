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

class SlideStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
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
            'genre' => [
                'sometimes',
                'required',
                'array',
            ],
            'genre.*' => [
                'exists:genres,id',
            ],
            //            'radio' => [
            //                'sometimes',
            //                'required',
            //                'array',
            //            ],
            //            'radio.*' => [
            //                'exists:radio,id',
            //            ],
            'podcast' => [
                'sometimes',
                'required',
                'array',
            ],
            'podcast.*' => [
                'exists:podcasts,id',
            ],
            'artwork' => [
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

        if ($this->input('podcast')) {
            $this->merge([
                'podcast' => implode(',', $this->input('podcast')),
            ]);
        }

        $this->merge([
            'user_id' => auth()->id(),
        ]);
    }
}
