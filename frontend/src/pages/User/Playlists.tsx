import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import apiClient from '@/api/client';
import { USER_PROFILE } from '@/api/endpoints';
import UserLayout from '@/Layouts/UserLayout';

export default function UserPlaylists() {
  const { username } = useParams<{ username: string }>();
  const [user, setUser] = useState<any>(null);
  const [playlists, setPlaylists] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await apiClient.get(USER_PROFILE.PLAYLISTS(username!));
        const apiData = response.data;
        setUser(apiData.user ?? null);
        setPlaylists(apiData.playlists ?? []);
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

  const pageTitle = user?.own_profile
    ? 'My Playlists'
    : `${user?.name}'s Playlists`;

  const controlsContent = user?.own_profile ? (
    <button className="btn btn-outline btn-narrow">
      Create Playlist
    </button>
  ) : undefined;

  return (
    <UserLayout title={pageTitle} user={user} controls={controlsContent}>
      {playlists && playlists.length > 0 ? (
        <div className="row w-100 vh-100 overflow-y-auto">
          {playlists.map((playlist: any) => (
            <div key={playlist.uuid} className="playlist-card">
              {playlist.title}
            </div>
          ))}
        </div>
      ) : (
        <div className="d-flex flex-column w-100 vh-100 align-items-center justify-content-center text-center">
          {user?.name} has no playlists
        </div>
      )}
    </UserLayout>
  );
}
