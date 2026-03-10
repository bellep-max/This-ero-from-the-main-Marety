<?php

namespace App\Http\Requests\Backend\Podcast;

use Illuminate\Foundation\Http\FormRequest;

class PodcastStoreRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
            ],
            'artist_id' => [
                'sometimes',
                'required',
                'exists:artists,id',
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
                'exists:countries,id',
            ],
            'language_id' => [
                'sometimes',
                'required',
                'nullable',
                'exists:languages,id',
            ],
            'rss_feed_url' => [
                'sometimes',
                'nullable',
                'url',
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

    protected function passedValidation()
    {
        $this->merge([
            'user_id' => auth()->id(),
        ]);
    }
}
