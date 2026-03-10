<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Settings\SettingsStoreRequest;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

use function config;

class SettingsController
{
    private const DEFAULT_ROUTE = 'backend.settings.index';

    public function index(): View|Application|Factory
    {
        //        if (! $handle = opendir(resource_path('views/frontend'))) {
        //            exit('Cannot open folder views/frontend in resources folder.');
        //        }
        //
        //        $skins = [];
        //
        //        while (false !== ($file = readdir($handle))) {
        //            if (is_dir(resource_path('views/frontend/'.$file)) and ($file != '.' and $file != '..')) {
        //                $skins[$file] = $file;
        //            }
        //        }
        //
        //        if (! $handle = opendir(resource_path('lang'))) {
        //            exit('Cannot open folder lang in resources folder.');
        //        }
        //
        //        $languages = [];
        //        while (false !== ($file = readdir($handle))) {
        //            if (is_dir(resource_path('lang/'.$file)) and ($file != '.' and $file != '..' and $file != 'vendor')) {
        //                $languages[$file] = trans('langcode.'.$file);
        //            }
        //        }
        //
        //        Cache::forever('languages', array_reverse($languages));
        //
        $storage = [];

        for ($index = 0; $index < count(config('filesystems.disks')); $index++) {
            $storage[array_keys(config('filesystems.disks'))[$index]] = (array_keys(config('filesystems.disks'))[$index]) . ' (driver ' . config('filesystems.disks')[array_keys(config('filesystems.disks'))[$index]]['driver'] . ')';
        }

        return view('backend.settings.index')
            ->with([
                //                'skins' => $skins,
                //                'song_comments' => config('settings.song_comments'),
                //                'languages' => array_reverse($languages),
                'storage' => $storage,
            ]);
    }

    public function store(SettingsStoreRequest $request)
    {
        $saveCon = $request->input('save_con');
        $currentSettings = config('settings');

        foreach ($saveCon as $name => $value) {
            $currentSettings[$name] = $value;
            config(["settings.$name" => $value]);
        }

        $data = var_export($currentSettings, 1);
        /* Clear config cache */

        config(['app.locale' => $saveCon['locale']]);

        if (env('APP_LOCALE') != $saveCon['locale']) {
            $this->envUpdate('APP_LOCALE', $saveCon['locale']);
        }

        $this->envUpdate('MAIL_DRIVER', $request->input('mail_driver'));
        $this->envUpdate('MAIL_HOST', $request->input('mail_host'));
        $this->envUpdate('MAIL_PORT', $request->input('mail_port'));
        $this->envUpdate('MAIL_USERNAME', $request->input('mail_username'));
        $this->envUpdate('MAIL_PASSWORD', $request->input('mail_password'));
        $this->envUpdate('MAIL_ENCRYPTION', $request->input('mail_encryption'));

        $this->envUpdate('AWS_ACCESS_KEY_ID', $request->input('amazon_s3_key_id'));
        $this->envUpdate('AWS_SECRET_ACCESS_KEY', $request->input('amazon_s3_secret'));
        $this->envUpdate('AWS_DEFAULT_REGION', $request->input('amazon_s3_region'));
        $this->envUpdate('AWS_BUCKET', $request->input('amazon_s3_bucket_name'));
        $this->envUpdate('AWS_URL', $request->input('amazon_s3_url'));

        $this->envUpdate('FACEBOOK_APP_ID', $request->input('facebook_app_id'));
        $this->envUpdate('FACEBOOK_APP_SECRET', $request->input('facebook_app_secret'));
        $this->envUpdate('FACEBOOK_APP_CALLBACK_URL', $request->input('facebook_app_callback_url'));

        $this->envUpdate('TWITTER_APP_ID', $request->input('twitter_app_id'));
        $this->envUpdate('TWITTER_APP_SECRET', $request->input('twitter_app_secret'));
        $this->envUpdate('TWITTER_APP_CALLBACK_URL', $request->input('twitter_app_callback_url'));

        $this->envUpdate('GOOGLE_CLIENT_ID', $request->input('google_client_id'));
        $this->envUpdate('GOOGLE_CLIENT_SECRET', $request->input('google_client_secret'));
        $this->envUpdate('GOOGLE_CLIENT_CALLBACK_URL', $request->input('google_app_callback_url'));

        $this->envUpdate('SETTINGS_MAX_SHARE_CHARS', $request->input('share_max_chars'));
        $this->envUpdate('SETTINGS_NUM_TRACKS_PER_PAGE', $request->input('num_song_per_page'));
        $this->envUpdate('SETTINGS_IMAGE_MAX_FILE_SIZE', $request->input('image_max_file_size'));
        $this->envUpdate('SETTINGS_IMAGE_MAX_THUMBNAIL_SIZE', $request->input('image_max_thumbnail_size'));
        $this->envUpdate('SETTINGS_IMAGE_AVATAR_SIZE', $request->input('image_avatar_size'));
        $this->envUpdate('SETTINGS_ARTWORK_QUALITY', $request->input('image_jpeg_quality'));
        $this->envUpdate('SETTINGS_ARTWORK_SM_SIZE', $request->input('image_artwork_sm'));
        $this->envUpdate('SETTINGS_ARTWORK_MD_SIZE', $request->input('image_artwork_md'));
        $this->envUpdate('SETTINGS_ARTWORK_LG_SIZE', $request->input('image_artwork_lg'));
        $this->envUpdate('SETTINGS_ARTWORK_MAX_SIZE', $request->input('image_artwork_max'));

        $this->envUpdate('APP_ADMIN_EMAIL', $saveCon['admin_mail']);

        if (env('APP_ADMIN_PATH') != $request->input('admin_path')) {
            $this->envUpdate('APP_ADMIN_PATH', $request->input('admin_path'));
            Artisan::call('route:clear');
            Artisan::call('route:cache');
        }

        if (File::put(config_path('settings.php'), "<?php\n return $data ;")) {
            return MessageHelper::redirectMessage('Settings successfully updated!', self::DEFAULT_ROUTE);
        } else {
            exit('Permission denied! Please set CHMOD config/settings.php to 666');
        }
    }

    public static function envUpdate(string $key, ?string $value): bool
    {
        $envFile = app()->environmentFilePath();
        $str = File::get($envFile);

        $oldValue = env($key);

        // If the key does not exist in the .env file, add it
        if (!str_contains($str, "$key=")) {
            $str .= "\n$key=$value";
        } else {
            // Replace the old value with the new one
            $str = str_replace("$key=$oldValue", "$key=$value", $str);
        }

        File::put($envFile, $str);

        return true;
    }
}
