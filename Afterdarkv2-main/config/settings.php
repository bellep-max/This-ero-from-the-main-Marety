<?php

return [
    // Site settings
    'site_title' => 'Music Engine Music Social',
    'short_title' => 'SoundEngine',
    'charset' => 'utf-8',
    'locale' => env('APP_LOCALE', 'en'),
    'lang' => 'en',
    'description' => 'Listen to your music',
    'keyword' => 'music, free listen, free stream, and more',
    'skin' => 'default',
    'site_offline' => 0,
    'offline_reason' => 'Erocast is being moved to a new Faster Server!!!
        It will be unavailable during the switch over process in order to prevent any loss of data.  
        We expect the switch over to take  a few hours.',
    'currency' => 'USD',

    // Conversions
    'ffmpeg' => 1,

    // Storage options
    'storage_audio_location' => 'wasabi',
    'amazon_s3_bucket_name' => '',
    'amazon_s3_key_id' => '',
    'amazon_s3_secret' => '',
    'amazon_s3_region' => '',
    'amazon_s3_url' => '',
    's3_signed_time' => 1140,

    // Images
    'storage_artwork_location' => 'wasabi',
    'image_t_seite' => 0,
    'image_watermark_seite' => 3,
    'image_allow_watermark' => 0,
    'image_watermark_minimum' => 500,
    'image_max_up_side' => 0,
    'image_max_file_size' => env('SETTINGS_IMAGE_MAX_FILE_SIZE', 8096),
    'image_max_thumbnail_size' => env('SETTINGS_IMAGE_MAX_THUMBNAIL_SIZE', 600),
    'image_avatar_size' => env('SETTINGS_IMAGE_AVATAR_SIZE', 300),
    'image_jpeg_quality' => env('SETTINGS_ARTWORK_QUALITY', 90),
    'image_artwork_sm' => env('SETTINGS_ARTWORK_SM_SIZE', 60),
    'image_artwork_md' => env('SETTINGS_ARTWORK_MD_SIZE', 120),
    'image_artwork_lg' => env('SETTINGS_ARTWORK_LG_SIZE', 300),
    'image_artwork_max' => env('SETTINGS_ARTWORK_MAX_SIZE', 500),
    'image_auto_clear' => 2,

    // Audio
    'max_audio_file_size' => 95200,
    'min_audio_bitrate' => 32,
    'max_audio_bitrate' => 320,
    'min_audio_duration' => 6,
    'max_audio_duration' => 6000,
    'audio_upload_flood' => 0,
    'audio_all_types' => 0,
    'audio_stream_hls' => 1,
    'audio_mp3_backup' => 1,
    'audio_default_bitrate' => 128,
    'audio_hd_bitrate' => 320,
    'audio_mp3_preview' => 1,
    'audio_preview_bitrate' => 128,
    'audio_hd' => 0,
    'audio_hls_drm' => 0,
    'audio_preview_duration' => 15,

    // Podcast
    'podcast_day_upload_restriction' => 0,
    'podcast_upload_notification' => 0,
    'podcast_max_audio_file_size' => 51200,
    'podcast_min_audio_bitrate' => 64,
    'podcast_max_audio_bitrate' => 320,
    'podcast_max_audio_duration' => 7200,
    'podcast_audio_upload_flood' => 0,
    'podcast_image_jpeg_quality' => 90,
    'podcast_ffmpeg' => 0,
    'podcast_audio_all_types' => 0,
    'podcast_audio_stream_hls' => 0,
    'podcast_audio_hls_drm' => 0,
    'podcast_audio_default_bitrate' => 64,
    'podcast_comments' => 1,
    'podcast_min_audio_duration' => 60,

    // Email options
    'mailchimp' => 1,
    'admin_mail' => 'admin@admin.com',
    'mail_metod' => 'smtp',
    'smtp_host' => '',
    'smtp_port' => '',
    'smtp_user' => '',
    'smtp_pass' => '',
    'smtp_secure' => 'ssl',
    'mail_title' => '[erocast]',

    // Register options
    'registration_method' => 0,
    'authorization_method' => 0,
    'default_usergroup' => 5,
    'disable_register' => 0,
    'register_rule' => 0,
    'register_captcha' => 0,
    'facebook_app_id' => '',
    'facebook_app_secret' => '',
    'facebook_app_callback_url' => '',
    'google_client_id' => '',
    'google_client_secret' => '',
    'google_redirect' => '',
    'social_login' => 1,
    'facebook_login' => 0,
    'google_login' => 0,
    'apple_login' => 1,
    'discord_login' => 1,
    'reddit_login' => 1,
    'twitter_login' => 0,

    // Comment default values
    'comment_max_chars' => 300,
    'comment_min_chars' => 1,
    'comments_per_page' => 10,
    'comment_order' => 0,
    'comment_flood' => 5,
    'song_comments' => 1,
    'album_comment' => 0,
    'artist_comment' => 0,
    'playlist_comment' => 1,
    'station_comment' => 1,
    'profile_comment' => 1,
    'tree_comment' => 1,
    'song_comment' => 1,
    'pod_comments' => 1,
    'playlist_comments' => 1,
    'album_comments' => 1,
    'artist_comments' => 1,
    'user_comments' => 1,
    'activity_comments' => 1,
    'subcribe_comment' => 0,
    'combination_comment' => 0,
    'comment_index' => 0,
    'comment_notif_admin' => 1,
    'allow_comments_after' => 0,

    // Payment options
    'monetization' => 0,
    'payment_paypal' => 1,
    'payment_paypal_sandbox' => 1,
    'payment_stripe' => 1,
    'payment_stripe_test_mode' => 1,
    'payment_stripe_test_key' => 'YOUR_STRIPE_TEST_KEY_HERE',
    'payment_stripe_publishable_key' => 'YOUR_STRIPE_PUBLISHABLE_KEY_HERE',

    // Post
    'num_post_per_page' => 4,
    'post_restriction' => 0,
    'post_time_format' => 'F j Y, H:i',
    'post_navigation' => 1,
    'post_sort_order' => 0,
    'post_auto_meta' => 1,
    'post_without_scheduling' => 0,
    'post_email_notification' => 1,
    'post_show_sub' => 1,

    // Admin
    'admin_dark_mode' => 0,
    'admin_path' => 'admin',
    'admin_allowed_ip' => null,

    // Recaptcha
    'allow_recaptcha' => 0,
    'recaptcha_public_key' => null,
    'recaptcha_theme' => 0,
    'captcha' => 0,
    'recaptcha_secret_key' => null,

    // Misc
    'google_analytics' => 0,
    'feed_homepage' => 0,
    'extra_login' => 0,
    'block_iframes' => 1,
    'own_ip' => null,
    'login_log' => 5,
    'login_ban_timeout' => 20,
    'log_hash' => 0,
    'artist_flood' => null,
    'users_max' => 0,
    'delete_inactive_user' => 0,
    'share_min_chars' => 1,
    'share_max_chars' => env('SETTINGS_MAX_SHARE_CHARS', 180),
    'num_song_per_swiper' => 4,
    'num_song_per_page' => env('SETTINGS_NUM_TRACKS_PER_PAGE', 20),
    'num_related_song' => 5,
    'day_upload_restriction' => 0,
    'auto_keywords' => 1,
    'upload_notification' => 0,
    'none_artist_upload' => 0,
    'reactions' => 'like,love,haha,wow,sad,angry',
    'firebase_api_key' => null,
    'firebase_auth_domain' => 'testerocast-default-rtdb.firebaseio.com',
    'firebase_database_url' => 'https://testerocast-default-rtdb.firebaseio.com',
    'direct_stream' => 0,
    'automate' => 0,
    'landing' => 1,
    'youtube_api_key' => null,
    'analytic_tracking_code' => null,
    'module_community' => 1,
    'module_podcast' => 1,
    'module_store' => 1,
    'module_radio' => 1,
    'module_blog' => 1,
    'waveform' => 0,
    'deeplink_scheme' => 'musicengine',
    'hide_youtube_player' => 0,
    'import_youtube_library' => 0,
    'import_youtube_method' => 0,

    /*
    |--------------------------------------------------------------------------
    | Spatie Laravel Settings Configuration (ER-153)
    |--------------------------------------------------------------------------
    |
    | The following settings are for the spatie/laravel-settings package.
    | These are separate from the legacy MusicEngine settings above.
    |
    */

    // Settings classes registered with spatie/laravel-settings
    'settings' => [
        App\Settings\RoleSettings::class,
        App\Settings\UserSettings::class,
    ],

    // Path where settings classes will be created
    'setting_class_path' => app_path('Settings'),

    // Directories where settings migrations are stored
    'migrations_paths' => [
        database_path('settings'),
    ],

    // Default repository for loading/saving settings
    'default_repository' => 'database',

    // Settings repositories configuration
    'repositories' => [
        'database' => [
            'type' => Spatie\LaravelSettings\SettingsRepositories\DatabaseSettingsRepository::class,
            'model' => null,
            'table' => null,
            'connection' => null,
        ],
        'redis' => [
            'type' => Spatie\LaravelSettings\SettingsRepositories\RedisSettingsRepository::class,
            'connection' => null,
            'prefix' => null,
        ],
    ],

    // Encoder/decoder for settings storage
    'encoder' => null,
    'decoder' => null,

    // Settings cache configuration
    'cache' => [
        'enabled' => env('SETTINGS_CACHE_ENABLED', false),
        'store' => null,
        'prefix' => null,
        'ttl' => null,
    ],

    // Global casts for non-default PHP types
    'global_casts' => [
        DateTimeInterface::class => Spatie\LaravelSettings\SettingsCasts\DateTimeInterfaceCast::class,
        DateTimeZone::class => Spatie\LaravelSettings\SettingsCasts\DateTimeZoneCast::class,
        Spatie\LaravelData\Data::class => Spatie\LaravelSettings\SettingsCasts\DataCast::class,
    ],

    // Paths for auto-discovering settings classes
    'auto_discover_settings' => [
        app_path('Settings'),
    ],

    // Cache path for discovered settings classes
    'discovered_settings_cache_path' => base_path('bootstrap/cache'),
];
