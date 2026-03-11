import React, { useState, useEffect } from 'react';
import { useAuthStore } from '@/stores/auth';
import apiClient from '@/api/client';
import { SETTINGS } from '@/api/endpoints';
import SettingsLayout from '@/Layouts/SettingsLayout';

export default function SettingsProfile() {
  const [genders, setGenders] = useState<any[]>([]);
  const [countries, setCountries] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const authUser = useAuthStore((s) => s.user);

  const [name, setName] = useState(authUser?.name || '');
  const [bio, setBio] = useState(authUser?.bio || '');
  const [birth, setBirth] = useState(authUser?.birth || '');
  const [gender, setGender] = useState(authUser?.gender || '');
  const [countryId, setCountryId] = useState(authUser?.country || '');

  const today = new Date().toISOString().substring(0, 10);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await apiClient.get(SETTINGS.PROFILE);
        const apiData = response.data;
        setGenders(apiData.genders ?? []);
        setCountries(apiData.countries ?? []);
      } catch (error) {
        console.error('Failed to load page data:', error);
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, []);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      await apiClient.patch(SETTINGS.PROFILE, {
        name,
        bio,
        birth,
        gender,
        country_id: countryId,
      });
    } catch (error) {
      console.error('Failed to update profile:', error);
    }
  };

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
    <SettingsLayout title="Profile">
      <form className="row gy-4" onSubmit={handleSubmit}>
        <div className="col-12 col-lg-6 d-flex flex-column justify-content-start align-items-start gap-1">
          <label className="font-default fs-14 default-text-color">Display Name:</label>
          <input
            type="text"
            className="form-control"
            maxLength={175}
            value={name}
            onChange={(e) => setName(e.target.value)}
          />
          <span className="fs-12 font-merge color-grey">
            Your display name visible to other users.
          </span>
        </div>
        <div className="col-12 col-lg-6 d-flex flex-column justify-content-start align-items-start gap-1">
          <label className="font-default fs-14 default-text-color">Country:</label>
          <select
            className="form-select"
            value={countryId}
            onChange={(e) => setCountryId(e.target.value)}
          >
            <option value="">Select country</option>
            {countries.map((country: any) => (
              <option key={country.id} value={country.id}>
                {country.name}
              </option>
            ))}
          </select>
          <span className="fs-12 font-merge color-grey">
            Your country location.
          </span>
        </div>
        <div className="col-12 col-lg-6 d-flex flex-column justify-content-start align-items-start gap-1">
          <label className="font-default fs-14 default-text-color">Gender:</label>
          <select
            className="form-select"
            value={gender}
            onChange={(e) => setGender(e.target.value)}
          >
            <option value="">Select gender</option>
            {genders.map((g: any, index: number) => (
              <option key={index} value={g.value ?? g}>
                {g.text ?? g}
              </option>
            ))}
          </select>
          <span className="fs-12 font-merge color-grey">
            Your gender identity.
          </span>
        </div>
        <div className="col-12 col-lg-6 d-flex flex-column justify-content-start align-items-start gap-1">
          <label className="font-default fs-14 default-text-color">Birth Date:</label>
          <input
            type="date"
            className="form-control"
            value={birth}
            max={today}
            onChange={(e) => setBirth(e.target.value)}
          />
          <span className="fs-12 font-merge color-grey">
            Your birth date.
          </span>
        </div>
        <div className="col-12 d-flex flex-column justify-content-start align-items-start gap-1">
          <label className="font-default fs-14 default-text-color">Bio:</label>
          <textarea
            className="form-control"
            maxLength={180}
            value={bio}
            onChange={(e) => setBio(e.target.value)}
          />
          <span className="fs-12 font-merge color-grey">
            A short bio about yourself.
          </span>
        </div>
        <div className="col-12 d-flex flex-row justify-content-center align-items-center gap-4 mt-5">
          <button type="submit" className="btn btn-pink">
            Save Changes
          </button>
        </div>
      </form>
    </SettingsLayout>
  );
}
