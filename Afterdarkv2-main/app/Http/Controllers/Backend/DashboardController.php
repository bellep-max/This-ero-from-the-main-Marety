<?php

namespace App\Http\Controllers\Backend;

use App\Constants\StatusConstants;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Comment;
use App\Models\Order;
use App\Models\Playlist;
use App\Models\Post;
use App\Models\Service;
use App\Models\Song;
use App\Models\MESubscription;
use App\Models\User;
use App\Services\AudioService;
use App\Services\Backend\BackendService;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use NiNaCoder\Updater\UpdaterManager;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;
use stdClass;

class DashboardController
{
    public function index(Request $request): View|Application|Factory
    {
        $dashboard = (object) [];
        $dashboard->total_songs = Song::query()->withoutGlobalScopes()->count();
        $dashboard->total_artists = Artist::query()->withoutGlobalScopes()->count();
        $dashboard->total_albums = Album::query()->withoutGlobalScopes()->count();
        $dashboard->total_playlists = Playlist::query()->withoutGlobalScopes()->count();

        $maxFileSize = (@ini_get('post_max_size') != '') ? @ini_get('post_max_size') : 'Unknown';
        $safeMode = (@ini_get('safe_mode') == 1) ? '<span class="text-danger">Safe mode IS <strong>ON!</strong>  We required off, please set <strong>safe mode</strong> to <strong>off</strong></span>' : '<span class="text-success">Safe mode IS <strong>OFF!</strong></span>';
        $ffmpeg = AudioService::checkIfFFMPEGInstalled() ? '<span class="text-success"><strong>Supported</strong></span>' : '<span class="text-danger"><strong>Not supported</strong></span>';

        $maxMemory = (@ini_get('memory_limit') != '') ? @ini_get('memory_limit') : 'Unknown';

        if (function_exists('gd_info')) {
            $array = gd_info();
            $gdVersion = '';

            foreach ($array as $key => $val) {
                $val = $val === true
                    ? 'Enabled'
                    : 'Disabled';

                $gdVersion .= $key . ":&nbsp;$val, ";
            }
        } else {
            $gdVersion = 'Undefined';
        }

        $mysqlVersion = DB::scalar('select version()');

        if (intval($maxFileSize) < 20) {
            $maxFileSize = "<span class=\"text-danger\">$maxFileSize. For best performance please set it to 20MB or higher</span>";
        } else {
            $maxFileSize = " <span class=\"text-success\">$maxFileSize</span>";
        }

        $dashboard->information = '<tr><td>Music Engine Version</td><td><strong>' . env('APP_VERSION') . "</strong></td></tr>\n";
        $dashboard->information .= '<tr><td>PHP Version </td><td>' . PHP_VERSION . "</td></tr>\n";
        $dashboard->information .= "<tr><td>MYSQL Version </td><td>$mysqlVersion</td></tr>\n";
        $dashboard->information .= "<tr><td>FFMPEG</td><td>$ffmpeg</td></tr>\n";
        $dashboard->information .= "<tr><td>Post Max Size</td><td><strong>$maxFileSize</strong>. The maximum upload file size.</td></tr>\n";
        $dashboard->information .= "<tr><td>Max Memory Allow</td><td>$maxMemory</td></tr>\n";
        $dashboard->information .= "<tr><td>Safemode</td><td>$safeMode</td></tr>\n";
        $dashboard->information .= '<tr><td>Server Time</td><td>' . date('D, F j, Y H:i:s', time()) . "</td></tr>\n";
        $dashboard->information .= '<tr><td>Server IP</td><td>' . getenv('REMOTE_ADDR') . "</td></tr>\n";
        $dashboard->information .= '<tr><td>Browser</td><td>' . getenv('HTTP_USER_AGENT') . "</td></tr>\n";
        $dashboard->information .= "<tr><td>Information About GD:</td><td>$gdVersion</td></tr>\n";
        $dashboard->information .= '<tr><td>Request URI</td><td>' . getenv('REQUEST_URI') . "</td></tr>\n";
        $dashboard->information .= '<tr><td>Referer</td><td>' . getenv('HTTP_REFERER') . "</td></tr>\n";
        $dashboard->information .= '<tr><td>RAM Allocated </td><td>' . BackendService::fileSizeConverter(@memory_get_usage(true)) . "</td></tr>\n";
        $dashboard->information .= '<tr><td>OS</td><td>' . PHP_OS . "</td></tr>\n";
        $dashboard->information .= '<tr><td>Server</td><td>' . getenv('SERVER_SOFTWARE') . "</td></tr>\n";
        $dashboard->information .= '<tr><td>Server Name</td><td>' . getenv('SERVER_NAME') . "</td></tr>\n";
        $dashboard->information .= '<tr><td>Upload Max File Size:</td><td>' . BackendService::fileSizeConverter(str_replace(['M', 'm'], '', intval(@ini_get('upload_max_filesize'))) * 1024 * 1024) . "</td></tr>\n";
        $dashboard->information .= '<tr><td>Available disk space:</td><td>' . BackendService::fileSizeConverter(@disk_free_space('.')) . "</td></tr>\n";

        $dashboard->server = new stdClass;
        $dashboard->server->post_max_size = (@ini_get('post_max_size') != '') ? intval(@ini_get('post_max_size')) : 'Unknown';
        $dashboard->server->upload_max_filesize = intval(str_replace(['M', 'm'], '', intval(@ini_get('upload_max_filesize'))));
        $dashboard->server->disk_free_space = BackendService::fileSizeConverter(@disk_free_space('.'));
        $dashboard->server->memory_limit = intval($maxMemory);
        $dashboard->server->max_execution_time = @ini_get('max_execution_time');
        $dashboard->server->max_input_time = @ini_get('max_input_time');

        /*
         * Get site statistics
         */

        $dashboard->statistics = new stdClass;
        $dashboard->statistics->system_status = config('settings.site_offline') ? '<span class="badge badge-danger">Off</span>' : '<span class="badge badge-success">On</span>';
        $dashboard->statistics->site_url = env('APP_URL');
        $dashboard->statistics->total_posts = Post::query()->count();
        $dashboard->statistics->awaiting_posts = Post::query()->notApproved()->count();
        $dashboard->statistics->total_comments = Comment::query()->count();
        $dashboard->statistics->awaiting_comments = Comment::query()->notApproved()->count();
        $dashboard->statistics->total_users = User::query()->count();
        $dashboard->statistics->banned_users = User::query()->has('ban')->count();
        $dashboard->statistics->cache_path = storage_path('framework/cache');
        $dashboard->statistics->total_artists = Artist::query()->count();
        $dashboard->statistics->awaiting_artists = Artist::query()->count();
        $dashboard->statistics->total_subscriptions = MESubscription::query()->count();

        /* Get recent users */

        $dashboard->recentUsers = User::limit(5)->latest()->get();
        $dashboard->recentPosts = Post::limit(5)->latest()->get();
        $dashboard->subscriptions = MESubscription::limit(8)->get();

        /** Get orders charts */
        $from = now()->subDays(15);

        $subscriptionsData = MESubscription::query()
            ->select(DB::raw('sum(amount) AS earnings'), DB::raw('DATE(created_at) as date'))
            ->whereNowOrPast('created_at')
            ->whereDate('created_at', '>=', $from)
            ->whereIn('status', StatusConstants::getAll())
            ->groupBy('date')
            ->get()
            ->toArray();

        $rows = BackendService::insertMissingData($subscriptionsData, ['earnings'], $from, now());
        $dashboard->orders_data = new stdClass;

        foreach ($rows as $item) {
            $item = (array) $item;
            $dashboard->orders_data->earnings[] = $item['earnings'];
            $dashboard->orders_data->period[] = Carbon::parse($item['date'])->format('F j');
        }

        /* get plan data */
        $dashboard->plans = Service::query()
            ->select('id', 'title')
            ->get();

        // If google analytic is enabled

        if (config('settings.google_analytics')) {
            if (Cache::has('analytics')) {
                $dashboard->analytics = Cache::get('analytics');
            } else {
                $dashboard->analytics = new stdClass;

                $dashboard->analytics->streamsPerCountry = Analytics::performQuery(
                    Period::months(1),
                    'ga:sessions',
                    [
                        'metrics' => 'ga:sessions',
                        'dimensions' => 'ga:country',
                        'sort' => '-ga:sessions',
                        'max-results' => '10',

                    ]
                );

                $dashboard->analytics->countryIsoCode = Analytics::performQuery(
                    Period::months(1),
                    'ga:sessions',
                    [
                        'metrics' => 'ga:sessions',
                        'dimensions' => 'ga:countryIsoCode',

                    ]
                );

                $visitorByCountryIsoCode = [];

                foreach ($dashboard->analytics->countryIsoCode->rows as $row) {
                    $visitorByCountryIsoCode[] = [
                        $row[0] => intval($row[1]),
                    ];
                }

                $visitorByCountryIsoCode = json_encode($visitorByCountryIsoCode);
                $visitorByCountryIsoCode = str_replace('[{', '{', $visitorByCountryIsoCode);
                $visitorByCountryIsoCode = str_replace('}]', '}', $visitorByCountryIsoCode);
                $visitorByCountryIsoCode = str_replace('}]', '}', $visitorByCountryIsoCode);
                $visitorByCountryIsoCode = str_replace('{"', '"', $visitorByCountryIsoCode);
                $visitorByCountryIsoCode = str_replace('},"', ',"', $visitorByCountryIsoCode);
                $visitorByCountryIsoCode = '{' . $visitorByCountryIsoCode;

                $dashboard->analytics->visitorByCountryIsoCode = $visitorByCountryIsoCode;

                $dashboard->analytics->userAgeBracket = Analytics::performQuery(
                    Period::months(1),
                    'ga:sessions',
                    [
                        'metrics' => 'ga:sessions',
                        'dimensions' => 'ga:userAgeBracket,ga:userGender',

                    ]
                );

                $dashboard->analytics->topBrowsers = Analytics::performQuery(
                    Period::months(1),
                    'ga:sessions',
                    [
                        'metrics' => 'ga:sessions',
                        'dimensions' => 'ga:browser',
                        'sort' => '-ga:sessions',
                        'max-results' => '5',
                    ]
                );

                $dashboard->analytics->userType = Analytics::performQuery(
                    Period::months(1),
                    'ga:sessions',
                    [
                        'metrics' => 'ga:sessions',
                        'dimensions' => 'ga:userType',
                        'sort' => '-ga:sessions',
                        'max-results' => '5',
                    ]
                );

                Cache::put('analytics', $dashboard->analytics, now()->addDay());
            }
        }

        $stats = Order::query()
            ->select(DB::raw('sum(amount) AS revenue'), DB::raw('sum(commission) AS commission'))
            ->first();

        $stats->album = Order::query()
            ->select(DB::raw('count(*) AS count'), DB::raw('sum(amount) AS revenue'))
            ->where('orderable_type', Album::class)
            ->first();

        $stats->song = Order::query()
            ->select(DB::raw('count(*) AS count'), DB::raw('sum(amount) AS revenue'))
            ->where('orderable_type', Song::class)
            ->first();

        return view('backend.dashboard.dashboard')
            ->with([
                'dashboard' => $dashboard,
                'stats' => $stats,
            ]);
    }

    public function checkForUpdate(Request $request, UpdaterManager $updater)
    {
        //        if ($updater->source()->isNewVersionAvailable()) {
        //            return response()->json([
        //                'success' => true,
        //                'new_version' => $updater->source()->getVersionAvailable()
        //            ]);
        //        } else {
        //            return response()->json([
        //                'success' => false
        //            ], 404);
        //        }
    }
}
