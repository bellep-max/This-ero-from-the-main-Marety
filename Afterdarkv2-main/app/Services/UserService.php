<?php

namespace App\Services;

use App\Constants\ActionConstants;
use App\Constants\UploadConstants;
use App\Enums\RoleEnum;
use App\Models\Activity;
use App\Models\Artist;
use App\Models\Playlist;
use App\Models\Song;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function delete(int $id)
    {
        return User::query()->whereId($id)->delete();
    }

    public static function getUserRecentSongs(User $user, int $take = 10): Collection
    {
        return Song::query()
            ->whereHas('history', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['user:id,uuid,name,username', 'tags', 'genres'])
            ->latest()
            ->limit($take)
            ->get();
    }

    public static function getUserRecentActivities(User $user, int $take = 20): Collection
    {
        return Activity::query()
            ->leftJoin('love', 'activities.activityable_id', '=', 'love.loveable_id')
            ->select(['activities.*', 'love.user_id as host_id'])
            ->where('love.user_id', $user->id)
            ->where(function ($query) {
                $query->where('love.loveable_type', Playlist::class)
                    ->whereNotIn('activities.action', [
                        ActionConstants::FOLLOW_PLAYLIST,
                        ActionConstants::FOLLOW_USER,
                        ActionConstants::PLAY_SONG,
                        ActionConstants::POST_FEED,
                    ]);
            })->orWhere(function ($query) {
                $query->where('love.loveable_type', Artist::class)
                    ->whereNotIn('activities.action', [
                        ActionConstants::FOLLOW_ARTIST,
                        ActionConstants::POST_FEED,
                    ])
                    ->where(function ($query) {
                        $query->whereIn('action', [
                            ActionConstants::ADD_SONG,
                            ActionConstants::ADD_EVENT,
                        ]);
                    });
            })
            ->latest()
            ->take($take)
            ->get();
    }

    public static function canUpload(User $user): bool
    {
        if ($user->hasRole(RoleEnum::Listener)) {
            return false;
        } elseif ($user->activeSubscription()) {
            return true;
        }

        $songsLimit = UploadConstants::DEFAULT_UPLOAD_NUMBER;
        $weekTracksCount = $user->tracks()->withoutGlobalScopes()->whereBetween('created_at', [now()->subWeek(), now()])->count();
        $weekPodcastsCount = $user->podcasts()->withoutGlobalScopes()->whereBetween('created_at', [now()->subWeek(), now()])->count();

        return ($weekTracksCount + $weekPodcastsCount) < $songsLimit;
    }

    public static function ownProfile(User $user): bool
    {
        return $user->id === auth()->id();
    }

    public function checkBannedStatus(int $id) {}
}
