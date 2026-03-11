import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import { useAuthStore } from '@/stores/auth';
import apiClient from '@/api/client';
import { USER_PROFILE } from '@/api/endpoints';
import UserLayout from '@/Layouts/UserLayout';

export default function UserFavorites() {
  const { username } = useParams<{ username: string }>();
  const [user, setUser] = useState<any>(null);
  const [adventures, setAdventures] = useState<any[]>([]);
  const [podcasts, setPodcasts] = useState<any[]>([]);
  const [episodes, setEpisodes] = useState<any[]>([]);
  const [songs, setSongs] = useState<any[]>([]);
  const [playlists, setPlaylists] = useState<any[]>([]);
  const [users, setUsers] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState(0);
  const authUser = useAuthStore((s) => s.user);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await apiClient.get(USER_PROFILE.FAVORITES(username!));
        const apiData = response.data;
        setUser(apiData.user ?? null);
        setAdventures(apiData.adventures ?? []);
        setPodcasts(apiData.podcasts ?? []);
        setEpisodes(apiData.episodes ?? []);
        setSongs(apiData.songs ?? []);
        setPlaylists(apiData.playlists ?? []);
        setUsers(apiData.users ?? []);
      } catch (error) {
        console.error('Failed to load page data:', error);
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, [username]);

  if (loading) {
    return (
      <div className="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
        <div className="spinner-border text-light" role="status">
          <span className="visually-hidden">Loading...</span>
        </div>
      </div>
    );
  }

  const tabs = [
    { title: `Songs (${songs.length})`, key: 'songs' },
    { title: `Adventures (${adventures.length})`, key: 'adventures' },
    { title: `Podcasts (${podcasts.length})`, key: 'podcasts' },
    { title: `Episodes (${episodes.length})`, key: 'episodes' },
    { title: `Playlists (${playlists.length})`, key: 'playlists' },
    { title: `Users (${users.length})`, key: 'users' },
  ];

  return (
    <UserLayout title="My Favorites" user={user} overflow={false}>
      <div className="tabs-header w-100">
        <ul className="nav px-4 d-flex flex-row justify-content-between w-100">
          {tabs.map((tab, index) => (
            <li
              key={tab.key}
              className={`nav-item tab-item default-text-color px-4 fs-5 font-merge ${activeTab === index ? 'tab-item-active' : ''}`}
              onClick={() => setActiveTab(index)}
              style={{ cursor: 'pointer' }}
            >
              {tab.title}
            </li>
          ))}
        </ul>
      </div>
      <div className="py-4">
        {activeTab === 0 && songs.map((song: any) => (
          <div key={song.uuid} className="song-item">{song.title}</div>
        ))}
        {activeTab === 1 && adventures.map((adventure: any) => (
          <div key={adventure.uuid} className="adventure-item">{adventure.title}</div>
        ))}
        {activeTab === 2 && (
          <div className="d-flex flex-row justify-content-center flex-wrap align-items-center gap-2 gap-lg-4">
            {podcasts.map((podcast: any) => (
              <div key={podcast.uuid} className="image-card">{podcast.title}</div>
            ))}
          </div>
        )}
        {activeTab === 3 && (
          <div className="d-flex flex-row justify-content-center flex-wrap align-items-center gap-2 gap-lg-4">
            {episodes.map((episode: any) => (
              <div key={episode.uuid} className="image-card">{episode.title}</div>
            ))}
          </div>
        )}
        {activeTab === 4 && (
          <div className="row w-100 vh-100 overflow-y-auto">
            {playlists.map((playlist: any) => (
              <div key={playlist.uuid} className="playlist-card">{playlist.title}</div>
            ))}
          </div>
        )}
        {activeTab === 5 && (
          <div className="d-flex flex-row justify-content-center flex-wrap align-items-center gap-2 gap-lg-4">
            {users.map((u: any) => (
              <div key={u.uuid} className="follower-card">{u.name}</div>
            ))}
          </div>
        )}
      </div>
    </UserLayout>
  );
}
