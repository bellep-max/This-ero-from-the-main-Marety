<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\LoveableObjectEnum;
use App\Http\Controllers\Controller;
use App\Services\DownloadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class DownloadController extends Controller
{
    public function __construct(
        private readonly DownloadService $downloadService,
    ) {}

    public function download(string $type, string $uuid): JsonResponse
    {
        $modelClass = LoveableObjectEnum::fromName($type);
        $track = $modelClass::where('uuid', $uuid)->firstOrFail();

        Gate::authorize('download', $track);

        return response()
            ->json([
                'download_url' => $this->downloadService->downloadTrack($track),
            ]);
    }

    public function downloadHd(string $type, string $uuid): JsonResponse
    {
        $modelClass = LoveableObjectEnum::fromName($type);
        $track = $modelClass::where('uuid', $uuid)->firstOrFail();

        Gate::authorize('download', $track);

        return response()
            ->json([
                'download_url' => $this->downloadService->downloadTrack($track, true),
            ]);
    }
}
