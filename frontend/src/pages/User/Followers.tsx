import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import apiClient from '@/api/client';
import { USER_PROFILE } from '@/api/endpoints';
import UserLayout from '@/Layouts/UserLayout';

export default function UserFollowers() {
  const { username } = useParams<{ username: string }>();
  const [user, setUser] = useState<any>(null);
  const [patrons, setPatrons] = useState<any[]>([]);
  const [freeFollowers, setFreeFollowers] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState(0);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await apiClient.get(USER_PROFILE.FOLLOWERS(username!));
        const apiData = response.data;
        setUser(apiData.user ?? null);
        setPatrons(apiData.patrons ?? []);
        setFreeFollowers(apiData.free_followers ?? []);
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
    ? 'My Followers'
    : `${user?.name}'s Followers`;

  return (
    <UserLayout title={pageTitle} user={user} overflow={false}>
      {user?.own_profile ? (
        <>
          <div className="tabs-header w-100">
            <ul className="nav tab-item px-4 w-100">
              <li
                className={`nav-item tab-item default-text-color px-4 ${activeTab === 0 ? 'tab-item-active' : ''}`}
                onClick={() => setActiveTab(0)}
                style={{ cursor: 'pointer' }}
              >
                Free Followers ({freeFollowers.length})
              </li>
              <li
                className={`nav-item tab-item default-text-color px-4 ${activeTab === 1 ? 'tab-item-active' : ''}`}
                onClick={() => setActiveTab(1)}
                style={{ cursor: 'pointer' }}
              >
                Patrons ({patrons.length})
              </li>
            </ul>
          </div>
          <div className="py-4 container-fluid w-100 vh-100 overflow-y-auto">
            {activeTab === 0 && (
              freeFollowers.length > 0 ? (
                <div className="row w-100">
                  {freeFollowers.map((u: any) => (
                    <div key={u.uuid} className="follower-card">{u.name}</div>
                  ))}
                </div>
              ) : (
                <div className="d-flex flex-row align-items-center text-center justify-content-center w-100">
                  {user?.name} has no followers
                </div>
              )
            )}
            {activeTab === 1 && (
              patrons.length > 0 ? (
                <div className="row w-100">
                  {patrons.map((u: any) => (
                    <div key={u.uuid} className="follower-card">{u.name}</div>
                  ))}
                </div>
              ) : (
                <div className="d-flex flex-row align-items-center text-center justify-content-center w-100">
                  {user?.name} has no followers
                </div>
              )
            )}
          </div>
        </>
      ) : (
        freeFollowers.length > 0 ? (
          <div className="row w-100">
            {freeFollowers.map((u: any) => (
              <div key={u.uuid} className="follower-card">{u.name}</div>
            ))}
          </div>
        ) : (
          <div className="d-flex flex-row align-items-center text-center justify-content-center w-100">
            {user?.name} has no followers
          </div>
        )
      )}
    </UserLayout>
  );
}
