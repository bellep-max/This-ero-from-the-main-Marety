<?php

namespace App\Enums;

enum ActivityTypeEnum: string
{
    use EnumMethods;

    case addToPlaylist = 'add_to_playlist';
    case removeFromPlaylist = 'remove_from_playlist';
    case removeFromPodcast = 'remove_from_podcast';

    case inviteCollaborate = 'invite_collaborate';
    case acceptedCollaboration = 'accepted_collaboration';

    case favoriteSong = 'favorite_song';
    case favoriteAlbum = 'favorite_album';
    case favoriteEpisode = 'favorite_episode';

    case collectSong = 'collect_song';

    case playSong = 'play_song';
    case playAdventure = 'play_adventure';

    case addSong = 'add_song';
    case addEpisode = 'add_episode';

    case followUser = 'follow_user';
    case followUserCamel = 'followUser'; // Legacy camelCase support

    case followPlaylist = 'follow_playlist';

    case followArtist = 'follow_artist';
    case followPodcast = 'follow_podcast';

    case addEvent = 'add_event';

    case postFeed = 'post_feed';
    case comment = 'comment';
    case commentMusic = 'commentMusic'; // Legacy camelCase support
    case replyComment = 'reply_comment';
    case replyCommentCamel = 'replyComment'; // Legacy camelCase support
    case reactComment = 'reactComment'; // Legacy camelCase support
    case commentMentioned = 'comment_mentioned';
    case default = 'unknown';

    public static function getSongActivities(): array
    {
        return [
            self::addToPlaylist,
            self::favoriteSong,
            self::collectSong,
            self::playSong,
            self::addSong,
        ];
    }

    public static function getUserActivities(): array
    {
        return [
            self::inviteCollaborate,
            self::followUser,
        ];
    }
}
