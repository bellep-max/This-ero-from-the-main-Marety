import React, { useEffect, Suspense, lazy } from 'react';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { ToastContainer } from 'react-toastify';
import { useAuthStore } from '@/stores/auth';
import { useAppStore } from '@/stores/app';
import AppLayout from '@/Layouts/AppLayout';

const Index = lazy(() => import('@/pages/Index'));
const Discover = lazy(() => import('@/pages/Discover'));
const Search = lazy(() => import('@/pages/Search'));
const Trending = lazy(() => import('@/pages/Trending'));
const Upload = lazy(() => import('@/pages/Upload'));
const StaticPage = lazy(() => import('@/pages/StaticPage'));
const AdventuresShow = lazy(() => import('@/pages/Adventures/Show'));
const AdventuresEdit = lazy(() => import('@/pages/Adventures/Edit'));
const BlogIndex = lazy(() => import('@/pages/Blog/Index'));
const BlogShow = lazy(() => import('@/pages/Blog/Show'));
const ChannelShow = lazy(() => import('@/pages/Channel/Show'));
const GenreIndex = lazy(() => import('@/pages/Genre/Index'));
const GenreShow = lazy(() => import('@/pages/Genre/Show'));
const PlaylistShow = lazy(() => import('@/pages/Playlist/Show'));
const PlaylistEdit = lazy(() => import('@/pages/Playlist/Edit'));
const PodcastIndex = lazy(() => import('@/pages/Podcast/Index'));
const PodcastShow = lazy(() => import('@/pages/Podcast/Show'));
const PodcastEdit = lazy(() => import('@/pages/Podcast/Edit'));
const PodcastEpisodeEdit = lazy(() => import('@/pages/Podcast/Episode/Edit'));
const PodcastSeasonShow = lazy(() => import('@/pages/Podcast/Season/Show'));
const SettingsProfile = lazy(() => import('@/pages/Settings/Profile'));
const SettingsAccount = lazy(() => import('@/pages/Settings/Account'));
const SettingsPassword = lazy(() => import('@/pages/Settings/Password'));
const SettingsPreferences = lazy(() => import('@/pages/Settings/Preferences'));
const SettingsSubscription = lazy(() => import('@/pages/Settings/Subscription'));
const SettingsConnectedServices = lazy(() => import('@/pages/Settings/ConnectedServices'));
const SongEdit = lazy(() => import('@/pages/Song/Edit'));
const TrackShow = lazy(() => import('@/pages/Track/Show'));
const UserShow = lazy(() => import('@/pages/User/Show'));
const UserTracks = lazy(() => import('@/pages/User/Tracks'));
const UserFavorites = lazy(() => import('@/pages/User/Favorites'));
const UserFollowers = lazy(() => import('@/pages/User/Followers'));
const UserFollowing = lazy(() => import('@/pages/User/Following'));
const UserNotifications = lazy(() => import('@/pages/User/Notifications'));
const UserPlaylists = lazy(() => import('@/pages/User/Playlists'));
const UserPodcasts = lazy(() => import('@/pages/User/Podcasts'));
const UserPurchased = lazy(() => import('@/pages/User/Purchased'));
const UserAdventures = lazy(() => import('@/pages/User/Adventures'));

const Spinner = () => (
    <div className="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
        <div className="spinner-border text-light" role="status">
            <span className="visually-hidden">Loading...</span>
        </div>
    </div>
);

export default function App() {
    const fetchUser = useAuthStore((s) => s.fetchUser);
    const fetchInitData = useAppStore((s) => s.fetchInitData);

    useEffect(() => {
        fetchUser();
        fetchInitData();
    }, []);

    return (
        <BrowserRouter>
            <AppLayout>
                <Suspense fallback={<Spinner />}>
                    <Routes>
                        <Route path="/" element={<Index />} />
                        <Route path="/discover" element={<Discover />} />
                        <Route path="/search" element={<Search />} />
                        <Route path="/trending" element={<Trending />} />
                        <Route path="/upload" element={<Upload />} />
                        <Route path="/page/:slug" element={<StaticPage />} />
                        <Route path="/adventures/:uuid" element={<AdventuresShow />} />
                        <Route path="/adventures/:uuid/edit" element={<AdventuresEdit />} />
                        <Route path="/blog" element={<BlogIndex />} />
                        <Route path="/blog/:slug" element={<BlogShow />} />
                        <Route path="/channel/:slug" element={<ChannelShow />} />
                        <Route path="/genres" element={<GenreIndex />} />
                        <Route path="/genre/:slug" element={<GenreShow />} />
                        <Route path="/playlist/:uuid" element={<PlaylistShow />} />
                        <Route path="/playlist/:uuid/edit" element={<PlaylistEdit />} />
                        <Route path="/podcasts" element={<PodcastIndex />} />
                        <Route path="/podcast/:uuid" element={<PodcastShow />} />
                        <Route path="/podcast/:uuid/edit" element={<PodcastEdit />} />
                        <Route path="/podcast/:uuid/episode/:episodeUuid/edit" element={<PodcastEpisodeEdit />} />
                        <Route path="/podcast/:uuid/season/:seasonUuid" element={<PodcastSeasonShow />} />
                        <Route path="/settings/profile" element={<SettingsProfile />} />
                        <Route path="/settings/account" element={<SettingsAccount />} />
                        <Route path="/settings/password" element={<SettingsPassword />} />
                        <Route path="/settings/preferences" element={<SettingsPreferences />} />
                        <Route path="/settings/subscription" element={<SettingsSubscription />} />
                        <Route path="/settings/connected-services" element={<SettingsConnectedServices />} />
                        <Route path="/song/:uuid/edit" element={<SongEdit />} />
                        <Route path="/track/:uuid" element={<TrackShow />} />
                        <Route path="/user/:username" element={<UserShow />} />
                        <Route path="/user/:username/tracks" element={<UserTracks />} />
                        <Route path="/user/:username/favorites" element={<UserFavorites />} />
                        <Route path="/user/:username/followers" element={<UserFollowers />} />
                        <Route path="/user/:username/following" element={<UserFollowing />} />
                        <Route path="/user/:username/notifications" element={<UserNotifications />} />
                        <Route path="/user/:username/playlists" element={<UserPlaylists />} />
                        <Route path="/user/:username/podcasts" element={<UserPodcasts />} />
                        <Route path="/user/:username/purchased" element={<UserPurchased />} />
                        <Route path="/user/:username/adventures" element={<UserAdventures />} />
                    </Routes>
                </Suspense>
            </AppLayout>
            <ToastContainer position="bottom-left" autoClose={3000} />
        </BrowserRouter>
    );
}
