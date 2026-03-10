<?php

namespace App\Http\Requests\Backend\Podcast;

use App\Models\Artist;
use App\Models\Country;
use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PodcastUpdateRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
            ],
            'artist_id' => [
                'sometimes',
                'required',
                Rule::exists(Artist::class, 'id'),
            ],
            'description' => [
                'nullable',
                'string',
                'max:300',
            ],
            'country_id' => [
                'sometimes',
                'required',
                'nullable',
                Rule::exists(Country::class, 'id'),
            ],
            'language_id' => [
                'sometimes',
                'required',
                'nullable',
                Rule::exists(Language::class, 'id'),
            ],
            'tags' => [
                'sometimes',
                'required',
                'array',
            ],
            'tags.*' => [
                'sometimes',
                'required',
                'array',
            ],
            'tags.*.tag' => [
                'sometimes',
                'required',
                'string',
                'alpha_num',
            ],
            //            'rss_feed_url' => [
            //                'sometimes',
            //                'nullable',
            //                'url',
            //            ],
            'artwork' => [
                'sometimes',
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:' . config('settings.image_max_file_size'),
            ],
            'is_visible' => [
                'required',
                'boolean',
            ],
            'allow_comments' => [
                'required',
                'boolean',
            ],
            'allow_download' => [
                'required',
                'boolean',
            ],
            'explicit' => [
                'required',
                'boolean',
            ],
            'categories' => [
                'sometimes',
                'required',
                'array',
            ],
            'categories.*' => [
                'sometimes',
                'required',
                'exists:podcast_categories,id',
            ],
        ];
    }
}
