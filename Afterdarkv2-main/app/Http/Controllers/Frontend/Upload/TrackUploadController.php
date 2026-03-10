<?php

namespace App\Http\Controllers\Frontend\Upload;

use App\Enums\TrackTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Upload\TrackUploadRequest;
use App\Services\UploadService;
use Illuminate\Http\RedirectResponse;

class TrackUploadController extends Controller
{
    private UploadService $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function __invoke(TrackUploadRequest $request): RedirectResponse
    {
        $response = $this->uploadService->handle($request, TrackTypeEnum::Song);

        return $response
            ? to_route('songs.show', $response)
            : redirect()->back();
    }
}
