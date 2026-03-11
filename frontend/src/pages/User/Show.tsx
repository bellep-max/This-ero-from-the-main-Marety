import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import { useAuthStore } from '@/stores/auth';
import apiClient from '@/api/client';
import { USER_PROFILE } from '@/api/endpoints';
import UserLayout from '@/Layouts/UserLayout';

export default function UserShow() {
  const { username } = useParams<{ username: string }>();
  const [user, setUser] = useState<any>(null);
  const [recent, setRecent] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const authUser = useAuthStore((s) => s.user);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await apiClient.get(USER_PROFILE.SHOW(username!));
        const apiData = response.data;
        setUser(apiData.user ?? null);
        setRecent(apiData.recent ?? []);
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
    ? 'My Profile Overview'
    : `${user?.name}'s Profile Overview`;

  return (
    <UserLayout title={pageTitle} user={user} overflow={false}>
      <div className="d-flex flex-row gap-4 justify-content-xl-start justify-content-center align-items-center flex-wrap">
        <div className="circled-text">
          <div className="circled-text__title">{user?.favorites}</div>
          <div className="circled-text__description">Favorites</div>
        </div>
        <div className="circled-text">
          <div className="circled-text__title">{user?.total_plays}</div>
          <div className="circled-text__description">Total Plays</div>
        </div>
        <div className="circled-text">
          <div className="circled-text__title">{user?.free_followers_count}</div>
          <div className="circled-text__description">Free Followers</div>
        </div>
        <div className="circled-text">
          <div className="circled-text__title">{user?.patrons_count}</div>
          <div className="circled-text__description">Patrons</div>
        </div>
      </div>
      {recent && recent.length > 0 && (
        <div className="d-flex flex-column w-100 mt-4 vh-100">
          <div className="profile-block-header">Recent Tracks</div>
          <div className="d-flex flex-column overflow-y-auto p-2">
            {recent.map((song: any) => (
              <div key={song.uuid} className="song-item">
                {song.title}
              </div>
            ))}
          </div>
        </div>
      )}
    </UserLayout>
  );
}
