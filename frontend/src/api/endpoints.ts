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
    TOP_BY_GENRE: (genre: string) => `/trending/genre/${genre}`,
    TOP_SONGS: '/trending/songs',
    TOP_VOICE: (voice: string) => `/trending/voice/${voice}`,
};

export const SEARCH = '/search';

export const SONGS = {
    SHOW: (uuid: string) => `/songs/${uuid}`,
    EDIT: (uuid: string) => `/songs/${uuid}/edit`,
    UPDATE: (uuid: string) => `/songs/${uuid}`,
    DELETE: (uuid: string) => `/songs/${uuid}`,
};

export const ADVENTURES = {
    INDEX: '/adventures',
    SHOW: (uuid: string) => `/adventures/${uuid}`,
    EDIT: (uuid: string) => `/adventures/${uuid}/edit`,
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
    MP3: (uuid: string, type: string) => `/stream/mp3/${uuid}/${type}`,
    HLS: (uuid: string, type: string) => `/stream/hls/${uuid}/${type}`,
    YOUTUBE: (uuid: string) => `/stream/youtube/${uuid}`,
    ON_TRACK_PLAYED: '/stream/on-track-played',
};

export const USER_PROFILE = {
    SHOW: (username: string) => `/users/${username}`,
    UPDATE: '/profile',
    TRACKS: (username: string) => `/users/${username}/tracks`,
    ADVENTURES: (username: string) => `/users/${username}/adventures`,
    PLAYLISTS: (username: string) => `/users/${username}/playlists`,
    PODCASTS: (username: string) => `/users/${username}/podcasts`,
    NOTIFICATIONS: (username: string) => `/users/${username}/notifications`,
    PURCHASED: (username: string) => `/users/${username}/purchased`,
    FOLLOWERS: (username: string) => `/users/${username}/followers`,
    FOLLOWING: (username: string) => `/users/${username}/following`,
    FAVORITES: (username: string) => `/users/${username}/favorites`,
    FAVORITES_STORE: (username: string) => `/users/${username}/favorites`,
    FAVORITES_DESTROY: (username: string) => `/users/${username}/favorites`,
    SUBSCRIPTIONS: {
        CHECKOUT: (username: string) => `/users/${username}/subscriptions/checkout`,
        SUCCESS: (username: string) => `/users/${username}/subscriptions/success`,
        CANCEL: (username: string) => `/users/${username}/subscriptions/cancel`,
        SUSPEND: (username: string) => `/users/${username}/subscriptions/suspend`,
        ACTIVATE: (username: string) => `/users/${username}/subscriptions/activate`,
    },
};

export const PLAYLISTS = {
    INDEX: '/playlists',
    STORE: '/playlists',
    SHOW: (uuid: string) => `/playlists/${uuid}`,
    EDIT: (uuid: string) => `/playlists/${uuid}/edit`,
    UPDATE: (uuid: string) => `/playlists/${uuid}`,
    DELETE: (uuid: string) => `/playlists/${uuid}`,
    ADD_SONG: (uuid: string) => `/playlists/${uuid}/songs`,
    REMOVE_SONG: (uuid: string, songUuid: string) => `/playlists/${uuid}/songs/${songUuid}`,
    COLLAB: (uuid: string, userUuid: string) => `/playlists/${uuid}/collab/${userUuid}`,
    INVITE_COLLABORATOR: (uuid: string) => `/playlists/${uuid}/collaborators`,
    RESPOND_COLLABORATION: (uuid: string, userUuid: string) => `/playlists/${uuid}/collaborators/${userUuid}/respond`,
};

export const PODCASTS = {
    INDEX: '/podcasts',
    STORE: '/podcasts',
    SHOW: (uuid: string) => `/podcasts/${uuid}`,
    EDIT: (uuid: string) => `/podcasts/${uuid}/edit`,
    UPDATE: (uuid: string) => `/podcasts/${uuid}`,
    DELETE: (uuid: string) => `/podcasts/${uuid}`,
    EPISODES: (uuid: string) => `/podcasts/${uuid}/episodes`,
    SEASONS: {
        SHOW: (podcastUuid: string, seasonId: string) => `/podcasts/${podcastUuid}/seasons/${seasonId}`,
    },
};

export const EPISODES = {
    SHOW: (uuid: string) => `/episodes/${uuid}`,
    EDIT: (uuid: string) => `/episodes/${uuid}/edit`,
    UPDATE: (uuid: string) => `/episodes/${uuid}`,
};

export const COMMENTS = {
    STORE: '/comments',
};

export const FOLLOWERS_API = {
    STORE: '/followers',
    DELETE: '/followers',
};

export const FOLLOWING_API = {
    INDEX: '/following',
    STORE: '/following',
    DELETE: '/following',
};

export const FAVORITES = {
    INDEX: '/favorites',
    STORE: '/favorites',
    DELETE: (id: number) => `/favorites/${id}`,
};

export const NOTIFICATIONS = {
    MARK_READ: (notificationId: string) => `/notifications/${notificationId}/read`,
};

export const REPORTS = {
    STORE: '/reports',
};

export const ACTIONS = {
    STORE: '/actions',
};

export const UPLOAD = {
    INDEX: '/upload',
    TRACKS: '/upload/tracks',
    ADVENTURES_HEADING: '/upload/adventures/heading',
    ADVENTURES_ROOT: '/upload/adventures/root',
    ADVENTURES_DESTROY: (uuid: string) => `/upload/adventures/${uuid}`,
    EPISODES: '/upload/episodes',
};

export const SETTINGS = {
    PROFILE: '/settings/profile',
    PROFILE_UPDATE: '/settings/profile',
    PROFILE_AVATAR: '/settings/profile/avatar',
    ACCOUNT: '/settings/account',
    ACCOUNT_UPDATE: '/settings/account',
    ACCOUNT_DELETE: '/settings/account',
    PASSWORD: '/settings/password',
    PASSWORD_UPDATE: '/settings/password',
    PREFERENCES: '/settings/preferences',
    PREFERENCES_UPDATE: '/settings/preferences',
    SUBSCRIPTION: '/settings/subscription',
    CONNECTED_SERVICES: '/settings/connected-services',
};

export const TERMS = {
    ACCEPT: '/terms/accept',
};

export const DOWNLOADS = {
    DOWNLOAD: (type: string, uuid: string) => `/download/${type}/${uuid}`,
    DOWNLOAD_HD: (type: string, uuid: string) => `/download-hd/${type}/${uuid}`,
};

export const SHARE = {
    EMBED: (type: string, uuid: string, theme?: string) =>
        theme ? `/share/embed/${type}/${uuid}/${theme}` : `/share/embed/${type}/${uuid}`,
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
    DELETE: '/linktree',
};
