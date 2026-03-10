<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\LoveableObjectEnum;
use App\Services\DownloadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class DownloadController extends ApiController
{
    public function __construct(
        private readonly DownloadService $downloadService,
    ) {}

    public function download(string $type, string $uuid): JsonResponse
    {
        $modelClass = LoveableObjectEnum::fromName($type);
        $track = $modelClass::where('uuid', $uuid)->firstOrFail();

        Gate::authorize('download', $track);

        return $this->success([
            'download_url' => $this->downloadService->downloadTrack($track),
        ]);
    }

    public function downloadHd(string $type, string $uuid): JsonResponse
    {
        $modelClass = LoveableObjectEnum::fromName($type);
        $track = $modelClass::where('uuid', $uuid)->firstOrFail();

        Gate::authorize('download', $track);

        return $this->success([
            'download_url' => $this->downloadService->downloadTrack($track, true),
        ]);
    }
}
