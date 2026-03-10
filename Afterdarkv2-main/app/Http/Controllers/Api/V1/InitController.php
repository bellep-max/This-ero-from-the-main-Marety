<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\OptionResource;
use App\Http\Resources\TagResource;
use App\Http\Resources\User\MyProfileResource;
use App\Http\Resources\VocalOptionResource;
use App\Models\Country;
use App\Models\Genre;
use App\Models\Language;
use App\Models\PodcastCategory;
use App\Models\Tag;
use App\Models\Vocal;
use App\Services\MenuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InitController extends ApiController
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = Auth::user();

        $authData = [
            'is_adult' => session()->has('is_adult'),
            'user' => null,
            'pageMenu' => MenuService::getSettingsMenu($user),
            'userMenu' => MenuService::getUserMenu($user),
        ];

        if ($user) {
            $authData['user'] = MyProfileResource::make($user->loadMissing([
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
            ]));
        }

        $filterPresets = $user ? [
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
        ] : [];

        return $this->success([
            'name' => config('app.name'),
            'auth' => $authData,
            'menu' => $this->getMainMenu(),
            'filter_presets' => $filterPresets,
            'version' => config('app.version', '1.0.0'),
        ]);
    }

    private function getMainMenu(): array
    {
        return [
            [
                'path' => '/discover',
                'key' => 'menus.header.discover',
                'permissions' => null,
            ],
            [
                'path' => '/genres',
                'key' => 'menus.header.genres',
                'permissions' => null,
            ],
            [
                'path' => '/podcasts',
                'key' => 'menus.header.podcasts',
                'permissions' => null,
            ],
            [
                'path' => '/posts',
                'key' => 'menus.header.blog',
                'permissions' => null,
            ],
        ];
    }
}
