<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * ER-153: Placeholder settings class for user-related settings.
 *
 * This class is a placeholder for future migration of non-ACS settings
 * from MusicEngine JSON. Settings will be added as properties as needed.
 *
 * Example usage:
 *   $settings = app(UserSettings::class);
 *   $value = $settings->some_setting;
 */
class UserSettings extends Settings
{
    //
    public bool $public_profile;

    //
    public bool $public_comments;

    //
    public bool $public_albums;

    //
    public bool $public_tracks;

    //
    public bool $public_podcasts;

    //
    public bool $public_episodes;

    //
    public bool $public_playlists;

    //
    public bool $persist_shuffle;

    //
    public bool $restore_queue;

    //
    public bool $play_pause_fade;

    //
    public bool $disable_player_shortcuts;

    // seconds, 0 to 10
    public int $crossfade_amount;

    //
    public bool $enable_hd_play;

    //
    public bool $enable_hd_steaming;

    //
    public bool $allow_profile_comments;

    //
    public bool $allow_album_comments;

    //
    public bool $allow_track_comments;

    //
    public bool $allow_podcast_comments;

    //
    public bool $allow_episode_comments;

    //
    public bool $allow_playlist_comments;

    //
    public bool $notify_followers;

    //
    public bool $notify_playlists;

    //
    public bool $notify_shares;

    //
    public bool $notify_features;

    //
    public bool $notify_following_publish_album;

    //
    public bool $notify_following_publish_track;

    //
    public bool $notify_following_publish_podcast;

    //
    public bool $notify_following_publish_episode;

    //
    public bool $notify_following_publish_playlist;

    public static function group(): string
    {
        return 'user';
    }
}
