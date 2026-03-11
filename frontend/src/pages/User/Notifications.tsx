import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import apiClient from '@/api/client';
import { USER_PROFILE } from '@/api/endpoints';
import UserLayout from '@/Layouts/UserLayout';

export default function UserNotifications() {
  const { username } = useParams<{ username: string }>();
  const [user, setUser] = useState<any>(null);
  const [notifications, setNotifications] = useState<any[]>([]);
  const [recentActivities, setRecentActivities] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState(0);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await apiClient.get(`/users/${username}/notifications`);
        const apiData = response.data;
        setUser(apiData.user ?? null);
        setNotifications(apiData.notifications ?? []);
        setRecentActivities(apiData.recent_activities ?? []);
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
    <UserLayout title="My Notifications" user={user} overflow={false}>
      <div className="tabs-header w-100">
        <ul className="nav tab-item px-4 w-100">
          <li
            className={`nav-item tab-item default-text-color px-4 ${activeTab === 0 ? 'tab-item-active' : ''}`}
            onClick={() => setActiveTab(0)}
            style={{ cursor: 'pointer' }}
          >
            Notifications
          </li>
          <li
            className={`nav-item tab-item default-text-color px-4 ${activeTab === 1 ? 'tab-item-active' : ''}`}
            onClick={() => setActiveTab(1)}
            style={{ cursor: 'pointer' }}
          >
            Recent Actions
          </li>
        </ul>
      </div>
      <div className="py-4">
        {activeTab === 0 && (
          notifications.length > 0 ? (
            <div className="d-flex flex-column w-100 vh-100 overflow-y-auto p-1">
              {notifications.map((notification: any, index: number) => (
                <div key={notification.id || index} className="notification-card">
                  {notification.message || notification.data?.message}
                </div>
              ))}
            </div>
          ) : (
            <div className="d-block text-center">No notifications</div>
          )
        )}
        {activeTab === 1 && (
          recentActivities.length > 0 ? (
            <div className="d-flex flex-column w-100 vh-100 overflow-y-auto p-1">
              {recentActivities.map((activity: any, index: number) => (
                <div key={activity.id || index} className="notification-card">
                  {activity.message || activity.data?.message}
                </div>
              ))}
            </div>
          ) : (
            <div className="d-block text-center">No recent actions</div>
          )
        )}
      </div>
    </UserLayout>
  );
}
