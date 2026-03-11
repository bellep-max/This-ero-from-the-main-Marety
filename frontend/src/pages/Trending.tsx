import React, { useState, useEffect, useMemo } from 'react';
import { Tab, Tabs } from 'react-bootstrap';
import apiClient from '@/api/client';
import { $t } from '@/i18n';
import SongBlockCarousel from '@/Components/Carousels/SongBlockCarousel';
import MainPageBlock from '@/Components/Sections/MainPageBlock';
import route from '@/helpers/route';

export default function TrendingPage() {
  const [popularAudios, setPopularAudios] = useState<any>(null);
  const [topGenre, setTopGenre] = useState<any>(null);
  const [topFemale, setTopFemale] = useState<any>(null);
  const [topMale, setTopMale] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await apiClient.get('/trending');
        const apiData = response.data;
        setPopularAudios(apiData.popularAudios ?? null);
        setTopGenre(apiData.topGenre ?? null);
        setTopFemale(apiData.topFemale ?? null);
        setTopMale(apiData.topMale ?? null);
      } catch (error) {
        console.error('Failed to load page data:', error);
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, []);

  const hasContent = (contentBlockName: string) => {
    if (contentBlockName === 'top20') {
      return (topFemale?.length ?? 0) > 0 || (topMale?.length ?? 0) > 0;
    } else if (contentBlockName === 'popularAudios') {
      return (popularAudios?.length ?? 0) > 0;
    } else if (contentBlockName === 'topGenre') {
      return (topGenre?.length ?? 0) > 0;
    } else if (contentBlockName === 'topFemale') {
      return (topFemale?.length ?? 0) > 0;
    }
    return (topMale?.length ?? 0) > 0;
  };

  const top20 = useMemo(() => [...(topFemale ?? []), ...(topMale ?? [])], [topFemale, topMale]);

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
    <div className="min-vh-100 d-flex flex-column gap-5">
      <div className="py-3 p-md-5 p-lg-6">
        <div className="container">
          <div className="row">
            <div className="col text-start">
              <div className="d-block">
                <div className="block-title text-truncate">
                  {$t('pages.trending.title')}
                </div>
              </div>
            </div>
          </div>
          {hasContent('top20') && (
            <MainPageBlock
              hasIcon
              title={$t('pages.trending.top_20.title')}
              description={$t('pages.trending.top_20.description')}
            >
              <SongBlockCarousel items={top20} />
            </MainPageBlock>
          )}
        </div>
      </div>

      {hasContent('topGenre') && (
        <MainPageBlock
          gradientBackground
          classList="bg-rounded"
          title={$t('pages.trending.top_genre.title')}
        >
          <SongBlockCarousel items={topGenre} />
        </MainPageBlock>
      )}

      {hasContent('popularAudios') && (
        <MainPageBlock
          hasIcon
          title={$t('pages.trending.new_audios.title')}
          description={$t('pages.trending.new_audios.description')}
          link={route('channel', 'new-audios')}
        >
          <Tabs className="tabs-header tab-item px-4 gap-4">
            {popularAudios?.map((audioType: any, i: number) => (
              <Tab
                key={i}
                eventKey={`audio-${i}`}
                title={$t(`pages.trending.popular_audios.categories.${audioType.tab_title}`)}
              >
                <div className="py-4">
                  <SongBlockCarousel items={audioType.data?.data} />
                </div>
              </Tab>
            ))}
          </Tabs>
        </MainPageBlock>
      )}
    </div>
  );
}
