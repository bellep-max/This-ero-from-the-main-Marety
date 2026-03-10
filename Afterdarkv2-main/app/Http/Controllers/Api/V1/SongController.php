<?php

namespace App\Http\Controllers\Api\V1;

use App\Constants\DefaultConstants;
use App\Http\Requests\Frontend\Song\SongUpdateRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\Song\SongEditResource;
use App\Http\Resources\Song\SongFullResource;
use App\Http\Resources\User\UserProfileResource;
use App\Models\Song;
use App\Services\NotificationService;
use App\Services\SongService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SongController extends ApiController
{
    public function __construct(
        private SongService $songService,
        private readonly NotificationService $notificationService,
    ) {}

    public function show(Song $song, Request $request): JsonResponse
    {
        Gate::authorize('show', $song);

        $song->loadMissing([
            'tags',
            'genres',
            'user',
            'slides' => function ($query) use ($song) {
                $query->when(auth()->id() !== $song->user_id, function ($query) {
                    $query->where('is_visible', DefaultConstants::TRUE);
                });
            },
        ])
            ->loadCount('fans');

        if ($song->allow_comments) {
            $song->loadMissing([
                'comments' => function ($query) {
                    $query->whereNull('parent_id')
                        ->orderBy('created_at', 'desc')
                        ->with([
                            'replies.user:id,name',
                            'user:id,name',
                        ]);
                },
            ]);
        }

        if ($request->hasValidSignature() && auth()->id() !== $song->user_id) {
            $song->increment('referral_plays');
        }

        $this->notificationService->markAsRead($song);

        return $this->success([
            'user' => UserProfileResource::make($song->user),
            'track' => SongFullResource::make($song),
            'comments' => CommentResource::collection($song->comments),
        ]);
    }

    public function edit(Song $song): JsonResponse
    {
        Gate::authorize('edit', $song);

        $song->loadMissing(
            'tags',
            'genres',
            'vocal'
        )->loadCount(
            'comments',
            'loves',
            'playlists',
        );

        return $this->success([
            'user' => UserProfileResource::make($song->user),
            'song' => SongEditResource::make($song),
        ]);
    }

    public function update(Song $song, SongUpdateRequest $request): JsonResponse
    {
        Gate::authorize('edit', $song);

        $this->songService->updateSong($song, $request);

        return $this->success(null, 'The song was successfully updated.');
    }

    public function destroy(Song $song): JsonResponse
    {
        Gate::authorize('destroy', $song);

        if ($song->delete()) {
            return $this->success(null, 'Song deleted successfully.');
        }

        return $this->error('Song could not be deleted.');
    }
}
