<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Models\User;

class MenuService
{
    public static function getUserMenu(?User $user): array
    {
        if (!$user) {
            return [];
        }

        return auth()->id() !== $user->id
            ? self::getForeignMenu($user)
            : self::getOwnMenu();
    }

    public static function getSettingsMenu(?User $user): array
    {
        if (!$user) {
            return [];
        }

        $defaultMenu = [
            [
                'route' => route('settings.profile.edit'),
                'active' => request()->routeIs('settings.profile.edit'),
                'icon' => 'user',
                'key' => 'menus.user_settings.profile',
                'permissions' => null,
            ], [
                'route' => route('settings.account.edit'),
                'active' => request()->routeIs('settings.account.edit'),
                'icon' => 'user-pen',
                'key' => 'menus.user_settings.account',
                'permissions' => null,
            ], [
                'route' => route('settings.password.edit'),
                'active' => request()->routeIs('settings.password.edit'),
                'icon' => 'lock',
                'key' => 'menus.user_settings.password',
                'permissions' => null,
            ], [
                'route' => route('settings.connections.index'),
                'active' => request()->routeIs('settings.connections.*'),
                'icon' => 'circle-nodes',
                'key' => 'menus.user_settings.connect',
                'permissions' => null,
            ],
        ];

        return $user->hasRole(RoleEnum::Listener)
            ? $defaultMenu
            : [...$defaultMenu, [
                'route' => route('settings.subscription.edit'),
                'active' => request()->routeIs('settings.subscription.edit'),
                'icon' => 'wallet',
                'key' => 'menus.user_settings.subscription',
                'permissions' => null,
            ]];
    }

    private static function getOwnMenu(): array
    {
        $user = auth()->user();

        return $user->hasRole(RoleEnum::Listener)
            ? [
                [
                    'route' => route('users.show', $user->uuid),
                    'active' => request()->routeIs('users.show'),
                    'icon' => 'list',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.overview',
                    'permissions' => null,
                    'stats' => '',
                ], [
                    'route' => route('users.favorites.index', $user->uuid),
                    'active' => request()->routeIs('users.favorites'),
                    'icon' => 'star',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.favorites',
                    'permissions' => null,
                    'stats' => null,
                ], [
                    'route' => route('users.notifications', $user->uuid),
                    'active' => request()->routeIs('users.notifications'),
                    'icon' => 'bell',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.notifications',
                    'permissions' => null,
                    'stats' => null,
                ], [
                    'route' => route('users.purchased', $user->uuid),
                    'active' => request()->routeIs('users.purchased'),
                    'icon' => 'wallet',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.purchased',
                    'permissions' => null,
                    'stats' => $user->purchased_count,
                ], [
                    'route' => route('users.playlists', $user->uuid),
                    'active' => request()->routeIs('users.playlists') || request()->routeIs('playlists.*'),
                    'icon' => 'play',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.playlists',
                    'permissions' => null,
                    'stats' => $user->playlists()->count(),
                ], [
                    'route' => route('users.followers.show', $user->uuid),
                    'active' => request()->routeIs('users.followers.show'),
                    'icon' => 'users',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.followers',
                    'permissions' => null,
                    'stats' => $user->followers()->count() + $user->patrons()->count(),
                ], [
                    'route' => route('users.following.index', $user->uuid),
                    'active' => request()->routeIs('users.following.index'),
                    'icon' => 'person-walking-arrow-right',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.following',
                    'permissions' => null,
                    'stats' => $user->following()->count(),
                ],
            ]
            : [
                [
                    'route' => route('users.show', $user->uuid),
                    'active' => request()->routeIs('users.show'),
                    'icon' => 'list',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.overview',
                    'permissions' => null,
                    'stats' => '',
                ], [
                    'route' => route('users.favorites.index', $user->uuid),
                    'active' => request()->routeIs('users.favorites'),
                    'icon' => 'star',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.favorites',
                    'permissions' => null,
                    'stats' => null,
                ], [
                    'route' => route('users.notifications', $user->uuid),
                    'active' => request()->routeIs('users.notifications'),
                    'icon' => 'bell',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.notifications',
                    'permissions' => null,
                    'stats' => null,
                ], [
                    'route' => route('users.purchased', $user->uuid),
                    'active' => request()->routeIs('users.purchased'),
                    'icon' => 'wallet',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.purchased',
                    'permissions' => null,
                    'stats' => $user->purchased_count,
                ], [
                    'route' => route('users.tracks', $user->uuid),
                    'active' => request()->routeIs('users.tracks') || request()->routeIs('songs.show'),
                    'icon' => 'music',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.tracks',
                    'permissions' => null,
                    'stats' => $user->tracks()->count(),
                ], [
                    'route' => route('users.adventures', $user->uuid),
                    'active' => request()->routeIs('users.adventures') || request()->routeIs('adventures.*'),
                    'icon' => 'route',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.adventures',
                    'permissions' => null,
                    'stats' => $user->adventures()->heading()->count(),
                ], [
                    'route' => route('users.podcasts', $user->uuid),
                    'active' => request()->routeIs('users.podcasts') || request()->routeIs('podcasts.*'),
                    'icon' => 'podcast',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.podcasts',
                    'permissions' => null,
                    'stats' => $user->podcasts()->count(),
                ], [
                    'route' => route('users.playlists', $user->uuid),
                    'active' => request()->routeIs('users.playlists') || request()->routeIs('playlists.*'),
                    'icon' => 'play',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.playlists',
                    'permissions' => null,
                    'stats' => $user->playlists()->count(),
                ], [
                    'route' => route('users.followers.show', $user->uuid),
                    'active' => request()->routeIs('users.followers.show'),
                    'icon' => 'users',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.followers',
                    'permissions' => null,
                    'stats' => $user->freeFollowers()->count() + $user->patrons()->count(),
                ], [
                    'route' => route('users.following.index', $user->uuid),
                    'active' => request()->routeIs('users.following.index'),
                    'icon' => 'person-walking-arrow-right',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.following',
                    'permissions' => null,
                    'stats' => $user->following()->count(),
                ],
            ];
    }

