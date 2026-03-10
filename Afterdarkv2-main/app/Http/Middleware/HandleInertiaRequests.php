<?php

namespace App\Http\Middleware;

use App\Http\Resources\OptionResource;
use App\Http\Resources\TagResource;
use App\Http\Resources\User\MyProfileResource;
use App\Http\Resources\VocalOptionResource;
use App\Models\Country;
use App\Models\Genre;
use App\Models\Language;
use App\Models\PodcastCategory;
use App\Models\Tag;
use App\Models\User;
use App\Models\Vocal;
use App\Services\MenuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        /** @var User $user */
        $user = Auth::user();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'is_adult' => session()->has('is_adult'),
                'user' => $user ? MyProfileResource::make($user->loadMissing([
                    'playlists',
                    'podcasts',
                    'approvedCollaboratedPlaylists',
                    'group',
                    'unreadNotifications' => function ($query) {
                        $query->with([
                            'notificationable',
                            'hostable',
                        ])
                            ->orderBy('read_at', 'asc')
                            ->orderBy('created_at', 'desc');
                    },
                ])
                ) : null,
                'pageMenu' => MenuService::getSettingsMenu($user),
                'userMenu' => MenuService::getUserMenu($user),
            ],
            'menu' => $this->getMainMenu(),
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'flash_message' => fn () => $request->session()->get('message'),
            'filter_presets' => $user ?
                [
                    'tags' => TagResource::collection(
                        Tag::query()
                            ->has('posts')
                            ->orHas('songs')
                            ->orHas('podcasts')
                            ->orHas('adventures')
                            ->get()
                    ),
                    'genres' => OptionResource::collection(Genre::all()),
                    'vocals' => VocalOptionResource::collection(Vocal::all()),
                    'podcast_categories' => OptionResource::collection(PodcastCategory::all()),
                    'languages' => OptionResource::collection(Language::all()),
                    'countries' => OptionResource::collection(Country::all()),
                ] : [],
            'version' => $this->version($request),
        ];
    }

    protected function getMainMenu(): array
    {
        return [
            [
                'route' => route('discover.index'),
                'active' => request()->routeIs('discover.index'),
                //                'icon' => 'element-11',
                'key' => 'menus.header.discover',
                'permissions' => null,
            ], [
                'route' => route('genres.index'),
                'active' => request()->routeIs('genres.*'),
                //                'icon' => 'element-11',
                'key' => 'menus.header.genres',
                'permissions' => null,
            ], [
                'route' => route('podcasts.index'),
                'active' => request()->routeIs('podcasts.*'),
                //                'icon' => 'element-11',
                'key' => 'menus.header.podcasts',
                'permissions' => null,
            ], [
                'route' => route('posts.index'),
                'active' => request()->routeIs('posts.*'),
                //                'icon' => 'element-11',
                'key' => 'menus.header.blog',
                'permissions' => null,
            ],
            //            [
            //                'route' => route('trending.index'),
            //                'active' => request()->routeIs('trending.index'),
            //                //                'icon' => 'element-11',
            //                'key' => 'menus.header.trending',
            //                'permissions' => null,
            //            ],
        ];
    }
}
