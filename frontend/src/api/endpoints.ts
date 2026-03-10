export const AUTH = {
    LOGIN: '/auth/login',
    LOGOUT: '/auth/logout',
    REGISTER: '/auth/register',
    USER: '/auth/user',
};

export const INIT = '/init';

export const HOMEPAGE = '/homepage';

export const DISCOVER = '/discover';

export const TRENDING = {
    INDEX: '/trending',
    TOP_BY_GENRE: '/trending/top-by-genre',
    TOP_SONGS: '/trending/top-songs',
    TOP_VOICE: '/trending/top-voice',
};

export const SEARCH = '/search';

export const SONGS = {
    SHOW: (uuid: string) => `/songs/${uuid}`,
    UPDATE: (uuid: string) => `/songs/${uuid}`,
    DELETE: (uuid: string) => `/songs/${uuid}`,
};

export const ADVENTURES = {
    INDEX: '/adventures',
    SHOW: (uuid: string) => `/adventures/${uuid}`,
    UPDATE: (uuid: string) => `/adventures/${uuid}`,
    DELETE: (uuid: string) => `/adventures/${uuid}`,
};

export const GENRES = {
    INDEX: '/genres',
    SHOW: (slug: string) => `/genres/${slug}`,
};

export const CHANNELS = {
    SHOW: (slug: string) => `/channels/${slug}`,
};

export const BLOG = {
    INDEX: '/blog',
    SHOW: (slug: string) => `/blog/${slug}`,
};

export const STREAM = {
    MP3: (uuid: string) => `/stream/mp3/${uuid}`,
    HLS: (uuid: string) => `/stream/hls/${uuid}`,
    YOUTUBE: (uuid: string) => `/stream/youtube/${uuid}`,
    ON_TRACK_PLAYED: '/stream/on-track-played',
};

export const USER_PROFILE = {
    SHOW: (username: string) => `/users/${username}`,
    TRACKS: (username: string) => `/users/${username}/tracks`,
    ADVENTURES: (username: string) => `/users/${username}/adventures`,
    PLAYLISTS: (username: string) => `/users/${username}/playlists`,
    PODCASTS: (username: string) => `/users/${username}/podcasts`,
    NOTIFICATIONS: '/user/notifications',
    PURCHASED: (username: string) => `/users/${username}/purchased`,
    FOLLOWERS: (username: string) => `/users/${username}/followers`,
    FOLLOWING: (username: string) => `/users/${username}/following`,
    FAVORITES: (username: string) => `/users/${username}/favorites`,
};

export const PLAYLISTS = {
    INDEX: '/playlists',
    SHOW: (uuid: string) => `/playlists/${uuid}`,
    STORE: '/playlists',
    UPDATE: (uuid: string) => `/playlists/${uuid}`,
    DELETE: (uuid: string) => `/playlists/${uuid}`,
    SONGS: (uuid: string) => `/playlists/${uuid}/songs`,
    COLLABORATORS: (uuid: string) => `/playlists/${uuid}/collaborators`,
};

export const PODCASTS = {
    INDEX: '/podcasts',
    SHOW: (uuid: string) => `/podcasts/${uuid}`,
    STORE: '/podcasts',
    UPDATE: (uuid: string) => `/podcasts/${uuid}`,
    DELETE: (uuid: string) => `/podcasts/${uuid}`,
    EPISODES: (uuid: string) => `/podcasts/${uuid}/episodes`,
    SEASONS: (uuid: string) => `/podcasts/${uuid}/seasons`,
};

export const COMMENTS = {
    STORE: '/comments',
};

export const FOLLOWERS_API = {
    STORE: '/followers',
    DELETE: (id: number) => `/followers/${id}`,
};

export const FOLLOWING_API = {
    INDEX: '/following',
    STORE: '/following',
    DELETE: (id: number) => `/following/${id}`,
};

export const FAVORITES = {
    INDEX: '/favorites',
    STORE: '/favorites',
    DELETE: (id: number) => `/favorites/${id}`,
};

export const NOTIFICATIONS = {
    MARK_READ: '/notifications/mark-read',
};

export const REPORTS = {
    STORE: '/reports',
};

export const ACTIONS = {
    STORE: '/actions',
};

export const UPLOAD = {
    TRACKS: '/upload/tracks',
    ADVENTURES: '/upload/adventures',
    EPISODES: '/upload/episodes',
};

export const SETTINGS = {
    PROFILE: '/settings/profile',
    ACCOUNT: '/settings/account',
    PASSWORD: '/settings/password',
    PREFERENCES: '/settings/preferences',
    SUBSCRIPTION: '/settings/subscription',
    CONNECTED_SERVICES: '/settings/connected-services',
};

export const TERMS = {
    ACCEPT: '/terms/accept',
};

export const DOWNLOADS = {
    DOWNLOAD: (uuid: string) => `/download/${uuid}`,
    DOWNLOAD_HD: (uuid: string) => `/download-hd/${uuid}`,
};

export const SHARE = {
    EMBED: (uuid: string) => `/share/embed/${uuid}`,
};

export const SUBSCRIPTIONS = {
    CHECKOUT: '/subscription/checkout',
    SUCCESS: '/subscription/success',
    CANCEL: '/subscription/cancel',
    SUSPEND: '/subscription/suspend',
    ACTIVATE: '/subscription/activate',
};

export const LINKTREE = {
    UPDATE: '/linktree',
    DELETE: (id: number) => `/linktree/${id}`,
};