    private static function getForeignMenu(User $user): array
    {
        return $user->hasRole(RoleEnum::Listener)
            ? [
                [
                    'route' => route('users.show', $user->uuid),
                    'active' => request()->routeIs('users.show'),
                    'icon' => 'list',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.overview',
                    'permissions' => null,
                    'stats' => null,
                ], [
                    'route' => route('users.playlists', $user->uuid),
                    'active' => request()->routeIs('users.playlists') || request()->routeIs('playlists.*'),
                    'icon' => 'play',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.playlists',
                    'permissions' => null,
                    'stats' => $user->playlists()->visible()->count(),
                ], ]
            : [
                [
                    'route' => route('users.show', $user->uuid),
                    'active' => request()->routeIs('users.show'),
                    'icon' => 'list',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.overview',
                    'permissions' => null,
                    'stats' => null,
                ], [
                    'route' => route('users.tracks', $user->uuid),
                    'active' => request()->routeIs('users.tracks') || request()->routeIs('songs.show'),
                    'icon' => 'music',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.tracks',
                    'permissions' => null,
                    'stats' => $user->tracks()->visible()->count(),
                ], [
                    'route' => route('users.adventures', $user->uuid),
                    'active' => request()->routeIs('users.adventures'),
                    'icon' => 'route',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.adventures',
                    'permissions' => null,
                    'stats' => $user->adventures()->heading()->visible()->count(),
                ], [
                    'route' => route('users.podcasts', $user->uuid),
                    'active' => request()->routeIs('users.podcasts'),
                    'icon' => 'podcast',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.podcasts',
                    'permissions' => null,
                    'stats' => $user->podcasts()->visible()->count(),
                ], [
                    'route' => route('users.playlists', $user->uuid),
                    'active' => request()->routeIs('users.playlists') || request()->routeIs('playlists.*'),
                    'icon' => 'play',
                    //                'icon' => 'element-11',
                    'key' => 'menus.user_menu.playlists',
                    'permissions' => null,
                    'stats' => $user->playlists()->visible()->count(),
                ], ];
    }
}
