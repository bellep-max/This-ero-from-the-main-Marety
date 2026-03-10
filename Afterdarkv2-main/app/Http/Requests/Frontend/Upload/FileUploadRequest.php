<?php

namespace App\Http\Requests\Frontend\Upload;

use App\Rules\AudioDurationRule;
use App\Services\AudioService;
use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
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
            'file' => [
                new AudioDurationRule,
            ],
            'file.*' => $this->getRules(),
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
