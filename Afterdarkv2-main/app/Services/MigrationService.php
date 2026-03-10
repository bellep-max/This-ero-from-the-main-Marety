<?php

namespace App\Services;

use App\Constants\RoleConstants;
use App\Enums\ActivityTypeEnum;
use App\Enums\AdventureSongTypeEnum;
use App\Enums\RoleEnum;
use App\Models\Activity;
use App\Models\Adventure;
use App\Models\PostTag;
use App\Models\Song;
use App\Models\SongTag;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\NoReturn;

class MigrationService
{
    #[NoReturn]
    public static function createAdventureEntries(): void
    {
        $mainSongs = Song::query()
            ->withoutGlobalScopes()
            ->adventure()
//            ->whereNotNull('file_url')
//            ->whereNotNull('genre')
            ->with('adventureChildSongs.adventureFinalSongs')
            ->get();

        dd($mainSongs);

        foreach ($mainSongs as $song) {
            $adventure = Adventure::create([
                'mp3' => $song->mp3,
                'hd' => $song->hd,
                'hls' => $song->hls,
                'type' => AdventureSongTypeEnum::Heading,
                'title' => $song->title,
                'description' => $song->description,
                'user_id' => $song->user_id,
                'file_url' => $song->file_url,
                'duration' => $song->duration,
                'plays' => $song->plays,
                'released_at' => $song->released_at,
                'copyright' => $song->copyright,
                'allow_download' => $song->allow_download,
                'download_count' => $song->download_count,
                'allow_comments' => $song->allow_comments,
                'approved' => $song->approved,
            ]);

            foreach (explode(',', $song->genre) as $genreId) {
                $adventure->genres()->attach($genreId);
            }

            self::moveMedia($song, $adventure);

            foreach ($song->adventureChildSongs as $childSong) {
                $adventureRoot = $adventure->children()->create([
                    'mp3' => $childSong->mp3,
                    'hd' => $childSong->hd,
                    'hls' => $childSong->hls,
                    'type' => AdventureSongTypeEnum::Root,
                    'order' => $childSong->order,
                    'title' => $childSong->title,
                    'description' => $childSong->description,
                    'user_id' => $childSong->user_id,
                    'file_url' => $childSong->file_url,
                    'duration' => $childSong->duration,
                    'plays' => $childSong->plays,
                    'released_at' => $childSong->released_at,
                    'copyright' => $childSong->copyright,
                    'allow_download' => $childSong->allow_download,
                    'download_count' => $childSong->download_count,
                    'allow_comments' => $childSong->allow_comments,
                    'approved' => $childSong->approved,
                ]);

                self::moveMedia($childSong, $adventureRoot);

                foreach ($childSong->adventureFinalSongs as $finalSong) {
                    $adventureFinal = $adventure->children()->create([
                        'mp3' => $finalSong->mp3,
                        'hd' => $finalSong->hd,
                        'hls' => $finalSong->hls,
                        'type' => AdventureSongTypeEnum::Final,
                        'order' => $finalSong->order,
                        'title' => $finalSong->title,
                        'description' => $finalSong->description,
                        'user_id' => $finalSong->user_id,
                        'file_url' => $finalSong->file_url,
                        'duration' => $finalSong->duration,
                        'plays' => $finalSong->plays,
                        'released_at' => $finalSong->released_at,
                        'copyright' => $finalSong->copyright,
                        'allow_download' => $finalSong->allow_download,
                        'download_count' => $finalSong->download_count,
                        'allow_comments' => $finalSong->allow_comments,
                        'approved' => $finalSong->approved,
                    ]);

                    self::moveMedia($finalSong, $adventureFinal);
                }
            }
        }
    }

    public static function createArtistModelRelations(string $model, string $relation, bool $rollback = false): bool
    {
        return self::createModelRelations($model, $relation, 'artistIds');
    }

    public static function setUserRoles(): void
    {
        foreach (User::query()->withoutGlobalScopes()->doesntHave('roles')->get() as $user) {
            if ($user->group_id == RoleConstants::ADMIN) {
                $user->assignRole(RoleEnum::Admin);

                continue;
            }

            if ($user->hasAudio) {
                $user->assignRole(RoleEnum::Creator);
            } else {
                $user->assignRole(RoleEnum::Listener);
            }
        }
    }

    public static function setTags(): void
    {
        PostTag::query()
            ->has('post')
            ->with('post')
            ->get()
            ->each(function (PostTag $postTag) {
                $tag = Tag::updateOrCreate([
                    'tag' => $postTag->tag,
                ], [
                    'tag' => $postTag->tag,
                ]);

                $post = $postTag->post;

                $post->tags()->attach($tag);
            });

        SongTag::query()
            ->withoutGlobalScopes()
            ->has('song')
            ->with('song')
            ->get()
            ->each(function (SongTag $songTag) {
                $tag = Tag::updateOrCreate([
                    'tag' => $songTag->tag,
                ], [
                    'tag' => $songTag->tag,
                ]);

                $song = $songTag->song;

                $song->tags()->attach($tag);
            });
    }

    public static function createModelRelations(string $model, string $relation, string $column): bool
    {
        $model::query()
            ->whereNotNull($column)
            ->orWhere($column, '!=', '')
            ->chunk(200, function (Collection $models) use ($column, $relation) {
                foreach ($models as $model) {
                    $relatedModelIds = explode(',', $model->$column);
                    $relatedModelIds = array_filter($relatedModelIds);

                    $model->$relation()->sync($relatedModelIds);
                }
            });

        return true;
    }

    public static function setActivityActionRelation(): bool
    {
        Activity::query()
            ->chunk(200, function (Collection $activities) {
                foreach ($activities as $activity) {
                    if (in_array($activity->action, ActivityTypeEnum::getSongActivities())) {
                        $activity->songs()->attach(explode(',', $activity->events));
                    } elseif (in_array($activity->action, ActivityTypeEnum::getUserActivities())) {
                        $activity->users()->attach(explode(',', $activity->events));
                    } elseif ($activity->action === ActivityTypeEnum::addEvent) {
                        $activity->events()->attach(explode(',', $activity->events));
                    } elseif (in_array($activity->action, [ActivityTypeEnum::followPlaylist, ActivityTypeEnum::followArtist])) {
                        $playlistIds = explode(',', $activity->events);
                        $playlistIds = array_filter($playlistIds);

                        $filteredIds = array_filter($playlistIds, function (int $playlistId) use ($activity) {
                            return $playlistId !== $activity->activityable_id;
                        });

                        foreach ($filteredIds as $filteredId) {
                            $newActivity = $activity->replicate();
                            $newActivity->events = '';
                            $newActivity->activityable_id = $filteredId;
                            $newActivity->created_at = $activity->created_at;
                            $newActivity->updated_at = $activity->updated_at;
                            $newActivity->save();
                        }
                    }
                }
            });

        return true;
    }

    private static function moveMedia(Model $modelFrom, Model $modelTo): void
    {
        foreach ($modelFrom->getMedia('*') as $media) {
            $media->copy($modelTo, $media->collection_name, $media->disk);
        }
    }

    //    private static function syncGenreRelation(Model $model): void
    //    {
    //        foreach (explode(',', $model->genre) as $genreId) {
    //            $model->genres()->attach($genreId);
    //        }
    //    }
}
