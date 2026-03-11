import React, { useState, useEffect } from 'react';
import { useAuthStore } from '@/stores/auth';
import apiClient from '@/api/client';
import { SETTINGS } from '@/api/endpoints';
import SettingsLayout from '@/Layouts/SettingsLayout';

export default function SettingsConnectedServices() {
  const [connections, setConnections] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const authUser = useAuthStore((s) => s.user);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await apiClient.get(SETTINGS.CONNECTED_SERVICES);
        const apiData = response.data;
        setConnections(apiData.connections ?? []);
      } catch (error) {
        console.error('Failed to load page data:', error);
      } finally {
        setLoading(false);
      }
    };
    fetchData();
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
    <SettingsLayout title="Connected Services">
      <div className="d-flex flex-column justify-content-center align-items-center gap-4">
        {connections && connections.length > 0 ? (
          <table className="table">
            <thead>
              <tr>
                {Object.keys(connections[0]).map((key) => (
                  <th key={key}>{key}</th>
                ))}
              </tr>
            </thead>
            <tbody>
              {connections.map((connection: any, index: number) => (
                <tr key={index}>
                  {Object.values(connection).map((value: any, i: number) => (
                    <td key={i}>{String(value)}</td>
                  ))}
                </tr>
              ))}
            </tbody>
          </table>
        ) : (
          <>
            <img src="/assets/images/services.svg" alt="Connected Services" />
            <div className="profile_page__content__subscription__desc">
              Connect your external services to enhance your experience.
            </div>
            <a
              href="/api/v1/settings/connections/redirect/spotify"
              className="btn btn-pink btn-wide"
            >
              Connect Spotify
            </a>
          </>
        )}
      </div>
    </SettingsLayout>
  );
}
