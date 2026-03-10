<?php

namespace App\Http\Requests\Frontend\Upload;

use App\Rules\AudioDurationRule;
use App\Services\AudioService;
use Illuminate\Foundation\Http\FormRequest;

class TrackUploadRequest extends FormRequest
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
        if ($genres = $this->input('genres')) {
            $ids = array_column($genres, 'value');

            $this->merge([
                'genres' => $ids,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'file' => [
                new AudioDurationRule,
            ],
            'file.*' => $this->getRules(),
            'title' => [
                'required',
                'string',
                'max:60',
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'script' => [
                'nullable',
                'string',
                'max:500',
            ],
            'is_visible' => [
                'required',
                'boolean',
            ],
            'is_explicit' => [
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
            'notify' => [
                'required',
                'boolean',
            ],
            'is_patron' => [
                'sometimes',
                'required',
                'boolean',
            ],
            'release_at' => [
                'nullable',
                'before_or_equal:today',
            ],
            'published_at' => [
                'required',
                'after_or_equal:today',
            ],
            'genres' => [
                'required',
                'array',
            ],
            'genres.*' => [
                'sometimes',
                'required',
                'exists:genres,id',
            ],
            'vocal_id' => [
                'sometimes',
                'required',
                'exists:vocals,id',
            ],
            'tags' => [
                'sometimes',
                'required',
                'array',
            ],
            'artwork' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:' . config('settings.image_max_file_size'),
            ],
        ];
    }

    private function getRules(): array
    {
        if (config('settings.ffmpeg') && AudioService::checkIfFFMPEGInstalled()) {
            return config('settings.max_audio_file_size') == 0
                ? [
                    'required',
                    'mimetypes:application/octet-stream,audio/ogg,audio/x-wav,audio/wav,audio/mpeg,audio/flac,audio/x-hx-aac-adts,audio/x-m4a,video/mp4,video/x-ms-wma,audio/ac3,audio/aac',
                ] : [
                    'required',
                    'mimetypes:application/octet-stream,audio/ogg,audio/x-wav,audio/wav,audio/mpeg,audio/flac,audio/x-hx-aac-adts,audio/x-m4a,video/mp4,video/x-ms-wma,audio/ac3,audio/aac',
                    'max:' . config('settings.max_audio_file_size', 51200),
                ];
        } else {
            return config('settings.max_audio_file_size') == 0
                ? [
                    'required',
                    'mimetypes:audio/mpeg,application/octet-stream',
                ] : [
                    'required',
                    'mimetypes:audio/mpeg,application/octet-stream',
                    'max:' . config('settings.max_audio_file_size', 10240),
                ];
        }
    }
}
