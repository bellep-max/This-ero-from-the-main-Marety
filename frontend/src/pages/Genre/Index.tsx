import React, { useState, useEffect } from 'react';
import apiClient from '@/api/client';
import { $t } from '@/i18n';
import ImageCard from '@/Components/Cards/ImageCard';
import route from '@/helpers/route';

export default function GenreIndexPage() {
  const [genres, setGenres] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await apiClient.get('/genres');
        const apiData = response.data;
        setGenres(apiData.genres ?? null);
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
    <div className="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
      <div className="container">
        <div className="row">
          <div className="col text-start">
            <div className="d-block">
              <div className="block-title color-light text-truncate">
                {$t('pages.genres.title')}
              </div>
              <div className="block-description color-light">
                {$t('pages.genres.description')}
              </div>
            </div>
          </div>
        </div>
        <div className="row mt-3">
          <div className="col">
            <div className="d-flex flex-row justify-content-center flex-wrap align-items-center gap-2 gap-lg-4">
              {genres?.map((genre: any) => (
                <ImageCard
                  key={genre.uuid}
                  model={genre}
                  route={route('genres.show', genre.slug)}
                />
              ))}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
