<?php

namespace App\Services;

use App\Constants\UploadConstants;
use App\Models\User;
use App\Scopes\VisibilityScope;

class UserStatsService
{
    public static function getProfileStats(User $user, bool $ownProfile = false): array
    {
        return $ownProfile
             ? [
                 'tracks' => $user->tracks()->withoutGlobalScopes([VisibilityScope::class])->count(),
                 'podcasts' => $user->podcasts()->withoutGlobalScopes([VisibilityScope::class])->count(),
                 'playlists' => $user->playlists()->withoutGlobalScopes()->count(),
                 'adventures' => $user->adventures()->count(),
                 'followers' => $user->followers()->count(),
                 'following' => $user->following()->count(),
             ] : [
                 'tracks' => $user->tracks()->count(),
                 'podcasts' => $user->podcasts()->count(),
                 'playlists' => $user->playlists()->count(),
                 'adventures' => $user->adventures()->count(),
             ];
    }

    public function canUpload(User $user): bool
    {
        $songsLimit = array_get($user->group->permissions, 'artist_max_songs_per_week', UploadConstants::DEFAULT_UPLOAD_NUMBER);
        $weekTracksCount = $user->tracks()->withoutGlobalScopes()->whereBetween('created_at', [now()->subWeek(), now()])->count();
        $weekPodcastsCount = $user->podcasts()->withoutGlobalScopes()->whereBetween('created_at', [now()->subWeek(), now()])->count();

        return ($weekTracksCount + $weekPodcastsCount) < $songsLimit || $user->activeSubscription();
    }

    public static function ownProfile(User $user): bool
    {
        return $user->id === auth()->id();
    }
}
