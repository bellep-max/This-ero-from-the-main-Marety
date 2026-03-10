import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router';

const routes: RouteRecordRaw[] = [
    {
        path: '/',
        name: 'home',
        component: () => import('@/pages/Index.vue'),
    },
    {
        path: '/discover',
        name: 'discover',
        component: () => import('@/pages/Discover.vue'),
    },
    {
        path: '/search',
        name: 'search',
        component: () => import('@/pages/Search.vue'),
    },
    {
        path: '/trending',
        name: 'trending',
        component: () => import('@/pages/Trending.vue'),
    },
    {
        path: '/upload',
        name: 'upload',
        component: () => import('@/pages/Upload.vue'),
    },
    {
        path: '/page/:slug',
        name: 'static-page',
        component: () => import('@/pages/StaticPage.vue'),
    },
    {
        path: '/adventures/:uuid',
        name: 'adventures.show',
        component: () => import('@/pages/Adventures/Show.vue'),
    },
    {
        path: '/adventures/:uuid/edit',
        name: 'adventures.edit',
        component: () => import('@/pages/Adventures/Edit.vue'),
    },
    {
        path: '/blog',
        name: 'blog.index',
        component: () => import('@/pages/Blog/Index.vue'),
    },
    {
        path: '/blog/:slug',
        name: 'blog.show',
        component: () => import('@/pages/Blog/Show.vue'),
    },
    {
        path: '/channel/:slug',
        name: 'channel.show',
        component: () => import('@/pages/Channel/Show.vue'),
    },
    {
        path: '/genres',
        name: 'genre.index',
        component: () => import('@/pages/Genre/Index.vue'),
    },
    {
        path: '/genre/:slug',
        name: 'genre.show',
        component: () => import('@/pages/Genre/Show.vue'),
    },
    {
        path: '/playlist/:uuid',
        name: 'playlist.show',
        component: () => import('@/pages/Playlist/Show.vue'),
    },
    {
        path: '/playlist/:uuid/edit',
        name: 'playlist.edit',
        component: () => import('@/pages/Playlist/Edit.vue'),
    },
    {
        path: '/podcasts',
        name: 'podcast.index',
        component: () => import('@/pages/Podcast/Index.vue'),
    },
    {
        path: '/podcast/:uuid',
        name: 'podcast.show',
        component: () => import('@/pages/Podcast/Show.vue'),
    },
    {
        path: '/podcast/:uuid/edit',
        name: 'podcast.edit',
        component: () => import('@/pages/Podcast/Edit.vue'),
    },
    {
        path: '/podcast/:uuid/episode/:episodeUuid/edit',
        name: 'podcast.episode.edit',
        component: () => import('@/pages/Podcast/Episode/Edit.vue'),
    },
    {
        path: '/podcast/:uuid/season/:seasonUuid',
        name: 'podcast.season.show',
        component: () => import('@/pages/Podcast/Season/Show.vue'),
    },
    {
        path: '/settings/profile',
        name: 'settings.profile',
        component: () => import('@/pages/Settings/Profile.vue'),
    },
    {
        path: '/settings/account',
        name: 'settings.account',
        component: () => import('@/pages/Settings/Account.vue'),
    },
    {
        path: '/settings/password',
        name: 'settings.password',
        component: () => import('@/pages/Settings/Password.vue'),
    },
    {
        path: '/settings/preferences',
        name: 'settings.preferences',
        component: () => import('@/pages/Settings/Preferences.vue'),
    },
    {
        path: '/settings/subscription',
        name: 'settings.subscription',
        component: () => import('@/pages/Settings/Subscription.vue'),
    },
    {
        path: '/settings/connected-services',
        name: 'settings.connected-services',
        component: () => import('@/pages/Settings/ConnectedServices.vue'),
    },
    {
        path: '/song/:uuid/edit',
        name: 'song.edit',
        component: () => import('@/pages/Song/Edit.vue'),
    },
    {
        path: '/track/:uuid',
        name: 'track.show',
        component: () => import('@/pages/Track/Show.vue'),
    },
    {
        path: '/user/:username',
        name: 'user.show',
        component: () => import('@/pages/User/Show.vue'),
    },
    {
        path: '/user/:username/tracks',
        name: 'user.tracks',
        component: () => import('@/pages/User/Tracks.vue'),
    },
    {
        path: '/user/:username/favorites',
        name: 'user.favorites',
        component: () => import('@/pages/User/Favorites.vue'),
    },
    {
        path: '/user/:username/followers',
        name: 'user.followers',
        component: () => import('@/pages/User/Followers.vue'),
    },
    {
        path: '/user/:username/following',
        name: 'user.following',
        component: () => import('@/pages/User/Following.vue'),
    },
    {
        path: '/user/:username/notifications',
        name: 'user.notifications',
        component: () => import('@/pages/User/Notifications.vue'),
    },
    {
        path: '/user/:username/playlists',
        name: 'user.playlists',
        component: () => import('@/pages/User/Playlists.vue'),
    },
    {
        path: '/user/:username/podcasts',
        name: 'user.podcasts',
        component: () => import('@/pages/User/Podcasts.vue'),
    },
    {
        path: '/user/:username/purchased',
        name: 'user.purchased',
        component: () => import('@/pages/User/Purchased.vue'),
    },
    {
        path: '/user/:username/adventures',
        name: 'user.adventures',
        component: () => import('@/pages/User/Adventures.vue'),
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
