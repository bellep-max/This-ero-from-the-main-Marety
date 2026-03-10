<?php

namespace App\Constants;

class ActionConstants
{
    public const ADD_SONG = 'addSong';

    public const ADD_EPISODE = 'add_episode';

    public const ADD_EVENT = 'add_event';

    public const ADD_MOOD = 'add_mood';

    public const ADD_GENRE = 'add_genre';

    public const ADD_CATEGORY = 'add_category';

    public const STORE_CATEGORY = 'save_add_category';

    public const STORE_GENRE = 'save_add_genre';

    public const STORE_MOOD = 'save_add_mood';

    public const EDIT_CATEGORY = 'change_category';

    public const EDIT_AUTHOR = 'change_author';

    public const EDIT_GENRE = 'change_genre';

    public const EDIT_MOOD = 'change_mood';

    public const EDIT_ARTIST = 'change_artist';

    public const EDIT_ALBUM = 'change_album';

    public const UPDATE_AUTHOR = 'save_change_author';

    public const UPDATE_CATEGORY = 'save_change_category';

    public const UPDATE_GENRE = 'save_change_genre';

    public const UPDATE_MOOD = 'save_change_mood';

    public const UPDATE_ARTIST = 'save_change_artist';

    public const UPDATE_ALBUM = 'save_change_album';

    public const FOLLOW_USER = 'follow_user';

    public const COLLECT_SONG = 'collect_song';

    public const PLAY_SONG = 'play_song';

    public const POST_FEED = 'post_feed';

    public const FAVORITE_SONG = 'favorite_song';

    public const FAVORITE_ALBUM = 'favorite_album';

    public const FAVORITE_EPISODE = 'favorite_episode';

    public const FOLLOW_ARTIST = 'follow_artist';

    public const FOLLOW_PLAYLIST = 'follow_playlist';

    public const ADD_TO_PLAYLIST = 'add_to_playlist';

    public const FOLLOW_PODCAST = 'follow_podcast';

    public const REMOVE_PODCAST_EPISODE = 'remove_from_podcast';

    public const REMOVE_PLAYLIST_SONG = 'remove_from_playlist';

    public const LOVE = 'love';

    public const UNLOVE = 'unlove';

    public const INVITE_COLLAB = 'invite_collaboration';

    public const ACCEPTED_COLLAB = 'accepted_collaboration';

    public const INVITE = 'invite';

    public const ACCEPT = 'accept';

    public const CANCEL = 'cancel';

    public const SHARED_MUSIC = 'shared_music';

    public const COMMENT_MENTIONED = 'comment_mentioned';

    public const COMMENT_MUSIC = 'comment_music';

    public const COMMENT_REPLY = 'reply_comment';

    public const COMMENT_REACT = 'react_comment';

    public const VERIFIED = 'verified';

    public const UNVERIFIED = 'unverified';

    public const APPROVE = 'approve';

    public const NOT_APPROVE = 'not_approve';

    public const FIXED = 'fixed';

    public const NOT_FIXED = 'not_fixed';

    public const COMMENTED = 'comments';

    public const UNCOMMENTED = 'not_comments';

    public const CLEAR_COUNT = 'clear_count';

    public const CLEAR_VIEWS = 'clear_views';

    public const CLEAR_TAGS = 'clear_tags';

    public const SET_CURRENT = 'set_current';

    public const SHOW = 'visibility';

    public const HIDE = 'private';

    public const DELETE = 'delete';

    public static function getMassActions(): array
    {
        return [
            self::ADD_GENRE,
            self::STORE_GENRE,
            self::EDIT_GENRE,
            self::UPDATE_GENRE,
            self::STORE_MOOD,
            self::EDIT_MOOD,
            self::UPDATE_MOOD,
            self::VERIFIED,
            self::UNVERIFIED,
            self::COMMENTED,
            self::UNCOMMENTED,
            self::DELETE,
        ];
    }

    public static function getSongMassActions(): array
    {
        return [
            self::ADD_GENRE,
            self::STORE_GENRE,
            self::EDIT_GENRE,
            self::UPDATE_GENRE,
            self::ADD_MOOD,
            self::STORE_MOOD,
            self::EDIT_MOOD,
            self::EDIT_ARTIST,
            self::UPDATE_ARTIST,
            self::EDIT_ALBUM,
            self::UPDATE_ALBUM,
            self::APPROVE,
            self::NOT_APPROVE,
            self::COMMENTED,
            self::UNCOMMENTED,
            self::CLEAR_COUNT,
            self::DELETE,
        ];
    }

    public static function getPostMassActions(): array
    {
        return [
            self::ADD_CATEGORY,
            self::STORE_CATEGORY,
            self::EDIT_CATEGORY,
            self::UPDATE_CATEGORY,
            self::EDIT_AUTHOR,
            self::UPDATE_AUTHOR,
            self::APPROVE,
            self::NOT_APPROVE,
            self::SET_CURRENT,
            self::FIXED,
            self::NOT_FIXED,
            self::CLEAR_VIEWS,
            self::CLEAR_TAGS,
            self::COMMENTED,
            self::UNCOMMENTED,
            self::DELETE,
        ];
    }

    public static function getPlaylistMassActions(): array
    {
        return [
            self::ADD_GENRE,
            self::STORE_GENRE,
            self::EDIT_GENRE,
            self::UPDATE_GENRE,
            self::ADD_MOOD,
            self::STORE_MOOD,
            self::EDIT_MOOD,
            self::UPDATE_MOOD,
            self::SHOW,
            self::HIDE,
            self::COMMENTED,
            self::UNCOMMENTED,
            self::DELETE,
        ];
    }

    public static function getPlaylistTrackMassActions(): array
    {
        return [
            self::REMOVE_PLAYLIST_SONG,
            self::DELETE,
        ];
    }
}
