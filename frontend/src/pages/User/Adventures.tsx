import React, { useState, useEffect, useMemo } from 'react';
import { useParams } from 'react-router-dom';
import { useAuthStore } from '@/stores/auth';
import apiClient from '@/api/client';
import { USER_PROFILE } from '@/api/endpoints';
import UserLayout from '@/Layouts/UserLayout';

export default function UserAdventures() {
  const { username } = useParams<{ username: string }>();
  const [user, setUser] = useState<any>(null);
  const [adventures, setAdventures] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [showAdventureModal, setShowAdventureModal] = useState(false);
  const authUser = useAuthStore((s) => s.user);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await apiClient.get(USER_PROFILE.ADVENTURES(username!));
        const apiData = response.data;
        setUser(apiData.user ?? null);
        setAdventures(apiData.adventures ?? []);
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
    ? 'My Adventures'
    : `${user?.name}'s Adventures`;

  const controlsContent = canUpload ? (
    <button
      className="btn btn-pink btn-narrow"
      onClick={() => setShowAdventureModal(true)}
    >
      Upload Adventure
    </button>
  ) : undefined;

  return (
    <UserLayout title={pageTitle} user={user} controls={controlsContent}>
      {adventures && adventures.length > 0 ? (
        <div className="d-flex flex-column w-100 vh-100 overflow-y-auto p-1">
          {adventures.map((adventure: any) => (
            <div key={adventure.uuid} className="adventure-item">
              {adventure.title}
            </div>
          ))}
        </div>
      ) : (
        <div className="d-flex flex-column w-100 vh-100 align-items-center justify-content-center text-center">
          {user?.name} has no adventures
        </div>
      )}
    </UserLayout>
  );
}
