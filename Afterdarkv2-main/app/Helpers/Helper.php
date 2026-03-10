<?php

namespace App\Helpers;

use App\Constants\ActionConstants;
use App\Constants\DefaultConstants;
use App\Constants\TypeConstants;
use App\Jobs\GetAlbumDetails;
use App\Models\Activity;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Category;
use App\Models\City;
use App\Models\Collection;
use App\Models\Country;
use App\Models\CountryLanguage;
use App\Models\Episode;
use App\Models\Genre;
use App\Models\Group;
use App\Models\Love;
use App\Models\Meta;
use App\Models\Notification;
use App\Models\Playlist;
use App\Models\Podcast;
use App\Models\PodcastCategory;
use App\Models\RadioCategory;
use App\Models\Region;
use App\Models\Song;
use App\Models\SongTag;
use App\Models\User;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use DOMDocument;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;
use stdClass;
use Torann\LaravelMetaTags\Facades\MetaTag;

class Helper
{
    public static function thousandsCurrencyFormat($num)
    {
        if ($num > 1000) {
            $x = round($num);
            $x_number_format = number_format($x);
            $x_array = explode(',', $x_number_format);
            $x_parts = ['k', 'm', 'b', 't'];
            $x_count_parts = count($x_array) - 1;
            $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
            $x_display .= $x_parts[$x_count_parts - 1];

            return $x_display;
        }

        return $num;
    }

    public static function pushNotification($toUserId, $notificationableId, $notificationableType, $action, $objectId = null)
    {
        if ($toUserId == auth()->id()) {
            return [];
        }

        return Notification::create([
            'user_id' => $toUserId,
            'object_id' => $objectId,
            'notificationable_id' => $notificationableId,
            'notificationable_type' => $notificationableType,
            'action' => $action,
            'hostable_id' => auth()->id(),
            'hostable_type' => User::class,
        ]);
    }

    public static function pushNotificationMentioned($content, $notificationableId, $notificationableType, $action, $objectId = null): void
    {
        $dom = new DOMDocument;
        @$dom->loadHTML($content);
        $tags = $dom->getElementsByTagName('tag');

        foreach ($tags as $tag) {
            if ($tag->getAttribute('data-id')) {
                self::pushNotification(
                    $tag->getAttribute('data-id'),
                    $notificationableId,
                    $notificationableType,
                    $action,
                    $objectId
                );
            }
        }
    }

    public static function groupPermission($access): ?array
    {
        if (!$access) {
            return null;
        }

        $data = [];
        $groups = explode('||', $access);
        foreach ($groups as $group) {
            [$groupid, $groupvalue] = explode(':', $group);
            $data[$groupid] = $groupvalue;
        }

        return $data;
    }

    #[NoReturn]
    public static function abortNoPermission(): void
    {
        $view = view()->make('commons.abort-no-permission');

        if (request()->ajax()) {
            $sections = $view->renderSections();
            exit($sections['content']);
        }

        exit($view);
    }

    public static function br2nl($input): array|string|null
    {
        return preg_replace('#<br[/\s]*>#si', "\n", $input);
    }

    public static function mentionToLink($string, $withAt = true): array|string|null
    {
        return preg_replace_callback("/<tag\sdata-id=\"(.+?)\"\sdata-username=\"(.+?)\">(.+?)<\/tag>/is", function ($matches) use ($withAt) {
            if ($withAt) {
                return '<a href="' . route('frontend.user.show', ['user' => $matches[2]]) . "\">@$matches[3]</a>";
            } else {
                return '<a href="' . route('frontend.user.show', ['user' => $matches[2]]) . "\">$matches[3]</a>";
            }
        }, $string);
    }

    public static function hashtagToLink($string): array|string|null
    {
        return preg_replace_callback("/#(\w+)/", function ($matches) {
            return '<a href="' . route('frontend.hashtag', ['slug' => $matches[1]]) . "\">#$matches[1]</a>";
        }, $string);
    }

    public static function humanTime($timestamp): string
    {
        return intval($timestamp) > 3600 ? date('H:i:s', intval($timestamp)) : date('i:s', intval($timestamp));
    }

    public static function nl2li($str): false|string
    {
        if (!isset($str)) {
            return false;
        }
        $arr = explode("\r\n", $str);
        $li = array_map(function ($s) {
            return '<li>' . $s . '</li>';
        }, $arr);

        return implode($li);
    }

    public static function htmlLink($title, $url, $class = null): string
    {
        if ($class) {
            return '<a href="' . $url . '" class="' . $class . '">' . $title . '</a>';
        } else {
            return '<a href="' . $url . '">' . $title . '</a>';
        }
    }

