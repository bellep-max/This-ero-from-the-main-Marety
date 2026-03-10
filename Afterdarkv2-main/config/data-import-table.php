<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Data Import Settings
    |--------------------------------------------------------------------------
    | These settings are used by the Data Import Table seeders.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    | Array containing the following:
    |
    | * source_connection: Name of database connection to import from.
    | * dest_connection: Name of database connection to populate.
    | * page_size: number of items per page
    |
    */

    'defaults' => [
        'source_connection' => env('DATA_IMPORT_SRC_CONN'),
        'dest_connection' => env('DATA_IMPORT_DEST_CONN'),
        'page_size' => env('DATA_IMPORT_PAGE_SIZE', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Per-Table configurations
    |--------------------------------------------------------------------------
    | Settings for a each child class of Database\Seeders\DataImportTable.
    |
    | Class name prefix `DataImportTable` is removed, the remainder is converted
    | to snake_case as a key here.
    |
    | Each table's array may contain matching override keys of the defaults
    | above, as well as:
    |
    | * source_table: Name of table to import from.
    | * dest_table: Name of table to populate.
    | * empty_destination (optional): whether to truncate the destination table
    |     (default true).
    | * page_size (optional): overrides the above, for very wide tables.
    | * column_map: array of "source column" => "destination column" pairs.  Any
    |     column from the source table may be omitted, but at least one column
    |     must be included.
    | * extra_dest_columns: Columns added to the inserted data, keyed by column
    |     name.
    |
    | The values in extra_dest_columns may be literal, which get included in the
    | data to be inserted.
    |
    | However, Database\Seeders\DataImportTable implements some fill methods.
    | The extra_dest_columns key is converted to StudlyCase and prefixed with
    | 'fill' (ex: fillUuid).  If such a method is found, the value given is
    | passed as its only argument.
    |
    */

    'tables' => [
        'activities' => [
            'source_table' => 'activities',
            'dest_table' => 'activities',
            'column_map' => [
                'id' => 'id',
                'user_id' => 'user_id',
                'activityable_id' => 'activityable_id',
                'activityable_type' => 'activityable_type',
                'events' => 'events',
                'action' => 'action',
                'allow_comments' => 'allow_comments',
                'comment_count' => 'comment_count',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
        ],
        'banners' => [
            'source_table' => 'banners',
            'dest_table' => 'banners',
            'column_map' => [
                'id' => 'id',
                'banner_tag' => 'banner_tag',
                'description' => 'description',
                'code' => 'code',
                'approved' => 'approved',
                'short_place' => 'short_place',
                'bstick' => 'bstick',
                'main' => 'main',
                'category' => 'category',
                'group_level' => 'group_level',
                'started_at' => 'started_at',
                'ended_at' => 'ended_at',
                'fpage' => 'fpage',
                'innews' => 'innews',
                'device_level' => 'device_level',
                'allow_views' => 'allow_views',
                'max_views' => 'max_views',
                'allow_counts' => 'allow_counts',
                'max_counts' => 'max_counts',
                'views' => 'views',
                'clicks' => 'clicks',
                'rubric' => 'rubric',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
        ],
        'channels' => [
            'source_table' => 'channels',
            'dest_table' => 'channels',
            'column_map' => [
                'id' => 'id',
                'user_id' => 'user_id',
                'priority' => 'priority',
                'title' => 'title',
                'description' => 'description',
                'alt_name' => 'alt_name',
                'object_ids' => 'object_ids',
                'object_type' => 'type',
                'meta_title' => 'meta_title',
                'meta_description' => 'meta_description',
                'allow_home' => 'allow_home',
                'allow_discover' => 'allow_discover',
                'allow_radio' => 'allow_radio',
                'allow_community' => 'allow_community',
                'allow_podcasts' => 'allow_podcasts',
                'allow_trending' => 'allow_trending',
                'mood' => 'mood',
                'radio' => 'radio',
                'podcast' => 'podcast',
                'visibility' => 'is_visible',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
        ],
        'comments' => [
            'source_table' => 'comments',
            'dest_table' => 'comments',
            'column_map' => [
                'id' => 'id',
                'parent_id' => 'parent_id',
                'reply_count' => 'reply_count',
                'user_id' => 'user_id',
                'commentable_id' => 'commentable_id',
                'commentable_type' => 'commentable_type',
                'content' => 'content',
                'edited' => 'edited',
                'ip' => 'ip',
                'reaction_count' => 'reaction_count',
                'approved' => 'approved',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
        ],
        'emails' => [
            'source_table' => 'emails',
            'dest_table' => 'emails',
            'column_map' => [
                'id' => 'id',
                'type' => 'type',
                'description' => 'description',
                'subject' => 'subject',
                'content' => 'content',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at'
            ],
        ],
        'genres' => [
            'source_table' => 'genres',
            'dest_table' => 'genres',
            'column_map' => [
                'id' => 'id',
                'parent_id' => 'parent_id',
                'priority' => 'priority',
                'discover' => 'discover',
                'name' => 'name',
                'alt_name' => 'alt_name',
                'description' => 'description',
                'meta_title' => 'meta_title',
                'meta_description' => 'meta_description',
                'meta_keywords' => 'meta_keywords',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
        ],
        'histories' => [
            'source_table' => 'histories',
            'dest_table' => 'histories',
            'column_map' => [
                'id' => 'id',
                'user_id' => 'user_id',
                'historyable_id' => 'historyable_id',
                'historyable_type' => 'historyable_type',
                'ownerable_type' => 'ownerable_type',
                'ownerable_id' => 'ownerable_id',
                'interaction_count' => 'interaction_count',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
        ],
        'love' => [
            'source_table' => 'love',
            'dest_table' => 'love',
            'column_map' => [
                'id' => 'id',
                'user_id' => 'user_id',
                'loveable_id' => 'loveable_id',
                'loveable_type' => 'loveable_type',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
        ],
        'media' => [
            'source_table' => 'media',
            'dest_table' => 'media',
            'column_map' => [
                'id' => 'id',
                'model_type' => 'model_type',
                'model_id' => 'model_id',
                'uuid' => 'uuid',
                'collection_name' => 'collection_name',
                'name' => 'name',
                'file_name' => 'file_name',
                'mime_type' => 'mime_type',
                'disk' => 'disk',
                'conversions_disk' => 'conversions_disk',
                'size' => 'size',
                'manipulations' => 'manipulations',
                'custom_properties' => 'custom_properties',
                'responsive_images' => 'responsive_images',
                'order_column' => 'order_column',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
        ],
        'metatags' => [
            'source_table' => 'metatags',
            'dest_table' => 'metatags',
            'column_map' => [
                'id' => 'id',
                'priority' => 'priority',
                'url' => 'url',
                'info' => 'info',
                'page_title' => 'page_title',
                'page_description' => 'page_description',
                'page_keywords' => 'page_keywords',
                'auto_keyword' => 'auto_keyword',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
        ],
        'musicengine_roles' => [
            'source_table' => 'roles',
            'dest_table' => 'musicengine_roles',
            'column_map' => [
                'id' => 'id',
                'name' => 'name',
                'permissions' => 'permissions',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
        ],
        'notifications' => [
            'source_table' => 'notifications',
            'dest_table' => 'notifications',
            'column_map' => [
                'id' => 'id',
                'user_id' => 'user_id',
                'object_id' => 'object_id',
                'notificationable_id' => 'notificationable_id',
                'notificationable_type' => 'notificationable_type',
                'hostable_id' => 'hostable_id',
                'hostable_type' => 'hostable_type',
                'action' => 'action',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
        ],
        'o_auth_socialite' => [
            'source_table' => 'oauth_socialite',
            'dest_table' => 'oauth_socialite',
            'column_map' => [
                'id' => 'id',
                'user_id' => 'user_id',
                'provider_id' => 'provider_id',
                'provider_name' => 'provider_name',
                'provider_email' => 'provider_email',
                'provider_artwork' => 'provider_artwork',
                'service' => 'service',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
        ],
        'pages' => [
            'source_table' => 'pages',
            'dest_table' => 'pages',
            'column_map' => [
                'id' => 'id',
                'user_id' => 'user_id',
                'title' => 'title',
                'alt_name' => 'alt_name',
                'content' => 'content',
                'meta_title' => 'meta_title',
                'meta_description' => 'meta_description',
                'meta_keywords' => 'meta_keywords',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
        ],
        'password_resets' => [
            'source_table' => 'password_resets',
            'dest_table' => 'password_reset_tokens',
            'column_map' => [
                'email' => 'email',
                'token' => 'token',
                'created_at' => 'created_at',
            ],
        ],
        'playlist_songs' => [
            'source_table' => 'playlist_songs',
            'dest_table' => 'playlist_songs',
            'column_map' => [
                'id' => 'id',
                'song_id' => 'song_id',
                'playlist_id' => 'playlist_id',
                'priority' => 'priority',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
        ],
        'playlists' => [
            'source_table' => 'playlists',
            'dest_table' => 'playlists',
            'column_map' => [
                'id' => 'id',
                'user_id' => 'user_id',
                'collaboration' => 'collaboration',
                'mood' => 'mood',
                'title' => 'title',
                'description' => 'description',
                'loves' => 'loves',
                'allow_comments' => 'allow_comments',
                'comment_count' => 'comment_count',
                'visibility' => 'is_visible',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
                'type' => 'type',
            ],
            'extra_dest_columns' => [
                'uuid' => false,
                'is_explicit' => 1,
            ],
        ],
        'posts' => [
            'source_table' => 'posts',
            'dest_table' => 'posts',
            'column_map' => [
                'id' => 'id',
                'user_id' => 'user_id',
                'title' => 'title',
                'alt_name' => 'alt_name',
                'short_content' => 'short_content',
                'full_content' => 'full_content',
                'category' => 'category_id',
                'view_count' => 'view_count',
                'allow_comments' => 'allow_comments',
                'comment_count' => 'comment_count',
                'allow_main' => 'allow_main',
                'disable_index' => 'disable_index',
                'fixed' => 'fixed',
                'meta_title' => 'meta_title',
                'meta_description' => 'meta_description',
                'meta_keywords' => 'meta_keywords',
                'access' => 'access',
                'visibility' => 'is_visible',
                'approved' => 'approved',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
            'extra_dest_columns' => [
                'uuid' => false,
            ],
        ],
        'reactions' => [
            'source_table' => 'reactions',
            'dest_table' => 'reactions',
            'column_map' => [
                'id' => 'id',
                'user_id' => 'user_id',
                'reactionable_id' => 'reactionable_id',
                'reactionable_type' => 'reactionable_type',
                'type' => 'type',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
        ],
        'role_users' => [
            // 'source_table' => 'role_users',
            // 'dest_table' => '',
            // 'column_map' => [
            // ],
        ],
        'songs' => [
            'source_table' => 'songs',
            'dest_table' => 'songs',
            'column_map' => [
                'id' => 'id',
                'user_id' => 'user_id',
                'mp3' => 'mp3',
                'preview' => 'preview', // TO ADD
                'flac' => 'flac',
                'wav' => 'wav',
                'hd' => 'hd',
                'hls' => 'hls',
                'file_url' => 'file_url', // TO ADD
                'explicit' => 'is_explicit', // set to 1 in preInsert()
                'selling' => 'selling',
                'price' => 'price',
                'mood' => 'mood',
                'title' => 'title',
                'description' => 'description',
                'access' => 'access',
                'duration' => 'duration',
                'loves' => 'loves',
                'collectors' => 'collectors',
                'plays' => 'plays',
                'released_at' => 'released_at',
                'copyright' => 'copyright',
                'allow_download' => 'allow_download',
                'download_count' => 'download_count',
                'allow_comments' => 'allow_comments',
                'comment_count' => 'comment_count',
                'visibility' => 'is_visible',
                'approved' => 'approved',
                'pending' => 'pending',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
                'vocal' => 'vocal_id',
                'script' => 'liner_notes',
            ],
            'extra_dest_columns' => [
                'uuid' => false,
                'referral_plays' => 0,
                'is_adventure' => 0,
                'is_patron' => 0,
                'published_at' => null,
            ],
        ],
        'song_tags' => [
            'source_table' => 'song_tags',
            'dest_table' => 'song_tags',
            'column_map' => [
                'id' => 'id',
                'song_id' => 'song_id',
                'tag' => 'tag',
            ],
        ],
        'users' => [
            'source_table' => 'users',
            'dest_table' => 'users',
            'page_size' => 1000,
            'column_map' => [
                'id' => 'id',
                'name' => 'name',
                'username' => 'username',
                'password' => 'password',
                'session_id' => 'session_id',
                'email' => 'email',
                'email_verified' => 'email_verified',
                'email_verified_code' => 'email_verified_code',
                'email_verified_at' => 'email_verified_at',
                'remember_token' => 'remember_token',
                'artist_id' => 'artist_id',
                'logged_ip' => 'logged_ip',
                'last_activity' => 'last_activity',
                'last_seen_notif' => 'last_seen_notif',
                'notification' => 'notification',
                'bio' => 'bio',
                'gender' => 'gender',
                'birth' => 'birth',
                'allow_comments' => 'allow_comments',
                'comment_count' => 'comment_count',
                'restore_queue' => 'restore_queue',
                'persist_shuffle' => 'persist_shuffle',
                'play_pause_fade' => 'play_pause_fade',
                'disablePlayerShortcuts' => 'disable_player_shortcuts',
                'crossfade_amount' => 'crossfade_amount',
                'hd_streaming' => 'hd_streaming',
                'activity_privacy' => 'activity_privacy',
                'notif_follower' => 'notif_follower',
                'notif_playlist' => 'notif_playlist',
                'notif_shares' => 'notif_shares',
                'notif_features' => 'notif_features',
                'trialed' => 'trialed',
                'balance' => 'balance',
                'payment_bank' => 'payment_bank',
                'payment_paypal' => 'payment_paypal',
                'payment_method' => 'payment_method',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
                'script' => 'description',
            ],
            'extra_dest_columns' => [
                'uuid' => false,
                'country_id' => null,
                'linktree_link' => null,
                'group_id' => null,
                'ends_at' => null,
            ],
        ],
        'song_genreables' => [
            'source_table' => 'songs',
            'dest_table' => 'genreables',
            'empty_destination' => false,
            // This is only here to avoid throwing an exception
            'column_map' => [
                'id' => 'id',
				'genre' => 'genre_id',
			],
        ],
    ]
];
