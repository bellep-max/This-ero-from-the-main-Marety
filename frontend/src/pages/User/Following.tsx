import React, { useState, useEffect, useCallback } from 'react';
import { useParams } from 'react-router-dom';
import { useAuthStore } from '@/stores/auth';
import apiClient from '@/api/client';
import { USER_PROFILE, FOLLOWING_API } from '@/api/endpoints';
import UserLayout from '@/Layouts/UserLayout';

export default function UserFollowing() {
  const { username } = useParams<{ username: string }>();
  const [user, setUser] = useState<any>(null);
  const [following, setFollowing] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const authUser = useAuthStore((s) => s.user);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await apiClient.get(USER_PROFILE.FOLLOWING(username!));
        const apiData = response.data;
        setUser(apiData.user ?? null);
        setFollowing(apiData.following ?? []);
      } catch (error) {
        console.error('Failed to load page data:', error);
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, [username]);

  const handleUnfollow = useCallback(async (targetUser: any) => {
    try {
      await apiClient.delete(FOLLOWING_API.DELETE, {
        data: { uuid: targetUser.uuid },
      });
      setFollowing((prev) => prev.filter((u) => u.uuid !== targetUser.uuid));
    } catch (error) {
      console.error('Failed to unfollow:', error);
    }
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

  return (
    <UserLayout title="Following" user={user}>
      {following && following.length > 0 ? (
        <div className="row w-100">
          {following.map((u: any) => (
            <div key={u.uuid} className="follower-card">
              <span>{u.name}</span>
              <button className="btn btn-sm btn-outline-danger" onClick={() => handleUnfollow(u)}>
                Unfollow
              </button>
            </div>
          ))}
        </div>
      ) : (
        <div className="d-flex flex-column w-100 vh-100 align-items-center justify-content-center text-center">
          Not following anyone
        </div>
      )}
    </UserLayout>
  );
}