    public static function timeElapsedString($datetime, $full = false): string
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;
        $string = [
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }
        if (!$full) {
            $string = array_slice($string, 0, 1);
        }

        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    public static function timeElapsedShortString($datetime, $full = false): string
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;
        $string = [
            'y' => 'y',
            'm' => 'm',
            'w' => 'w',
            'd' => 'd',
            'h' => 'h',
            'i' => 'm',
            's' => 's',
        ];
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . $v;
            } else {
                unset($string[$k]);
            }
        }
        if (!$full) {
            $string = array_slice($string, 0, 1);
        }

        return $string ? implode(', ', $string) : 'just now';
    }

    /**
     * Loops through a folder and requires all PHP files
     * Searches sub-directories as well.
     */
    public function includeRouteFiles($folder): void
    {
        $directory = $folder;
        $handle = opendir($directory);
        $directoryList = [$directory];

        while (false !== ($filename = readdir($handle))) {
            if ($filename != '.' && $filename != '..' && is_dir($directory . $filename)) {
                $directoryList[] = $directory . $filename . '/';
            }
        }

        foreach ($directoryList as $directory) {
            foreach (glob($directory . '*.php') as $filename) {
                require $filename;
            }
        }
    }

    /**
     * Generate UUID.
     */
    public static function generateUuid($string, int $len = 10): string
    {
        $hex = md5($string);
        $pack = pack('H*', $hex);
        $uid = base64_encode($pack);
        $uid = preg_replace('/[^a-zA-Z 0-9]+/', '', $uid);
        if ($len < 4) {
            $len = 4;
        }
        if ($len > 128) {
            $len = 128;
        }
        while (strlen($uid) < $len) {
            $uid = $uid . Str::uuid();
        }

        return substr($uid, 0, $len);
    }

    public static function randomFolderName($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): string
    {
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; $i++) {
            $pieces[] = $keyspace[random_int(0, $max)];
        }

        return implode('', $pieces);
    }

    public static function genreFromIds($genre)
    {
        return $genre
            ? (new Genre)->get('id IN (' . $genre . ')', '', 4)
            : (object) [];
    }

    public static function genreSelection(int $categoryId = 0, int $parentId = 0, bool $nocat = true, string $sublevelMarker = '', string $returnString = '', bool $limit = false): string
    {
        $catInfo = [];

        foreach (Genre::query()->discover()->get() as $row) {
            $catInfo[$row->id] = json_decode(json_encode($row), true);
        }

        if (count($catInfo)) {
            foreach ($catInfo as $key) {
                $cat[$key['id']] = $key['name'];
                $catParentId[$key['id']] = $key['parent_id'];
            }
        }

        if ($parentId == 0) {
            // Disable for select2
            // if( $nocat ) $returnString .= '<option value="0"></option>';
        } else {
            $sublevelMarker .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        }

        if (isset($catParentId)) {
            $rootCategory = @array_keys($catParentId, $parentId);

            if (is_array($rootCategory)) {
                foreach ($rootCategory as $id) {
                    $categoryName = $cat[$id];
                    $color = 'black';
                    $returnString .= "<option style=\"color: $color\" value=\"" . $id . '" ';

                    if (is_array($categoryId)) {
                        foreach ($categoryId as $element) {
                            if ($element == $id) {
                                $returnString .= 'SELECTED';
                            }
                        }
                    } elseif ($categoryId == $id) {
                        $returnString .= 'SELECTED';
                    }
                    $returnString .= '>' . $sublevelMarker . $categoryName . '</option>';
                }
            }
        }

        return $returnString;
    }

    public static function radioCategorySelection(int $categoryId = 0, int $parentId = 0, bool $nocat = true, string $sublevelMarker = '', string $returnString = '')
    {
        $rows = RadioCategory::all();
        $catInfo = [];

        foreach ($rows as $row) {
            $catInfo[$row->id] = [];
            $catInfo[$row->id] = json_decode(json_encode($row), true);
        }

        if (count($catInfo)) {
            foreach ($catInfo as $key) {
                $cat[$key['id']] = $key['name'];
                $catParentId[$key['id']] = $key['parent_id'];
            }
        }

        if ($parentId == 0) {
            $sublevelMarker = '';
            // if( $nocat ) $returnString .= '<option value="0"></option>';
        } else {
            $sublevelMarker .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        }

        if (isset($catParentId)) {
            $rootCategory = @array_keys($catParentId, $parentId);

            if (is_array($rootCategory)) {
                foreach ($rootCategory as $id) {
                    $categoryName = $cat[$id];
                    $color = 'black';
                    $returnString .= '<option value="' . $id . '" ';

                    if (is_array($categoryId)) {
                        foreach ($categoryId as $element) {
                            if ($element == $id) {
                                $returnString .= 'SELECTED';
                            }
                        }
                    } elseif ($categoryId == $id) {
                        $returnString .= 'SELECTED';
                    }
                    $returnString .= '>' . $sublevelMarker . $categoryName . '</option>';
                    $returnString = self::radioCategorySelection($categoryId, $id, $nocat, $sublevelMarker, $returnString);
                }
            }
        }

        return $returnString;
    }

    public static function podcastCategorySelection(int $categoryId = 0, int $parentId = 0, bool $nocat = true, string $sublevelMarker = '', string $returnString = '')
    {
        $rows = PodcastCategory::all();
        $catInfo = [];

        foreach ($rows as $row) {
            $catInfo[$row->id] = [];
            $catInfo[$row->id] = json_decode(json_encode($row), true);
        }

        if (count($catInfo)) {
            foreach ($catInfo as $key) {
                $cat[$key['id']] = $key['name'];
                $catParentId[$key['id']] = $key['parent_id'];
            }
        }

        if ($parentId == 0) {
            $sublevelMarker = '';
            // if( $nocat ) $returnString .= '<option value="0"></option>';
        } else {
            $sublevelMarker .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        }

        if (isset($catParentId)) {
            $rootCategory = @array_keys($catParentId, $parentId);

            if (is_array($rootCategory)) {
                foreach ($rootCategory as $id) {
                    $categoryName = $cat[$id];
                    $color = 'black';
                    $returnString .= '<option value="' . $id . '" ';

                    if (is_array($categoryId)) {
                        foreach ($categoryId as $element) {
                            if ($element == $id) {
                                $returnString .= 'SELECTED';
                            }
                        }
                    } elseif ($categoryId == $id) {
                        $returnString .= 'SELECTED';
                    }
                    $returnString .= '>' . $sublevelMarker . $categoryName . '</option>';
                    $returnString = self::podcastCategorySelection($categoryId, $id, $nocat, $sublevelMarker, $returnString);
                }
            }
        }

        return $returnString;
    }

    public static function categorySelection(int $categoryId = 0, int $parentId = 0, bool $nocat = true, string $sublevelMarker = '', string $returnString = '')
    {
        $rows = Category::all();
        $catInfo = [];

        foreach ($rows as $row) {
            $catInfo[$row->id] = [];
            $catInfo[$row->id] = json_decode(json_encode($row), true);
        }

        if (count($catInfo)) {
            foreach ($catInfo as $key) {
                $cat[$key['id']] = $key['name'];
                $catParentId[$key['id']] = $key['parent_id'];
            }
        }

        if ($parentId == 0) {
            $sublevelMarker = '';
            // if( $nocat ) $returnString .= '<option value="0"></option>';
        } else {
            $sublevelMarker .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        }

        if (isset($catParentId)) {
            $rootCategory = @array_keys($catParentId, $parentId);

            if (is_array($rootCategory)) {
                foreach ($rootCategory as $id) {
                    $categoryName = $cat[$id];
                    $color = 'black';
                    $returnString .= '<option value="' . $id . '" ';

                    if (is_array($categoryId)) {
                        foreach ($categoryId as $element) {
                            if ($element == $id) {
                                $returnString .= 'SELECTED';
                            }
                        }
                    } elseif ($categoryId == $id) {
                        $returnString .= 'SELECTED';
                    }

                    $returnString .= '>' . $sublevelMarker . $categoryName . '</option>';

                    $returnString = self::categorySelection($categoryId, $id, $nocat, $sublevelMarker, $returnString);
                }
            }
        }

        return $returnString;
    }

    public static function makeChannelDropDown($options, $name, $selected): string
    {
        $output = "<select name=\"$name\" class=\"form-control slide-show-type\"><option disabled selected value></option>";
        foreach ($options as $value => $description) {
            $output .= "<option value=\"$value\"";
            if ($selected == $value) {
                $output .= ' selected ';
            }
            $output .= ">$description</option>";
        }
        $output .= '</select>';

        return $output;
    }

    public static function makeCountryDropDown($name, $class, $selected = null): string
    {
        $output = "<select name=\"$name\" class=\"$class\" style=\"display: none\"><option disabled selected value></option>";

        foreach (Country::all() as $country) {
            $output .= "<option value=\"$country->code\"";

            if ($selected == $country->code) {
                $output .= ' selected ';
            }

            $output .= ">$country->name</option>";
        }

        $output .= '</select>';

        return $output;
    }

    public static function makeCityDropDown($countryCode, $name, $class, $selected = null): string
    {
        $output = "<select name=\"$name\" class=\"$class\" style=\"display: none\"><option disabled selected value></option>";

        foreach (City::query()->where('country_id', $countryCode)->get() as $city) {
            $output .= "<option value=\"$city->id\"";

            if ($selected == $city->id) {
                $output .= ' selected ';
            }

            $output .= ">$city->name</option>";
        }

        $output .= '</select>';

        return $output;
    }

    public static function makeRegionDropDown($name, $class, $selected = null): string
    {
        $output = "<select name=\"$name\" class=\"$class\"><option disabled selected value></option>";

        foreach (Region::all() as $region) {
            $output .= "<option value=\"$region->id\"";

            if ($selected == $region->id) {
                $output .= ' selected ';
            }

            $output .= ">$region->name</option>";
        }

        $output .= '</select>';

        return $output;
    }

    public static function makeCountryLanguageDropDown($countryCode, $name, $class, $selected = null): string
    {
        $languages = CountryLanguage::query()
            ->where('country_id', $countryCode)
            ->get();

        $output = "<select name=\"$name\" class=\"$class\" style=\"display: none\"><option disabled selected value></option>";

        foreach ($languages as $language) {
            $output .= "<option value=\"$language->id\"";

            if ($selected == $language->id) {
                $output .= ' selected ';
            }

            $output .= ">$language->name</option>";
        }

        $output .= '</select>';

        return $output;
    }

    public static function makeSlideShowDropDown($options, $name, $selected): string
    {
        $output = "<select name=\"$name\" class=\"form-control slide-show-type\">";

        foreach ($options as $value => $description) {
            $output .= "<option value=\"$value\"";

            if ($selected == $value) {
                $output .= ' selected ';
            }

            $output .= ">$description</option>";
        }

        $output .= '</select>';

        return $output;
    }

    public static function makeRolesDropDown($name, $selected = null, $required = ''): string
    {
        $output = "<select name=\"$name\" class=\"form-control select2-active\" $required><option></option>";

        foreach (Group::all() as $role) {
            if ($role->id != 6) {
                $output .= "<option value=\"$role->id\"";

                if ($selected == $role->id) {
                    $output .= ' selected ';
                }

                $output .= ">$role->name</option>";
            }
        }

        $output .= '</select>';

        return $output;
    }

    public static function makeDropDown($options, $name, $selected = null, $disableFirstOption = true): string
    {
        $output = "<select name=\"$name\" class=\"form-select select2-active select2\">";

        if (!$disableFirstOption) {
            $output .= '<option disabled selected value></option>';
        }

        foreach ($options as $value => $description) {
            $output .= "<option value=\"$value\"";

            if ($selected == $value) {
                $output .= ' selected ';
            }

            $output .= ">$description</option>";
        }

        $output .= '</select>';

        return $output;
    }

    public static function makeTagSelector($name, $tags = ''): string
    {
        $allTags = SongTag::all('tag')->unique('tag')->pluck('tag')->toArray();

        $output = "<select name=\"$name\" class=\"form-control select2-tags\"  multiple=\"multiple\">";

        if ($tags && !is_array($tags)) {
            $tags = explode(',', $tags);
        }

        foreach ($allTags as $defaultTag) {
            $output .= "<option value=\"$defaultTag\"";

            if ($tags && in_array($defaultTag, $tags)) {
                $output .= ' selected ';
            }

            $output .= ">$defaultTag</option>";
        }

        $output .= '</select>';

        return $output;
    }

    public static function makeCheckBox($name, $selected = false): string
    {
        $selected ? $selected = 'checked' : $selected = '';

        return "<input type=\"hidden\" name=\"$name\" value=\"0\" /><input type=\"checkbox\" name=\"$name\" value=\"1\" $selected>";
    }

    public static function makeActivity($hostId, $objectId, $objectType, $action, $events, $collapse = true, $schedule = null): void
    {
        if ($schedule) {
            Activity::insert([
                'user_id' => $hostId,
                'activityable_id' => $objectId,
                'activityable_type' => $objectType,
                'events' => $events,
                'action' => $action,
                'created_at' => $schedule,
            ]);
        } else {
            $row = Activity::query()
                ->where('user_id', $hostId)
                ->where('activityable_type', $objectType)
                ->where('action', $action)
                ->latest('id');

            if ($action == ActionConstants::ADD_TO_PLAYLIST) {
                $row = $row->where('activityable_id', $objectId);
            }

            $row = $row->first();

            if (isset($row->created_at) && Carbon::parse($row->created_at)->addHours(2)->isPast() && $collapse) {
                $newList = $events . ',' . $row->events;
                $newList = explode(',', $newList);
                $newList = array_unique($newList);
                array_filter($newList);
                $newList = implode(',', $newList);

                Activity::query()
                    ->where('id', $row->id)
                    ->update([
                        'events' => $newList,
                        'updated_at' => Carbon::now(),
                    ]);
            } else {
                Activity::insert([
                    'user_id' => $hostId,
                    'activityable_id' => $objectId,
                    'activityable_type' => $objectType,
                    'events' => $events,
                    'action' => $action,
                ]);
            }

            if ($action == ActionConstants::FOLLOW_USER) {
                Notification::create([
                    'user_id' => $objectId,
                    'notificationable_id' => auth()->id(),
                    'notificationable_type' => User::class,
                    'action' => ActionConstants::FOLLOW_USER,
                    'hostable_id' => auth()->id(),
                    'hostable_type' => User::class,
                ]);
            } elseif ($action == ActionConstants::COLLECT_SONG) {
                $song = Song::findOrFail($objectId);
                $song->increment('plays');

                if (isset($song->artists[0])) {
                    $artist_id = $song->artists[0]->id;
                    // Insert song statistic
                    DB::statement('INSERT INTO ' . DB::getTablePrefix() . 'popular (`song_id`, `artist_id`, `collections`, `created_at`) VALUES (' . intval($objectId) . ", '" . $artist_id . "', '1', '" . Carbon::now() . "') ON DUPLICATE KEY UPDATE collections = collections + 1");
                }
            } elseif ($action == ActionConstants::FAVORITE_SONG) {
                $song = Song::query()->withoutGlobalScopes()->findOrFail($objectId);
                $song->increment('plays');

                if (isset($song->artists[0])) {
                    $artist_id = $song->artists[0]->id;
                    // Insert song statistic
                    DB::statement('INSERT INTO ' . DB::getTablePrefix() . 'popular (`song_id`, `artist_id`, `favorites`, `created_at`) VALUES (' . intval($objectId) . ", '" . $artist_id . "', '1', '" . Carbon::now() . "') ON DUPLICATE KEY UPDATE favorites = favorites + 1");
                }
            }
        }
    }

    public static function makeLibrary($action, $objectId, $objectType): void
    {
        if ($action == ActionConstants::LOVE) {
            switch ($objectType) {
                case TypeConstants::SONG:
                    $loveAbleType = (new Song)->getMorphClass();
                    Song::query()
                        ->where('id', $objectId)
                        ->increment('collectors');

                    self::makeActivity(auth()->id(), $objectId, $loveAbleType, ActionConstants::COLLECT_SONG, $objectId);
                    break;
                default:
                    $loveAbleType = null;
            }

            try {
                Collection::create([
                    'user_id' => auth()->id(),
                    'collectionable_id' => $objectId,
                    'collectionable_type' => $loveAbleType,
                ]);
            } catch (Exception $e) {
            }
        } else {
            switch ($objectType) {
                case TypeConstants::SONG:
                    $loveAbleType = (new Song)->getMorphClass();
                    Song::query()->where('id', $objectId)->decrement('collectors');
                    break;
                default:
                    $loveAbleType = null;
            }

            Collection::query()
                ->where('user_id', auth()->id())
                ->where('collectionable_id', $objectId)
                ->where('collectionable_type', $loveAbleType)
                ->delete();
        }
    }

    public static function makeFavorite($action, $objectId, $objectType): void
    {
        if ($action == ActionConstants::LOVE) {
            switch ($objectType) {
                case TypeConstants::SONG:
                    $loveAbleType = (new Song)->getMorphClass();
                    Song::query()
                        ->where('id', $objectId)
                        ->increment('loves');
                    self::makeActivity(auth()->id(), $objectId, $loveAbleType, ActionConstants::FAVORITE_SONG, $objectId);
                    break;
                case TypeConstants::ALBUM:
                    $loveAbleType = (new Album)->getMorphClass();
                    Album::query()
                        ->where('id', $objectId)
                        ->increment('loves');
                    self::makeActivity(auth()->id(), $objectId, $loveAbleType, ActionConstants::FAVORITE_ALBUM, $objectId, false);
                    break;
                case TypeConstants::ARTIST:
                    $loveAbleType = (new Artist)->getMorphClass();
                    Artist::query()
                        ->where('id', $objectId)
                        ->increment('loves');
                    self::makeActivity(auth()->id(), $objectId, $loveAbleType, ActionConstants::FOLLOW_ARTIST, $objectId);
                    break;
                case TypeConstants::PLAYLIST:
                    $loveAbleType = (new Playlist)->getMorphClass();
                    Playlist::query()
                        ->where('id', $objectId)
                        ->increment('loves');
                    self::makeActivity(auth()->id(), $objectId, $loveAbleType, ActionConstants::FOLLOW_PLAYLIST, $objectId);
                    break;
                case TypeConstants::PODCAST:
                    $loveAbleType = (new Podcast)->getMorphClass();
                    Podcast::query()
                        ->where('id', $objectId)
                        ->increment('loves');
                    self::makeActivity(auth()->id(), $objectId, $loveAbleType, ActionConstants::FOLLOW_PODCAST, $objectId);
                    break;
                case TypeConstants::EPISODE:
                    $loveAbleType = (new Episode)->getMorphClass();
                    Episode::query()
                        ->where('id', $objectId)
                        ->increment('loves');
                    self::makeActivity(auth()->id(), $objectId, $loveAbleType, ActionConstants::FAVORITE_EPISODE, $objectId);
                    break;
                case TypeConstants::USER:
                    $loveAbleType = (new User)->getMorphClass();
                    self::makeActivity(auth()->id(), $objectId, $loveAbleType, ActionConstants::FOLLOW_USER, $objectId);
                    break;
                default:
                    $loveAbleType = null;
            }

            try {
                Love::updateOrCreate([
                    'user_id' => auth()->id(),
                    'loveable_id' => $objectId,
                    'loveable_type' => $loveAbleType,
                ]);
            } catch (Exception $e) {
            }
        } else {
            switch ($objectType) {
                case TypeConstants::SONG:
                    $loveAbleType = (new Song)->getMorphClass();
                    Song::query()
                        ->where('id', $objectId)
                        ->decrement('loves');
                    break;
                case TypeConstants::ALBUM:
                    $loveAbleType = (new Album)->getMorphClass();
                    Album::query()
                        ->where('id', $objectId)
                        ->decrement('loves');
                    break;
                case TypeConstants::ARTIST:
                    $loveAbleType = (new Artist)->getMorphClass();
                    Artist::query()
                        ->where('id', $objectId)
                        ->decrement('loves');
                    break;
                case TypeConstants::PLAYLIST:
                    $loveAbleType = (new Playlist)->getMorphClass();
                    Playlist::query()
                        ->where('id', $objectId)
                        ->decrement('loves');
                    break;
                case TypeConstants::USER:
                    $loveAbleType = (new User)->getMorphClass();
                    break;
                default:
                    $loveAbleType = null;
            }

            Love::query()
                ->where('user_id', auth()->id())
                ->where('loveable_id', $objectId)
                ->where('loveable_type', $loveAbleType)
                ->delete();
        }
    }

    public static function sortArrayByDate($a, $b): int
    {
        return strtotime($a['date']) - strtotime($b['date']);
    }

    public static function insertMissingData($originalData, $types, $startDate, $endDate): array
    {
        $dates = [];
        $data = [];
        foreach ($originalData as $d) {
            $d = (array) $d;
            $dates[] = $d['date'];
            $data[] = $d;
        }

        sort($dates);

        $period = new DatePeriod(
            new DateTime($startDate),
            new DateInterval('P1D'),
            new DateTime($endDate)
        );

        foreach ($period as $d) {
            $key = $d->format('Y-m-d');
            if (!in_array($key, $dates)) {
                $array = [];
                foreach ($types as $type) {
                    $array[$type] = 0;
                }
                $array['date'] = $key;
                $data[] = $array;
            }
        }
        usort($data, 'sortArrayByDate');

        return $data;
    }

    public static function insertMissingDate($data, $action, $startDate, $endDate)
    {
        $dates = [];
        foreach ($data as $d) {
            $dates[] = $d->created_at;
        }
        sort($dates);
        $period = new DatePeriod(
            new DateTime($startDate),
            new DateInterval('P1D'),
            new DateTime($endDate)
        );
        foreach ($period as $d) {
            $key = $d->format('Y-m-d');
            if (!in_array($key, $dates)) {
                $data[] = [
                    $action => 0,
                    'created_at' => $key,
                ];
            }
        }

        $data = (array) $data;
        usort($data, 'sortArrayByDate');

        return $data[0];
    }

    public static function objectsToHtmlLink($objects): string
    {
        $objectLink = '';
        foreach ($objects as $key => $item) {
            $title = $item->name ?? $item->title;
            $objectLink .= '<a href="' . $item->url . '">' . $title . '</a>';

            if ($key !== count($objects) - 1) {
                $objectLink .= ', ';
            }
        }

        return $objectLink;
    }

    public static function fileSizeConverter($size): string
    {
        $bytes = intval($size);
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        if ($size == 0) {
            return '';
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public static function clearUrlForMetatags($a): array|string|null
    {
        if (!$a) {
            return '';
        }

        if (str_starts_with($a, '//')) {
            $a = 'http:' . $a;
        }
        $a = parse_url($a);

        if (isset($a['query'])) {
            $a = $a['path'] . '?' . $a['query'];
        } else {
            if (isset($a['path'])) {
                $a = $a['path'];
            }
        }

        $a = preg_replace('#[/]+#i', '/', $a);

        if ($a[0] != '/') {
            $a = '/' . $a;
        }

        return $a;
    }

    public static function detectEncoding($string): ?string
    {
        static $list = ['utf-8', 'windows-1251'];

        foreach ($list as $item) {
            if (function_exists('mb_convert_encoding')) {
                $sample = mb_convert_encoding($string, $item, $item);
            } elseif (function_exists('iconv')) {
                $sample = iconv($item, $item, $string);
            }

            if (md5($sample) == md5($string)) {
                return $item;
            }
        }

        return null;
    }

    public static function getMetatags($item = null): void
    {
        $customMetatags = [];
        $pageHeaderInfo = [];

        if (!Cache::has('metatags')) {
            $rows = Meta::query()
                ->orderBy('priority', 'desc')
                ->get();

            foreach ($rows as $row) {
                if (str_contains($row->url, '*')) {
                    $row->url = preg_quote(urldecode($row->url), '%');
                    $row->url = '%^' . str_replace('\*', '(.*)', $row->url) . '%i';
                    $customMetatags['regex'][$row->url] = [
                        'page_title' => $row->page_title,
                        'page_description' => stripslashes($row->page_description),
                        'page_keywords' => $row->page_keywords,
                        'artwork' => $row->artwork,
                    ];
                } else {
                    $row->url = urldecode($row->url);
                    $customMetatags['simple'][$row->url] = [
                        'page_title' => $row->page_title,
                        'page_description' => stripslashes($row->page_description),
                        'page_keywords' => $row->page_keywords,
                        'artwork_url' => $row->artwork_url,
                    ];
                }
            }

            Cache::forever('metatags', $customMetatags);
        } else {
            $customMetatags = Cache::get('metatags');
        }

        $rUri = preg_replace('#[/]+#i', '/', urldecode($_SERVER['REQUEST_URI']));

        /** site char set */
        $charset = 'utf-8';

        /* end charset */

        $urlCharset = self::detectEncoding($rUri);

        if ($urlCharset && $urlCharset != $charset) {
            if (function_exists('mb_convert_encoding')) {
                $rUri = mb_convert_encoding($rUri, $charset, $urlCharset);
            } elseif (function_exists('iconv')) {
                $rUri = iconv($urlCharset, $charset, $rUri);
            }
        }
        if (is_array($customMetatags['simple']) && count($customMetatags['simple']) && isset($customMetatags['simple'][$rUri])) {
            if ($customMetatags['simple'][$rUri]['page_title']) {
                $pageHeaderInfo['title'] = $customMetatags['simple'][$rUri]['page_title'];
            }
            if ($customMetatags['simple'][$rUri]['page_description']) {
                $pageHeaderInfo['description'] = $customMetatags['simple'][$rUri]['page_description'];
            }
            if ($customMetatags['simple'][$rUri]['page_keywords']) {
                $pageHeaderInfo['keywords'] = $customMetatags['simple'][$rUri]['page_keywords'];
            }
            if (isset($customMetatags['simple'][$rUri]['artwork_url'])) {
                $pageHeaderInfo['artwork_url'] = $customMetatags['simple'][$rUri]['artwork_url'];
            }
        } elseif (isset($customMetatags['regex']) && is_array($customMetatags['regex']) && count($customMetatags['regex'])) {
            foreach ($customMetatags['regex'] as $key => $value) {
                if (preg_match($key, $rUri)) {
                    if ($value['page_title']) {
                        $pageHeaderInfo['title'] = $value['page_title'];
                    }
                    if ($value['page_description']) {
                        $pageHeaderInfo['description'] = $value['page_description'];
                    }
                    if ($value['page_keywords']) {
                        $pageHeaderInfo['keywords'] = $value['page_keywords'];
                    }
                    if ($value['artwork_url']) {
                        $pageHeaderInfo['artwork_url'] = $value['artwork_url'];
                    }
                }
            }
        }

        if (isset($pageHeaderInfo['title'])) {
            $data = [];

            isset($item->title) && $data = array_merge($data, ['title' => $item->title]);
            // isset($item->artists) && $data = array_merge($data, ['artist' => implode(',',$item->artists->map(function ($row){
            //     return $row->name;
            // })->toArray())));
            isset($item->artists) && $data = array_merge($data, ['artist' => implode(',', array_map(function ($row) {
                return $row['name'];
            }, $item->artists->toArray()))]);

            isset($item->user->name) && $data = array_merge($data, ['user' => $item->user->name]);
            isset($item->name) && $data = array_merge($data, ['name' => $item->name]);
            isset($item->term) && $data = array_merge($data, ['term' => $item->term]);

            $pageHeaderInfo['title'] = self::metaParse($data, $pageHeaderInfo['title']);
            isset($pageHeaderInfo['description']) && $pageHeaderInfo['description'] = self::metaParse($data, $pageHeaderInfo['description']);

            MetaTag::set('title', $pageHeaderInfo['title']);
            isset($pageHeaderInfo['description']) && MetaTag::set('description', $pageHeaderInfo['description']);
            isset($pageHeaderInfo['keywords']) ? MetaTag::set('keywords', $pageHeaderInfo['keywords']) : MetaTag::set('keywords', self::keywordGenerator($pageHeaderInfo['description'] ?? $pageHeaderInfo['title']));

            if (isset($item->artwork_url)) {
                MetaTag::set('image', url($item->artwork_url));
            } elseif (isset($pageHeaderInfo['artwork_url'])) {
                MetaTag::set('image', url($pageHeaderInfo['artwork_url']));
            }
        }
    }

    public static function metaParse($data, $content): array|string|null
    {
        return preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($data) {
            [$shortCode, $index] = $matches;

            if (isset($data[$index])) {
                return $data[$index];
            } else {
                /*
                 * for testing only
                 */
                // throw new Exception("Shortcode {$shortCode} not found in template id {$this->id}", 1);
            }
        }, $content);
    }

    public static function parseJsonArray($jsonArray, $parentID = 0): array
    {
        $return = [];

        foreach ($jsonArray as $subArray) {
            $returnSubSubArray = [];

            if (isset($subArray['children'])) {
                $returnSubSubArray = self::parseJsonArray($subArray['children'], $subArray['id']);
            }

            $return[] = [
                'id' => $subArray['id'],
                'parent_id' => $parentID,
            ];

            $return = array_merge($return, $returnSubSubArray);
        }

        return $return;
    }

    public static function keywordGenerator($content): string
    {
        $content = strip_tags($content);
        $keywordCount = 20;
        $newarr = [];

        $quotes = ["\x22", "\x60", "\t", "\n", "\r", ',', '.', '/', '¬', '#', ';', ':', '@', '~', '[', ']', '{', '}', '=', '-', '+', ')', '(', '*', '^', '%', '$', '<', '>', '?', '!', '"'];
        $fastquotes = ["\x22", "\x60", "\t", "\n", "\r", '"', '\\', '\r', '\n', '/', '{', '}', '[', ']'];

        $content = str_replace('&nbsp;', ' ', $content);
        $content = str_replace('<br />', ' ', $content);
        $content = preg_replace('#&(.+?);#', '', $content);
        $content = trim(str_replace(' ,', '', stripslashes($content)));

        $content = str_replace($fastquotes, '', $content);
        $content = str_replace($quotes, ' ', $content);

        $arr = explode(' ', $content);

        foreach ($arr as $word) {
            if (iconv_strlen($word, 'utf-8') > 4) {
                $newarr[] = $word;
            }
        }

        $arr = array_count_values($newarr);
        arsort($arr);

        $arr = array_keys($arr);

        $offset = 0;

        $arr = array_slice($arr, $offset, $keywordCount);

        return implode(', ', $arr);
    }

    public static function handleSpotifySong($item, $artists, $artwork_url = null): stdClass
    {
        $artistIds = [];

        foreach ($artists as $artist) {
            $artistIds[] = $artist->id;
        }

        $uniqueSongId = intval(crc32($item['id']) / 100);

        DB::table('songs')->insertOrIgnore([
            [
                'id' => $uniqueSongId,
                'title' => $item['name'],
                'artistIds' => implode(',', $artistIds),
                'duration' => intval($item['duration_ms'] / 1000),
                'is_explicit' => boolval($item['explicit']),
                'approved' => DefaultConstants::TRUE,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        DB::table('song_spotify_logs')->insertOrIgnore([
            [
                'spotify_id' => $item['id'],
                'song_id' => $uniqueSongId,
                'artwork_url' => $artwork_url ?: $item['album']['images'][1]['url'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        $song = new stdClass;
        $song->id = $uniqueSongId;
        $song->title = $item['name'];
        $song->artists = $artists;
        $song->duration = intval($item['duration_ms'] / 1000);
        $song->is_explicit = boolval($item['explicit']);
        $song->approved = DefaultConstants::TRUE;
        $song->artwork_url = $artwork_url ?: $item['album']['images'][1]['url'];
        $song->mp3 = 0;
        $song->hls = 0;
        $song->hd = 0;
        $song->loves = 0;
        $song->streamable = Group::getValue('option_stream') ? true : false;
        $song->collectors = 0;
        $song->plays = 0;
        $song->favorite = DefaultConstants::FALSE;
        $song->permalink = route('frontend.song.show', ['song' => $uniqueSongId, 'slug' => str_slug($item['name']) ? str_slug($item['name']) : str_replace(' ', '-', $item['name'])]);
        $song->artists = $artists;
        $song->stream_url = URL::temporarySignedRoute('frontend.song.stream.mp3', now()->addDay(), [
            'id' => $uniqueSongId,
        ]);

        return $song;
    }

    public static function handleSpotifyArtists($items): array
    {
        $artists = [];
        foreach ($items as $artistItem) {
            $uniqueArtistId = intval(crc32($artistItem['id']) / 100);

            DB::table('artists')->insertOrIgnore([
                [
                    'id' => $uniqueArtistId,
                    'name' => $artistItem['name'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]);

            DB::table('artist_spotify_logs')->insertOrIgnore([
                [
                    'spotify_id' => $artistItem['id'],
                    'artist_id' => $uniqueArtistId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]);

            $artist = new Artist;
            $artist->id = $uniqueArtistId;
            $artist->name = $artistItem['name'];

            if (env('QUEUE_CONNECTION') != 'sync') {
                // dispatch(new \App\Jobs\GetArtistDetails($artist));
            }

            $artists[] = $artist;
        }

        return $artists;
    }

    public static function handleSpotifyAlbum($artists, $item): stdClass
    {
        $artistIds = [];

        foreach ($artists as $artist) {
            $artistIds[] = $artist->id;
        }

        $uniqueAlbumId = intval(crc32($item['id']) / 100);

        DB::table('albums')->insertOrIgnore([
            [
                'id' => $uniqueAlbumId,
                'title' => $item['name'],
                'artistIds' => implode(',', $artistIds),
                'released_at' => Carbon::parse($item['release_date']),
                'approved' => DefaultConstants::TRUE,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        DB::table('album_spotify_logs')->insertOrIgnore([
            [
                'spotify_id' => $item['id'],
                'album_id' => $uniqueAlbumId,
                'artwork_url' => $item['images'][1]['url'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        if (env('QUEUE_CONNECTION') != 'sync') {
            // dispatch(new GetAlbumDetails($album));
        }

        $album = new stdClass;
        $album->id = $uniqueAlbumId;
        $album->title = $item['name'];
        $album->artists = $artists;
        $album->approved = DefaultConstants::TRUE;
        $album->artwork_url = $item['images'][1]['url'];
        $album->permalink = route('frontend.album.show', ['id' => $uniqueAlbumId, 'slug' => str_slug($item['name']) ? str_slug($item['name']) : str_replace(' ', '-', $item['name'])]);

        return $album;
    }
}
