import React, { useState } from 'react';
import { useAuthStore } from '@/stores/auth';
import apiClient from '@/api/client';
import { SETTINGS } from '@/api/endpoints';
import SettingsLayout from '@/Layouts/SettingsLayout';

export default function SettingsPreferences() {
  const authUser = useAuthStore((s) => s.user);

  const [restoreQueue, setRestoreQueue] = useState(authUser?.restore_queue ?? false);
  const [playPauseFade, setPlayPauseFade] = useState(authUser?.play_pause_fade ?? false);
  const [allowComments, setAllowComments] = useState(authUser?.allow_comments ?? false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      await apiClient.patch(SETTINGS.PREFERENCES, {
        restore_queue: restoreQueue,
        play_pause_fade: playPauseFade,
        allow_comments: allowComments,
      });
    } catch (error) {
      console.error('Failed to update preferences:', error);
    }
  };

  return (
    <SettingsLayout title="Preferences">
      <div className="d-flex flex-column align-items-start gap-4 ps-5">
        <div className="form-check form-switch">
          <input
            className="form-check-input"
            type="checkbox"
            checked={restoreQueue}
            onChange={(e) => setRestoreQueue(e.target.checked)}
          />
          <label className="form-check-label">Restore songs on page load</label>
        </div>
        <div className="form-check form-switch">
          <input
            className="form-check-input"
            type="checkbox"
            checked={playPauseFade}
            onChange={(e) => setPlayPauseFade(e.target.checked)}
          />
          <label className="form-check-label">Fade on play/pause</label>
        </div>
        <div className="form-check form-switch">
          <input
            className="form-check-input"
            type="checkbox"
            checked={allowComments}
            onChange={(e) => setAllowComments(e.target.checked)}
          />
          <label className="form-check-label">Allow comments on your profile</label>
        </div>
        <button className="btn btn-pink mx-auto" onClick={handleSubmit}>
          Save Changes
        </button>
      </div>
    </SettingsLayout>
  );
}
