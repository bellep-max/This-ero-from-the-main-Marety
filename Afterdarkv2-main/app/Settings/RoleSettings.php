<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * ER-153: Placeholder settings class for role-related settings.
 *
 * This class is a placeholder for future migration of non-ACS settings
 * from MusicEngine JSON. Settings will be added as properties as needed.
 *
 * Example usage:
 *   $settings = app(RoleSettings::class);
 *   $value = $settings->some_setting;
 */
class RoleSettings extends Settings
{
    // nullable
    public string $badge;

    // 0 to 1000
    public int $user_bio_max_length;

    // 0 to 10000
    public int $user_description_max_length;

    // 1 to 1000
    public int $user_max_download_kbps;

    // 0 to 10
    public int $user_max_active_uploads;

    // 0 to 200; 0 = unlimited
    public int $albums_limit;

    // 0 to 20; 0 = unlimited
    public int $album_images_limit;

    // 0 to 100
    public float $album_sale_artist_cut;

    // 0 to 100
    public float $album_min_price;

    // 0 to 100
    public float $album_max_price;

    // 0 to 10000; 0 = unlimited
    public int $tracks_limit;

    // 0 to 20; 0 = unlimited
    public int $track_images_limit;

    // 0 to 50; 0 = unlimited
    public int $track_hours_editable;

    // 0 to 100
    public float $track_sale_artist_cut;

    // 0 to 100
    public float $track_min_price;

    // 0 to 100
    public float $track_max_price;

    // 0 to .1
    public float $track_stream_artist_rate;

    // 0 to 200; 0 = unlimited
    public int $podcasts_limit;

    // 0 to 20; 0 = unlimited
    public int $podcast_images_limit;

    // 0 to 100; 0 = unlimited
    public int $episodes_limit;

    // 0 to 50; 0 = unlimited
    public int $episode_hours_editable;

    // 0 to 20; 0 = unlimited
    public int $episode_images_limit;

    // 0 to .1
    public float $episode_stream_artist_rate;

    // 0 to 1000; 0 = unlimited
    public int $playlists_limit;

    // 0 top 20; 0 = unlimited
    public int $playlist_images_limit;

    // 0 to 2000; 0 = unlimited
    public int $playlist_tracks_limit;

    // 0 to 100; 0 = unlimited
    public int $playlist_collaborators_limit;

    // 0 to 4
    public int $comment_links_limit;

    // 0 to 60; 0 = unlimited
    public int $comment_minutes_editiable;

    // 10 to 200
    public int $revenue_min_withdraw;

    // 500 to 10000
    public int $revenue_max_withdraw;

    // 0 to 1000
    public int $comments_character_limit;

    public static function group(): string
    {
        return 'role';
    }
}
