import React, { useState } from 'react';
import { useAuthStore } from '@/stores/auth';
import apiClient from '@/api/client';
import { SETTINGS } from '@/api/endpoints';
import SettingsLayout from '@/Layouts/SettingsLayout';

export default function SettingsAccount() {
  const authUser = useAuthStore((s) => s.user);

  const [username, setUsername] = useState(authUser?.username || '');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');

  const slugify = (value: string) => {
    return value.toLowerCase().replace(/[^a-z0-9-]/g, '-');
  };

  const validateEmail = (value: string) => {
    if (!value) return undefined;
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      const data: any = { username };
      if (email) data.email = email;
      if (password) data.password = password;
      await apiClient.patch(SETTINGS.ACCOUNT, data);
    } catch (error) {
      console.error('Failed to update account:', error);
    }
  };

  return (
    <SettingsLayout title="Account">
      <div className="d-flex flex-column justify-content-start align-items-center gap-4">
        <div className="d-flex flex-row justify-content-between align-items-start gap-4 w-100">
          <div className="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
            <label className="text-start font-default fs-14 default-text-color">
              Username
            </label>
            <input
              type="text"
              className="form-control"
              value={username}
              onChange={(e) => setUsername(slugify(e.target.value))}
            />
            <span className="fs-12 font-merge color-grey">
              Your unique username URL.
            </span>
          </div>
          <div className="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
            <label className="text-start font-default fs-14 default-text-color">
              Email
            </label>
            <input
              type="email"
              className={`form-control ${email ? (validateEmail(email) ? 'is-valid' : 'is-invalid') : ''}`}
              value={email}
              onChange={(e) => setEmail(e.target.value)}
            />
            <span className="fs-12 font-merge color-grey">
              Your email address.
            </span>
          </div>
        </div>
        <div className="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
          <label className="text-start font-default fs-14 default-text-color">
            Current Password
          </label>
          <input
            type="password"
            className={`form-control ${password.length > 0 ? 'is-valid' : ''}`}
            value={password}
            onChange={(e) => setPassword(e.target.value)}
          />
          <span className="fs-12 font-merge color-grey">
            Enter your current password to confirm changes.
          </span>
        </div>
        <div className="col-12 d-flex flex-row justify-content-center align-items-center gap-4 mt-5">
          <button className="btn btn-pink" onClick={handleSubmit}>
            Save Changes
          </button>
          <button className="btn btn-outline">
            Delete Account
          </button>
        </div>
      </div>
    </SettingsLayout>
  );
}
