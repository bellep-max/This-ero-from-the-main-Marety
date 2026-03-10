<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('user.public_profile', true);
        $this->migrator->add('user.public_comments', true);
        $this->migrator->add('user.public_albums', true);
        $this->migrator->add('user.public_tracks', true);
        $this->migrator->add('user.public_podcasts', true);
        $this->migrator->add('user.public_episodes', true);
        $this->migrator->add('user.public_playlists', true);
        $this->migrator->add('user.persist_shuffle', true);
        $this->migrator->add('user.restore_queue', true);
        $this->migrator->add('user.play_pause_fade', true);
        $this->migrator->add('user.disable_player_shortcuts', false);
        $this->migrator->add('user.crossfade_amount', 5);
        $this->migrator->add('user.enable_hd_play', false);
        $this->migrator->add('user.enable_hd_steaming', false);
        $this->migrator->add('user.allow_profile_comments', false);
        $this->migrator->add('user.allow_album_comments', true);
        $this->migrator->add('user.allow_track_comments', true);
        $this->migrator->add('user.allow_podcast_comments', true);
        $this->migrator->add('user.allow_episode_comments', true);
        $this->migrator->add('user.allow_playlist_comments', true);
        $this->migrator->add('user.notify_followers', true);
        $this->migrator->add('user.notify_playlists', true);
        $this->migrator->add('user.notify_shares', true);
        $this->migrator->add('user.notify_features', true);
        $this->migrator->add('user.notify_following_publish_album', true);
        $this->migrator->add('user.notify_following_publish_track', true);
        $this->migrator->add('user.notify_following_publish_podcast', true);
        $this->migrator->add('user.notify_following_publish_episode', true);
        $this->migrator->add('user.notify_following_publish_playlist', true);
    }
};
