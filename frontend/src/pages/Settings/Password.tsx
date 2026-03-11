import React, { useState, useMemo } from 'react';
import apiClient from '@/api/client';
import { SETTINGS } from '@/api/endpoints';
import SettingsLayout from '@/Layouts/SettingsLayout';

export default function SettingsPassword() {
  const [currentPassword, setCurrentPassword] = useState('');
  const [password, setPassword] = useState('');
  const [passwordConfirmation, setPasswordConfirmation] = useState('');

  const minimalLength = 8;

  const allFieldsSet = currentPassword.length > 0 && password.length > 0 && passwordConfirmation.length > 0;
  const validatePwdInput = password.length > minimalLength;
  const validatePwdConfirmInput = passwordConfirmation.length > minimalLength;

  const handleUpdatePassword = async () => {
    try {
      await apiClient.put(SETTINGS.PASSWORD, {
        current_password: currentPassword,
        password,
        password_confirmation: passwordConfirmation,
      });
      setCurrentPassword('');
      setPassword('');
      setPasswordConfirmation('');
    } catch (error) {
      console.error('Failed to update password:', error);
    }
  };

  return (
    <SettingsLayout title="Password">
      <div className="d-flex flex-column justify-content-start align-items-center gap-4">
        <div className="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
          <label className="text-start font-default fs-14 default-text-color">
            Current Password
          </label>
          <input
            type="password"
            className="form-control"
            value={currentPassword}
            onChange={(e) => setCurrentPassword(e.target.value)}
          />
        </div>
        <div className="d-flex flex-row justify-content-between align-items-center gap-4 w-100">
          <div className="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
            <label className="text-start font-default fs-14 default-text-color">
              New Password
            </label>
            <input
              type="password"
              className={`form-control ${password ? (validatePwdInput ? 'is-valid' : 'is-invalid') : ''}`}
              value={password}
              onChange={(e) => setPassword(e.target.value)}
            />
          </div>
          <div className="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
            <label className="text-start font-default fs-14 default-text-color">
              Retype New Password
            </label>
            <input
              type="password"
              className={`form-control ${passwordConfirmation ? (validatePwdConfirmInput ? 'is-valid' : 'is-invalid') : ''}`}
              value={passwordConfirmation}
              onChange={(e) => setPasswordConfirmation(e.target.value)}
            />
          </div>
        </div>
        <span className="fs-12 font-merge color-grey text-center">
          Password must be at least {minimalLength} characters long.
        </span>
        <button
          className="btn btn-pink"
          onClick={handleUpdatePassword}
          disabled={!allFieldsSet}
        >
          Change Password
        </button>
      </div>
    </SettingsLayout>
  );
}
