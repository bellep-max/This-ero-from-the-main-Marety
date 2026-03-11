import React, { useState, useEffect, useCallback } from 'react';
import { useParams } from 'react-router-dom';
import { useAuthStore } from '@/stores/auth';
import apiClient from '@/api/client';
import { USER_PROFILE } from '@/api/endpoints';
import UserLayout from '@/Layouts/UserLayout';

export default function UserTracks() {
  const { username } = useParams<{ username: string }>();
  const [user, setUser] = useState<any>(null);
  const [tracks, setTracks] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const authUser = useAuthStore((s) => s.user);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await apiClient.get(USER_PROFILE.TRACKS(username!));
        const apiData = response.data;
        setUser(apiData.user ?? null);
        setTracks(apiData.tracks ?? []);
      } catch (error) {
        console.error('Failed to load page data:', error);
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, [username]);

  const handleSearchChange = useCallback((e: React.ChangeEvent<HTMLInputElement>) => {
    setSearch(e.target.value);
  }, []);

  const resetFilters = useCallback(() => {
    setSearch('');
  }, []);

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
    ? 'My Tracks'
    : `${user?.name}'s Tracks`;

  return (
    <UserLayout title={pageTitle} user={user} overflow={false}>
      <div className="d-flex flex-column gap-4">
        <div className="d-flex flex-row justify-content-between gap-5">
          <div className="input-group input-group-sm">
            <input
              type="text"
              className="form-control"
              placeholder="Search"
              value={search}
              onChange={handleSearchChange}
            />
            <button
              className="btn btn-outline-danger"
              onClick={resetFilters}
              disabled={!search}
            >
              Clear
            </button>
          </div>
        </div>
        {tracks && tracks.length > 0 ? (
          <div className="d-flex flex-column w-100 vh-100 overflow-y-auto p-1">
            {tracks.map((song: any) => (
              <div key={song.uuid} className="song-item">
                {song.title}
              </div>
            ))}
          </div>
        ) : (
          <div className="d-flex flex-column w-100 vh-100 align-items-center justify-content-center text-center">
            {user?.name} has no tracks
          </div>
        )}
      </div>
    </UserLayout>
  );
}
