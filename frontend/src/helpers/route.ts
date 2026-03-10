const routeMap: Record<string, string | ((...args: any[]) => string)> = {
  'songs.show': (song: any) => `/track/${typeof song === 'object' ? (song.uuid || song.slug) : song}`,
  'songs.edit': (song: any) => `/song/${typeof song === 'object' ? (song.uuid || song.slug) : song}/edit`,
  'songs.update': (song: any) => `/api/v1/songs/${typeof song === 'object' ? (song.uuid || song.slug) : song}`,
  'songs.destroy': (song: any) => `/api/v1/songs/${typeof song === 'object' ? (song.uuid || song.slug) : song}`,

  'adventures.show': (adv: any) => `/adventures/${typeof adv === 'object' ? (adv.uuid || adv.slug) : adv}`,
  'adventures.edit': (adv: any) => `/adventures/${typeof adv === 'object' ? (adv.uuid || adv.slug) : adv}/edit`,
  'adventures.update': (adv: any) => `/api/v1/adventures/${typeof adv === 'object' ? (adv.uuid || adv.slug) : adv}`,
  'adventures.destroy': (adv: any) => `/api/v1/adventures/${typeof adv === 'object' ? (adv.uuid || adv.slug) : adv}`,

  'genres.index': '/genres',
  'genres.show': (genre: any) => `/genre/${typeof genre === 'object' ? genre.slug : genre}`,

  'channels.show': (channel: any) => `/channel/${typeof channel === 'object' ? (channel.slug || channel.uuid) : channel}`,
  'channel': (channel: any) => `/channel/${typeof channel === 'object' ? (channel.slug || channel.uuid) : channel}`,

  'users.show': (params: any) => `/user/${typeof params === 'object' ? (params.user || params.username) : params}`,
  'users.tracks': (params: any) => `/user/${typeof params === 'object' ? (params.user || params.username) : params}/tracks`,

  'playlists.show': (pl: any) => `/playlist/${typeof pl === 'object' ? (pl.uuid || pl.slug) : pl}`,
  'playlists.edit': (pl: any) => `/playlist/${typeof pl === 'object' ? (pl.uuid || pl.slug) : pl}/edit`,
  'playlists.store': '/api/v1/playlists',
  'playlists.update': (pl: any) => `/api/v1/playlists/${typeof pl === 'object' ? (pl.uuid || pl.slug) : pl}`,
  'playlists.destroy': (pl: any) => `/api/v1/playlists/${typeof pl === 'object' ? (pl.uuid || pl.slug) : pl}`,
  'playlists.collab.set': (uuid: any) => `/api/v1/playlists/${typeof uuid === 'object' ? uuid.uuid : uuid}/collab`,
  'playlists.collaborators.store': (uuid: any) => `/api/v1/playlists/${typeof uuid === 'object' ? uuid.uuid : uuid}/collaborators`,
  'playlists.collaborators.response': (uuid: any) => `/api/v1/playlists/${typeof uuid === 'object' ? uuid.uuid : uuid}/collaborators/response`,

  'podcasts.index': '/podcasts',
  'podcasts.show': (p: any) => `/podcast/${typeof p === 'object' ? (p.uuid || p.slug) : p}`,
  'podcasts.edit': (p: any) => `/podcast/${typeof p === 'object' ? (p.uuid || p.slug) : p}/edit`,
  'podcasts.store': '/api/v1/podcasts',
  'podcasts.update': (p: any) => `/api/v1/podcasts/${typeof p === 'object' ? (p.uuid || p.slug) : p}`,
  'podcasts.seasons.show': (params: any) => `/podcast/${params.podcast || params.slug || params.uuid}/season/${params.season}`,

  'episodes.show': (ep: any) => `/podcast/${typeof ep === 'object' ? ep.podcast_uuid : 'unknown'}/episode/${typeof ep === 'object' ? ep.id : ep}/edit`,
  'episodes.update': (ep: any) => `/api/v1/episodes/${typeof ep === 'object' ? ep.id : ep}`,
  'episodes.destroy': (ep: any) => `/api/v1/episodes/${typeof ep === 'object' ? ep.id : ep}`,

  'posts.index': '/blog',
  'posts.show': (post: any) => `/blog/${typeof post === 'object' ? post.slug : post}`,

  'discover.index': (params?: any) => {
    if (params && typeof params === 'object') {
      const qs = new URLSearchParams(params).toString()
      return `/discover${qs ? '?' + qs : ''}`
    }
    return '/discover'
  },
  'search.show': (params?: any) => {
    if (params && typeof params === 'object') {
      const qs = new URLSearchParams(params).toString()
      return `/search${qs ? '?' + qs : ''}`
    }
    return '/search'
  },

  'login.store': '/api/v1/auth/login',
  'register.store': '/api/v1/auth/register',

  'comments.store': '/api/v1/comments',
  'report.store': '/api/v1/reports',

  'users.favorites.store': () => '/api/v1/favorites',
  'users.favorites.destroy': (params: any) => {
    const id = typeof params === 'object' ? (params.id || params.uuid) : params
    return `/api/v1/favorites/${id}`
  },
  'users.following.store': () => '/api/v1/following',
  'users.following.destroy': (params: any) => {
    const id = typeof params === 'object' ? (params.id || params.uuid) : params
    return `/api/v1/following/${id}`
  },

  'notifications.read': (params?: any) => {
    if (params && typeof params === 'object' && params.notification) {
      return `/api/v1/notifications/${params.notification}/read`
    }
    return '/api/v1/notifications/read'
  },

  'downloads.download': (params: any) => {
    if (typeof params === 'object') {
      return `/api/v1/download/${params.uuid}/${params.type || 'song'}`
    }
    return `/api/v1/download/${params}/song`
  },
  'downloads.download.hd': (params: any) => {
    if (typeof params === 'object') {
      return `/api/v1/download/${params.uuid}/${params.type || 'song'}/hd`
    }
    return `/api/v1/download/${params}/song/hd`
  },

  'share.embed': '/api/v1/share/embed',

  'upload.tracks.store': '/api/v1/upload/tracks',
  'upload.adventures.heading.store': '/api/v1/upload/adventure-heading',
  'upload.adventures.root.store': '/api/v1/upload/adventure-root',
  'upload.adventure.destroy': (adv: any) => `/api/v1/upload/adventures/${typeof adv === 'object' ? adv.id : adv}`,
  'upload.episodes.store': '/api/v1/upload/episodes',

  'settings.profile.update': '/api/v1/settings/profile',
  'settings.profile.avatar.update': '/api/v1/settings/avatar',
  'settings.account.update': '/api/v1/settings/account',
  'settings.password.update': '/api/v1/settings/password',
  'settings.preferences.update': '/api/v1/settings/preferences',
  'settings.subscription.edit': '/settings/subscription',
  'settings.subscription.checkout': '/api/v1/subscriptions/checkout',
  'settings.subscription.suspend': '/api/v1/subscriptions/suspend',
  'settings.subscription.activate': '/api/v1/subscriptions/activate',
  'settings.subscription.cancel': '/api/v1/subscriptions/cancel',
  'settings.connections.redirect': (params: any) => `/api/v1/settings/connections/${params?.provider || params}/redirect`,
  'subscription.update': '/api/v1/subscriptions',

  'users.subscriptions.checkout': (params: any) => `/api/v1/users/${typeof params === 'object' ? params.user : params}/subscriptions/checkout`,
  'users.subscriptions.suspend': (params: any) => `/api/v1/users/${typeof params === 'object' ? params.user : params}/subscriptions/suspend`,
  'users.subscriptions.activate': (params: any) => `/api/v1/users/${typeof params === 'object' ? params.user : params}/subscriptions/activate`,
  'users.subscriptions.cancel': (params: any) => `/api/v1/users/${typeof params === 'object' ? params.user : params}/subscriptions/cancel`,

  'profiles.linktree.update': (params: any) => `/api/v1/linktree`,
  'profiles.linktree.destroy': (params: any) => `/api/v1/linktree`,

  'terms.accept': '/api/v1/terms/accept',
  'tracks.destroy': (song: any) => `/api/v1/songs/${typeof song === 'object' ? (song.uuid || song.slug) : song}`,
}

export function route(name: string, params?: any): string {
  const entry = routeMap[name]
  if (!entry) {
    console.warn(`[route helper] Unknown route: ${name}`)
    return '#'
  }
  if (typeof entry === 'function') {
    return entry(params)
  }
  return entry
}

export default route
