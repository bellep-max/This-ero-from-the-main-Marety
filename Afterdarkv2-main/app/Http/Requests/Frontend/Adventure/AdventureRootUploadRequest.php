<?php

namespace App\Http\Requests\Frontend\Adventure;

use App\Rules\AudioDurationRule;
use App\Services\AudioService;
use Illuminate\Foundation\Http\FormRequest;

class AdventureRootUploadRequest extends FormRequest
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
            'parent_uuid' => [
                'required',
                'exists:adventures,uuid',
            ],
            'order' => [
                'integer',
                'between:1,5',
            ],
            'file' => [
                new AudioDurationRule,
            ],
            'file.*' => $this->getFileRules(),
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
            'artwork' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:' . config('settings.image_max_file_size'),
            ],
            'finals' => [
                'required',
                'array',
            ],
            'finals.*.order' => [
                'integer',
                'between:1,3',
            ],
            'finals.*.file' => [
                new AudioDurationRule,
            ],
            'finals.*.file.*' => $this->getFileRules(),
            'finals.*.title' => [
                'required',
                'string',
                'max:60',
            ],
            'finals.*.description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'finals.*.artwork' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:' . config('settings.image_max_file_size'),
            ],
        ];
    }

    private function getFileRules(): array
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
