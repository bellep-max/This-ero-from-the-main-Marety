<?php

namespace App\Http\Requests\Backend\Song;

use Illuminate\Foundation\Http\FormRequest;

class SongUpdateRequest extends FormRequest
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
                'max:100',
            ],
            'artistIds' => [
                'sometimes',
                'required',
                'array',
            ],
            'artistIds.*' => [
                'exists:artists,id',
            ],
            'albumIds' => [
                'sometimes',
                'nullable',
                'required',
                'array',
            ],
            'albumIds.*' => [
                'sometimes',
                'exists:albums,id',
            ],
            'genre' => [
                'sometimes',
                'required',
                'array',
            ],
            'genre.*' => [
                'exists:genres,id',
            ],
            'released_at' => [
                'sometimes',
                'nullable',
                'required',
                'date',
            ],
            'copyright' => [
                'sometimes',
                'required',
                'nullable',
                'string',
            ],
            'youtube_id' => [
                'sometimes',
                'required',
            ],
            'allow_comments' => [
                'sometimes',
                'required',
                'accepted',
            ],
            'approved' => [
                'sometimes',
                'required',
                'accepted',
            ],
            'group_extra' => [
                'sometimes',
                'required',
                'array',
            ],
            'group_extra.*' => [
                'sometimes',
                'required',
            ],
            'artwork' => [
                'sometimes',
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:' . config('settings.image_max_file_size'),
            ],
            //            'file_id' => [],
        ];
    }

    protected function passedValidation(): void
    {
        if ($this->input('group_extra')) {
            $groupRegel = [];

            foreach ($this->input('group_extra') as $key => $value) {
                if ($value) {
                    $groupRegel[] = intval($key) . ':' . intval($value);
                }
            }

            $this->merge([
                'access' => count($groupRegel)
                    ? implode('||', $groupRegel)
                    : null,
            ]);
        }
    }
}
