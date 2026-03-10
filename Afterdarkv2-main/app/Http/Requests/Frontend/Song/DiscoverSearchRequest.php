<?php

namespace App\Http\Requests\Frontend\Song;

use App\Models\Adventure;
use App\Models\Song;
use App\Models\Tag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DiscoverSearchRequest extends FormRequest
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
        if ($this->has('released_at') && !$this->input('released_at')) {
            $this->offsetUnset('released_at');
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'genres' => [
                'sometimes',
                'required',
                'array',
            ],
            'genres.*' => [
                'sometimes',
                'required',
                'exists:genres,id',
            ],
            'vocals' => [
                'sometimes',
                'required',
                'array',
            ],
            'vocals.*' => [
                'sometimes',
                'required',
                'exists:vocals,id',
            ],
            'tags' => [
                'sometimes',
                'required',
                'array',
            ],
            'tags.*' => [
                'sometimes',
                'required',
                'exists:tags,tag',
            ],
            'duration' => [
                'sometimes',
                'required',
                'array',
            ],
            'duration.*' => [
                'sometimes',
                'required',
                'integer',
            ],
            'released_at' => [
                'sometimes',
                'required',
                'boolean',
            ],
            'type' => [
                'sometimes',
                'required',
                Rule::in(['song', 'adventure']),
            ],
        ];
    }

    protected function passedValidation(): void
    {
        if ($this->filled('duration')) {
            $this->merge([
                'duration' => array_map(fn ($e) => $e * 60, $this->input('duration')),
            ]);
        }

        if ($this->filled('type')) {
            $this->merge([
                'type' => $this->input('type') === 'song'
                    ? Song::class
                    : Adventure::class,
            ]);
        }

        if ($this->filled('tags.*')) {
            $this->merge([
                'tags' => Tag::query()->whereIn('tag', $this->input('tags'))->pluck('id')->toArray(),
            ]);
        }
    }
}
