<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Intervention\Image\Laravel\Facades\Image;

class PickAndChoosePlaylistCreateRequest extends FormRequest
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
     *
     * @return array
     */
    public function rules()
    {
        return [
            'label' => 'required|string',
            'desc' => 'required|string',
            'img' => 'file|mimes:jpg,bmp,png',
        ];
    }

    public function getBase64Img(): string
    {
        $img = Image::read($this->file('img'));

        return base64_encode(
            $img->coverDown(config('settings.image_artwork_max'), config('settings.image_artwork_max'))
                ->toJpeg(config('settings.image_jpeg_quality'))
        );
    }
}
