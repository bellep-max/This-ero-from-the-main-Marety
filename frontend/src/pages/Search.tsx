import React, { useState, useEffect, useCallback, useMemo } from 'react';
import { useSearchParams } from 'react-router-dom';
import apiClient from '@/api/client';
import { $t } from '@/i18n';
import { useAuthStore } from '@/stores/auth';
import { isLogged } from '@/Services/AuthService';
import { isNotEmpty } from '@/Services/MiscService';
import { addToFavorites, removeFromFavorites } from '@/Services/FavoriteService';
import Song from '@/Components/Song';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import Icon from '@/Components/Icons/Icon';
import RoundButton from '@/Components/Buttons/RoundButton';
import DefaultCarousel from '@/Components/Carousels/DefaultCarousel';
import ImageCard from '@/Components/Cards/ImageCard';
import route from '@/helpers/route';

export default function SearchPage() {
  const [searchParams] = useSearchParams();
  const [searchString, setSearchString] = useState('');
  const [songs, setSongs] = useState<any[]>([]);
  const [users, setUsers] = useState<any[]>([]);
  const [pagination, setPagination] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  const authStore = useAuthStore();
  const user = authStore.user;

  const lastPage = useMemo(
    () => !pagination || pagination.current_page >= pagination.last_page,
    [pagination]
  );

  const fetchData = useCallback(async (page = 1) => {
    try {
      const q = searchParams.get('q') || '';
      const response = await apiClient.get('/search', { params: { q, page } });
      const apiData = response.data;
      setSearchString(apiData.searchString ?? q);
      if (page === 1) {
        setSongs(apiData.songs ?? []);
      } else {
        setSongs((prev) => [...prev, ...(apiData.songs ?? [])]);
      }
      setUsers(apiData.users ?? []);
      setPagination(apiData.pagination ?? null);
    } catch (error) {
      console.error('Failed to load page data:', error);
    } finally {
      setLoading(false);
    }
  }, [searchParams]);

  useEffect(() => {
    setLoading(true);
    fetchData();
  }, [searchParams.get('q')]);

  const loadMore = () => {
    if (pagination) {
      fetchData(pagination.current_page + 1);
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
    <div className="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
      <div className="container">
        <div className="row">
          <div className="col text-start">
            <div className="d-block">
              <div className="block-title color-light text-truncate">
                {$t('pages.search.title', { query: searchString })}
              </div>
            </div>
          </div>
        </div>
        <div className="row mt-3">
          <div className="col text-start">
            <div className="d-block">
              <div className="block-description color-light">
                {$t('pages.search.users')}
              </div>
            </div>
          </div>
          <div className="col-12 text-center">
            {isNotEmpty(users) ? (
              <DefaultCarousel config={{ itemsToShow: 'auto', wrapAround: true, gap: 24 }}>
                {users.map((slide: any, i: number) => (
                  <ImageCard
                    key={slide.uuid || i}
                    model={slide}
                    route={route('users.show', slide.uuid)}
                  />
                ))}
              </DefaultCarousel>
            ) : (
              <span className="block-description color-light text-center">
                {$t('pages.search.not_found')}
              </span>
            )}
          </div>
        </div>
        <div className="row mt-3">
          <div className="col text-start">
            <div className="d-block">
              <div className="block-description color-light">
                {$t('pages.search.songs')}
              </div>
            </div>
          </div>
          {isNotEmpty(songs) ? (
            <div className="col-12 flex-column">
              {songs.map((song: any) => (
                <Song
                  key={song.uuid}
                  song={song}
                  canView={isLogged}
                  isOwned={song.user?.uuid === user?.uuid}
                  onAddToFavorites={(e: any) => addToFavorites(e, authStore.user?.uuid)}
                  onRemoveFromFavorites={(e: any) => removeFromFavorites(e, authStore.user?.uuid)}
                />
              ))}
              <DefaultButton classList="btn-outline mt-2 mx-auto" onClick={loadMore} disabled={lastPage}>
                {$t('buttons.load_more')}
              </DefaultButton>
            </div>
          ) : (
            <span className="block-description color-light text-center">
              {$t('pages.search.not_found')}
            </span>
          )}
        </div>
      </div>
    </div>
  );
}
