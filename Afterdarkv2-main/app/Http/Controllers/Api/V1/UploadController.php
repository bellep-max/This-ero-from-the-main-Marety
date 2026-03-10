<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\AdventureSongTypeEnum;
use App\Enums\TrackTypeEnum;
use App\Http\Requests\Frontend\Adventure\AdventureHeadingUploadRequest;
use App\Http\Requests\Frontend\Adventure\AdventureRootUploadRequest;
use App\Http\Requests\Frontend\Podcast\PodcastEpisodeStoreRequest;
use App\Http\Requests\Frontend\Upload\TrackUploadRequest;
use App\Http\Resources\Adventure\AdventureTypeResource;
use App\Http\Resources\Genre\GenreShortResource;
use App\Models\Adventure;
use App\Models\Genre;
use App\Models\MEPlan;
use App\Services\AdventureService;
use App\Services\UploadService;
use Illuminate\Http\JsonResponse;

class UploadController extends ApiController
{
    public function __construct(
        private readonly UploadService $uploadService,
        private readonly AdventureService $adventureService,
    ) {}

    public function index(): JsonResponse
    {
        return $this->success([
            'genres' => GenreShortResource::collection(Genre::query()->discover()->get()),
            'plan' => MEPlan::query()->firstWhere('type', 'site'),
        ]);
    }

    public function tracks(TrackUploadRequest $request): JsonResponse
    {
        $response = $this->uploadService->handle($request, TrackTypeEnum::Song);

        if (!$response) {
            return $this->error('Failed to upload track.');
        }

        return $this->success(['uuid' => $response->uuid], 'Track uploaded successfully', 201);
    }

    public function adventureHeading(AdventureHeadingUploadRequest $request): JsonResponse
    {
        $result = $this->adventureService->handle(
            [
                ...$request->all(),
                'type' => AdventureSongTypeEnum::Heading,
            ],
            $request->file('file'),
            $request->hasFile('artwork') ? $request->file('artwork') : null
        );

        if (class_basename($result) === class_basename(Adventure::class)) {
            return $this->success(AdventureTypeResource::make($result), 'Adventure heading uploaded successfully', 201);
        }

        return $this->success($result, 'Adventure heading uploaded successfully', 201);
    }

    public function adventureRoot(AdventureRootUploadRequest $request): JsonResponse
    {
        $result = $this->adventureService->handle(
            [
                ...$request->all(),
                'type' => AdventureSongTypeEnum::Root,
            ],
            $request->file('file'),
            $request->hasFile('artwork') ? $request->file('artwork') : null
        );

        foreach ($request->input('finals') as $index => $final) {
            $this->adventureService->handle(
                [
                    ...$final,
                    'parent_id' => $result->id,
                    'type' => AdventureSongTypeEnum::Final,
                ],
                $request->file("finals.$index.file"),
                $request->hasFile('artwork') ? $request->file("finals.$index.artwork") : null,
            );
        }

        if (class_basename($result) === class_basename(Adventure::class)) {
            return $this->success(AdventureTypeResource::make($result), 'Adventure root uploaded successfully', 201);
        }

        return $this->success($result, 'Adventure root uploaded successfully', 201);
    }

    public function destroyAdventure(Adventure $adventure): JsonResponse
    {
        $adventure->media()->delete();
        $adventure->delete();

        return $this->success(null, 'Adventure deleted successfully');
    }

    public function episodes(PodcastEpisodeStoreRequest $request): JsonResponse
    {
        $response = $this->uploadService->handle($request, TrackTypeEnum::Podcast);

        if (!$response) {
            return $this->error('Failed to upload episode.');
        }

        return $this->success(['uuid' => $response->uuid], 'Episode uploaded successfully', 201);
    }
}
