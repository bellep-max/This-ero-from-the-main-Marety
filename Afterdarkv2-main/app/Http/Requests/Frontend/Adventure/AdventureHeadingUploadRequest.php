<?php

namespace App\Http\Requests\Frontend\Adventure;

use App\Rules\AudioDurationRule;
use App\Services\AudioService;
use Illuminate\Foundation\Http\FormRequest;

class AdventureHeadingUploadRequest extends FormRequest
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
            'is_visible' => $this->input('is_visible') === 'true',
            'allow_comments' => $this->input('allow_comments') === 'true',
        ]);

        if ($genres = $this->input('genres')) {
            $ids = array_column($genres, 'value');

            $this->merge([
                'genres' => $ids,
            ]);
        }

        //        if ($this->has('finals')) {
        //            foreach ($this->input('finals') as $index => $final) {
        //                $this->merge([
        //                    "finals.{$index}.is_visible" => $this->input('is_visible') === 'true',
        //                    "finals.{$index}.allow_comments" => $this->input('allow_comments') === 'true',
        //                    "finals.{$index}.allow_download" => $this->input('allow_download') === 'true',
        //                ]);
        //
        //                if ($genres = $final->input('genres')) {
        //                    $ids = array_column($genres, 'value');
        //
        //                    $this->merge([
        //                        "finals.{$index}.genres" => $ids,
        //                    ]);
        //                }
        //            }
        //        }
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
            'is_visible' => [
                'required',
                'boolean',
            ],
            'allow_comments' => [
                'required',
                'boolean',
            ],
            'release_at' => [
                'nullable',
                'before_or_equal:today',
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
