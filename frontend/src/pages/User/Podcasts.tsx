import React, { useState, useEffect, useMemo } from 'react';
import { useParams } from 'react-router-dom';
import { useAuthStore } from '@/stores/auth';
import apiClient from '@/api/client';
import { USER_PROFILE } from '@/api/endpoints';
import UserLayout from '@/Layouts/UserLayout';

export default function UserPodcasts() {
  const { username } = useParams<{ username: string }>();
  const [user, setUser] = useState<any>(null);
  const [podcasts, setPodcasts] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const authUser = useAuthStore((s) => s.user);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await apiClient.get(USER_PROFILE.PODCASTS(username!));
        const apiData = response.data;
        setUser(apiData.user ?? null);
        setPodcasts(apiData.podcasts ?? []);
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

  const canUpload = user?.own_profile && authUser?.can_upload;
  const pageTitle = user?.own_profile
    ? 'My Podcasts'
    : `${user?.name}'s Podcasts`;

  const controlsContent = canUpload ? (
    <div className="d-flex flex-row gap-3">
      <button className="btn btn-outline btn-narrow">Create Podcast</button>
      <button className="btn btn-outline btn-narrow">Upload Episode</button>
    </div>
  ) : undefined;

  return (
    <UserLayout title={pageTitle} user={user} controls={controlsContent}>
      {podcasts && podcasts.length > 0 ? (
        <div className="d-flex flex-row justify-content-start flex-wrap align-items-start gap-2">
          {podcasts.map((podcast: any) => (
            <div key={podcast.uuid} className="image-card">
              {podcast.title}
            </div>
          ))}
        </div>
      ) : (
        <div className="d-flex flex-column w-100 vh-100 align-items-center justify-content-center text-center">
          {user?.name} has no podcasts
        </div>
      )}
    </UserLayout>
  );
}
