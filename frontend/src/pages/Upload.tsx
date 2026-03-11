import React, { useState, useEffect, useMemo } from 'react';
import apiClient from '@/api/client';
import { $t } from '@/i18n';
import { useAuthStore } from '@/stores/auth';
import { uploadAssetImage } from '@/Services/AssetService';
import { isNotEmpty } from '@/Services/MiscService';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import CircledText from '@/Components/CircledText';
import route from '@/helpers/route';

export default function UploadPage() {
  const [plan, setPlan] = useState<any>(null);
  const [genres, setGenres] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [showTrackModal, setShowTrackModal] = useState(false);
  const [showAdventureModal, setShowAdventureModal] = useState(false);

  const authStore = useAuthStore();
  const hasUploadAccess = authStore.user?.can_upload;
  const currentUser = authStore.user;

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await apiClient.get('/uploads');
        const apiData = response.data;
        setPlan(apiData.plan ?? null);
        setGenres(apiData.genres ?? null);
      } catch (error) {
        console.error('Failed to load page data:', error);
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, []);

  const parsedPlanPrice = useMemo(() => `$${plan?.price ?? '0'}`, [plan]);

  const openPodcastModal = () => {
    console.log('Podcast modal not yet implemented in React');
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
    <div className="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
      <div className="container">
        <div className="row">
          <div className="col text-start">
            <div className="d-block">
              <div className="block-title color-light text-truncate">
                {$t('pages.upload.title')}
              </div>
            </div>
          </div>
        </div>
        <div className="row mt-3">
          <div className="col d-flex flex-column justify-content-center align-items-center">
            <div className="container bg-default rounded-5 p-3 p-lg-5 h-100">
              {hasUploadAccess ? (
                <div className="d-flex flex-column align-items-center justify-content-center gap-4">
                  <img src={uploadAssetImage} alt={$t('pages.upload.title')} />
                  <div className="text-start font-default">
                    <p>{$t('pages.upload.recommended_format')}</p>
                    <p>{$t('pages.upload.max_size')}</p>
                    <p>{$t('pages.upload.time_allowed')}</p>
                    <p>{$t('pages.upload.formats_allowed')}</p>
                  </div>
                  <div className="d-flex flex-column flex-md-row justify-content-center align-items-center gap-3">
                    <DefaultButton classList="btn-pink" onClick={() => setShowTrackModal(true)}>
                      {$t('buttons.upload.track')}
                    </DefaultButton>
                    <DefaultButton classList="btn-pink" onClick={() => setShowAdventureModal(true)}>
                      {$t('buttons.upload.adventure')}
                    </DefaultButton>
                    <DefaultButton classList="btn-pink" onClick={openPodcastModal}>
                      {$t('buttons.upload.podcast_episode')}
                    </DefaultButton>
                  </div>
                </div>
              ) : (
                <div className="d-flex flex-column align-items-center justify-content-center gap-4">
                  <div className="text-center">
                    <p className="font-default default-text-color">
                      {$t('pages.upload.limit_exceeded')}
                    </p>
                    <p className="font-default default-text-color">
                      {$t('pages.upload.upgrade_account')}
                    </p>
                  </div>
                  <div className="text-center fs-5 font-default fw-bolder">
                    {plan?.name}
                  </div>
                  <CircledText title={parsedPlanPrice} description={`/${$t('misc.month')}`} />
                  <DefaultButton href={route('settings.subscription.edit')} classList="btn-pink">
                    {$t('buttons.buy_now')}
                  </DefaultButton>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
