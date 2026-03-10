<?php

namespace App\Http\Requests\Frontend\Podcast;

use App\Rules\AudioDurationRule;
use App\Services\AudioService;
use Illuminate\Foundation\Http\FormRequest;

class PodcastEpisodeStoreRequest extends FormRequest
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
            'file' => [
                new AudioDurationRule,
            ],
            'file.*' => $this->getRules(),
            'podcast_uuid' => [
                'required',
                'exists:podcasts,uuid',
            ],
            'title' => [
                'required',
                'string',
                'max:50',
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'season' => [
                'required',
                'numeric',
                'min:1',
            ],
            'number' => [
                'required',
                'numeric',
                'min:1',
            ],
            'is_visible' => [
                'sometimes',
                'required',
                'boolean',
            ],
            'allow_comments' => [
                'sometimes',
                'required',
                'boolean',
            ],
            'allow_download' => [
                'sometimes',
                'required',
                'boolean',
            ],
            'explicit' => [
                'sometimes',
                'required',
                'boolean',
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
