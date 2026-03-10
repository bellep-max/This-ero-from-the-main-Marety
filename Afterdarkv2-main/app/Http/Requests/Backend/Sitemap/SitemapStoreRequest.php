<?php

namespace App\Http\Requests\Backend\Sitemap;

use Illuminate\Foundation\Http\FormRequest;

class SitemapStoreRequest extends FormRequest
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
            'post_num' => [
                'required',
                'integer',
                'max:10000',
            ],
            'song_num' => [
                'required',
                'integer',
                'max:10000',
            ],
            'static_priority' => [
                'required',
                'numeric',
                'between:0.1,1.0',
            ],
            'song_priority' => [
                'required',
                'numeric',
                'between:0.1,1.0',
            ],
            'blog_priority' => [
                'required',
                'numeric',
                'between:0.1,1.0',
            ],
        ];
    }
}
