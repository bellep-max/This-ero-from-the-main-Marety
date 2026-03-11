import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import { useAuthStore } from '@/stores/auth';
import apiClient from '@/api/client';
import { USER_PROFILE } from '@/api/endpoints';
import UserLayout from '@/Layouts/UserLayout';

export default function UserPurchased() {
  const { username } = useParams<{ username: string }>();
  const [user, setUser] = useState<any>(null);
  const [orders, setOrders] = useState<any[]>([]);
  const [subscriptions, setSubscriptions] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState(0);
  const authUser = useAuthStore((s) => s.user);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await apiClient.get(USER_PROFILE.PURCHASED(username!));
        const apiData = response.data;
        setUser(apiData.user ?? null);
        setOrders(apiData.orders ?? []);
        setSubscriptions(apiData.subscriptions ?? []);
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

  return (
    <UserLayout title="My Orders" user={user} overflow={false}>
      <div className="tabs-header w-100">
        <ul className="nav px-4 w-100">
          <li
            className={`nav-item tab-item default-text-color px-4 fs-5 font-merge ${activeTab === 0 ? 'tab-item-active fs-5 font-merge' : ''}`}
            onClick={() => setActiveTab(0)}
            style={{ cursor: 'pointer' }}
          >
            Subscriptions ({subscriptions.length})
          </li>
          <li
            className={`nav-item tab-item default-text-color px-4 fs-5 font-merge ${activeTab === 1 ? 'tab-item-active fs-5 font-merge' : ''}`}
            onClick={() => setActiveTab(1)}
            style={{ cursor: 'pointer' }}
          >
            Orders ({orders.length})
          </li>
        </ul>
      </div>
      <div className="py-4">
        {activeTab === 0 && (
          subscriptions.length > 0 ? (
            <div className="d-flex flex-column gap-2 w-100 vh-100 overflow-y-auto">
              {subscriptions.map((sub: any) => (
                <div key={sub.uuid} className="subscription-card">
                  {sub.name}
                </div>
              ))}
            </div>
          ) : (
            <div className="d-flex flex-column w-100 vh-100 align-items-center justify-content-center text-center">
              No orders
            </div>
          )
        )}
        {activeTab === 1 && (
          orders.length > 0 ? (
            <div className="d-flex flex-column gap-2 w-100 vh-100 overflow-y-auto">
              {orders.map((song: any) => (
                <div key={song.uuid} className="song-item">
                  {song.title}
                </div>
              ))}
            </div>
          ) : (
            <div className="d-flex flex-column w-100 vh-100 align-items-center justify-content-center text-center">
              No orders
            </div>
          )
        )}
      </div>
    </UserLayout>
  );
}
