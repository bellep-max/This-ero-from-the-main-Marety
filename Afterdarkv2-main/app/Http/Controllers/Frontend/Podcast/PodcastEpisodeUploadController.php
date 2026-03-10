<?php

namespace App\Http\Controllers\Frontend\Podcast;

use App\Enums\TrackTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Podcast\PodcastEpisodeStoreRequest;
use App\Services\UploadService;
use Illuminate\Http\RedirectResponse;

class PodcastEpisodeUploadController extends Controller
{
    public function __construct(private readonly UploadService $uploadService) {}

    public function __invoke(PodcastEpisodeStoreRequest $request): RedirectResponse
    {
        $response = $this->uploadService->handle($request, TrackTypeEnum::Podcast);

        return $response
            ? to_route('podcasts.show', $response->podcast)
            : redirect()->back();
    }
}
