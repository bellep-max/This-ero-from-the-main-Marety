<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('role.badge', null);
        $this->migrator->add('role.user_bio_max_length', 300);
        $this->migrator->add('role.user_description_max_length', 4000);
        $this->migrator->add('role.user_max_download_kbps', 200);
        $this->migrator->add('role.user_max_active_uploads', 5);
        $this->migrator->add('role.albums_limit', 5);
        $this->migrator->add('role.album_images_limit', 1);
        $this->migrator->add('role.album_sale_artist_cut', .6);
        $this->migrator->add('role.album_min_price', 0.99);
        $this->migrator->add('role.album_max_price', 9.99);
        $this->migrator->add('role.tracks_limit', 25);
        $this->migrator->add('role.track_images_limit', 1);
        $this->migrator->add('role.track_hours_editable', 2);
        $this->migrator->add('role.track_sale_artist_cut', .6);
        $this->migrator->add('role.track_min_price', 0.99);
        $this->migrator->add('role.track_max_price', 9.99);
        $this->migrator->add('role.track_stream_artist_rate', 0.00783);
        $this->migrator->add('role.podcasts_limit', 1);
        $this->migrator->add('role.podcast_images_limit', 1);
        $this->migrator->add('role.episodes_limit', 10);
        $this->migrator->add('role.episode_hours_editable', 2);
        $this->migrator->add('role.episode_images_limit', 1);
        $this->migrator->add('role.episode_stream_artist_rate', 0.00783);
        $this->migrator->add('role.playlists_limit', 10);
        $this->migrator->add('role.playlist_images_limit', 3);
        $this->migrator->add('role.playlist_tracks_limit', 20);
        $this->migrator->add('role.playlist_collaborators_limit', 0);
        $this->migrator->add('role.comment_links_limit', 3);
        $this->migrator->add('role.comment_minutes_editiable', 5);
        $this->migrator->add('role.revenue_min_withdraw', 100);
        $this->migrator->add('role.revenue_max_withdraw', 1000);
        $this->migrator->add('role.comments_character_limit', 180);
    }
};
