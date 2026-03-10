<?php

namespace App\Http\Requests\Backend\Comment;

use App\Models\Activity;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Episode;
use App\Models\Playlist;
use App\Models\Podcast;
use App\Models\Post;
use App\Models\Song;
use App\Models\Station;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CommentStoreRequest extends FormRequest
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
        $this->merge([
            'content' => trim(strip_tags(preg_replace("/\s|&nbsp;/", ' ', $this->input('content')))),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'uuid' => [
                'required',
                //                'exists:comments,commentable_id',
            ],
            'commentable_type' => [
                'required',
                Rule::in([
                    Song::class,
                    Artist::class,
                    Album::class,
                    Station::class,
                    Playlist::class,
                    Post::class,
                    User::class,
                    Activity::class,
                    Podcast::class,
                    Episode::class,
                ]),
            ],
            'content' => [
                'required',
                'string',
                'min:' . config('settings.comment_min_chars', 1),
                'max:' . config('settings.comment_max_chars'),
            ],
            'parent_id' => [
                'present',
                'nullable',
                //                'required',
                //                'exists:comments,id',
            ],
        ];
    }
}
