import React, { useState, useEffect, useMemo } from 'react';
import { Tab, Tabs } from 'react-bootstrap';
import apiClient from '@/api/client';
import { $t } from '@/i18n';
import { isNotEmpty } from '@/Services/MiscService';
import ImageLinkTypes from '@/Enums/ImageLinkTypes';
import SongBlockCarousel from '@/Components/Carousels/SongBlockCarousel';
import MainPageBlock from '@/Components/Sections/MainPageBlock';
import ImageCarousel from '@/Components/Carousels/ImageCarousel';
import TestimonialCarousel from '@/Components/Carousels/TestimonialCarousel';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import GenreBlockCarousel from '@/Components/Carousels/GenreBlockCarousel';
import indexImage from '@/assets/images/homepage.webp';
import route from '@/helpers/route';

export default function IndexPage() {
  const [newAudios, setNewAudios] = useState<any>(null);
  const [popularAudios, setPopularAudios] = useState<any>(null);
  const [genres, setGenres] = useState<any>(null);
  const [adventures, setAdventures] = useState<any>(null);
  const [posts, setPosts] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  const testimonials = useMemo(() => {
    const items: any[] = [];
    for (let i = 0; i < 10; i++) {
      items.push({
        title: 'Lorem!',
        description: `Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
          incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
          exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.`,
        rating: Math.floor(Math.random() * 5),
      });
    }
    return items;
  }, []);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await apiClient.get('/homepage');
        const apiData = response.data;
        setNewAudios(apiData.newAudios ?? null);
        setPopularAudios(apiData.popularAudios ?? null);
        setGenres(apiData.genres ?? null);
        setAdventures(apiData.adventures ?? null);
        setPosts(apiData.posts ?? null);
      } catch (error) {
        console.error('Failed to load page data:', error);
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, []);

  const hasContent = (contentBlockName: string) => {
    if (contentBlockName === 'newAudios') return true;
    if (contentBlockName === 'popularAudios') return isNotEmpty(popularAudios);
    if (contentBlockName === 'posts') return isNotEmpty(posts);
    if (contentBlockName === 'adventures') return isNotEmpty(adventures);
    return isNotEmpty(genres);
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
    <>
      <div className="container-fluid p-0">
        <div className="d-flex flex-row justify-content-between align-items-center">
          <div className="d-flex flex-column justify-content-center align-items-start p-2 ps-lg-8">
            <div className="d-flex flex-column gap-2 gap-lg-5 ms-lg-5">
              <div className="block-title default-text-color text-lg-start text-center main_info__left-section">
                Lorem ipsum dolor
              </div>
              <div className="font-merge default-text-color lh-lg">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                labore et dolore magna aliqua.
              </div>
              <div className="d-flex flex-row align-items-center justify-content-center justify-content-lg-start gap-4">
                <DefaultButton classList="btn-pink btn-wide"> Lorem ipsum </DefaultButton>
                <DefaultButton classList="btn-outline btn-wide"> Lorem ipsum </DefaultButton>
              </div>
            </div>
          </div>
          <img src={indexImage} alt="Login Icon" className="d-none d-lg-inline-block" />
        </div>
      </div>

      {hasContent('newAudios') && (
        <MainPageBlock
          hasIcon
          title={$t('pages.home.new_audios.title')}
          description={$t('pages.home.new_audios.description')}
          link={route('channels.show', 'new-audios')}
        >
          <Tabs className="tabs-header gap-lg-4 px-lg-4">
            {newAudios && Object.entries(newAudios).map(([name, data]: [string, any], index: number) => (
              <Tab
                key={index}
                eventKey={name}
                title={$t(`pages.home.new_audios.categories.${name}`)}
                tabClassName="tab-item default-text-color px-4"
              >
                <div className="py-4">
                  <SongBlockCarousel items={data} />
                </div>
              </Tab>
            ))}
          </Tabs>
        </MainPageBlock>
      )}

      {hasContent('adventures') && (
        <MainPageBlock
          hasIcon
          title={$t('pages.home.adventures.title')}
          description={$t('pages.home.adventures.description')}
        >
          <SongBlockCarousel items={adventures} />
        </MainPageBlock>
      )}

      {hasContent('genres') && (
        <MainPageBlock
          title={$t('pages.home.genres.title')}
          description={$t('pages.home.genres.description')}
          gradientBackground
          link={route('genres.index')}
          classList="rounded-5"
        >
          <ImageCarousel items={genres} type={ImageLinkTypes.Genre} />
        </MainPageBlock>
      )}

      <MainPageBlock
        hasIcon
        title={$t('pages.home.popular_audios.title')}
        description={$t('pages.home.popular_audios.description')}
        link={route('channels.show', 'new-audios')}
      >
        <Tabs className="tabs-header tab-item px-lg-4 gap-lg-4">
          {popularAudios && Object.entries(popularAudios).map(([name, genreData]: [string, any]) => (
            <Tab
              key={name}
              eventKey={name}
              title={$t(`pages.home.popular_audios.categories.${name}`)}
            >
              <div className="py-4">
                <GenreBlockCarousel items={genreData} />
              </div>
            </Tab>
          ))}
        </Tabs>
      </MainPageBlock>

      {hasContent('posts') && (
        <MainPageBlock
          title="New Posts"
          link={route('posts.index')}
          gradientBackground
          classList="rounded-5"
        >
          <ImageCarousel items={posts} type={ImageLinkTypes.Post} />
        </MainPageBlock>
      )}

      <div className="bg-default container-fluid">
        <div className="container py-5 d-flex flex-column gap-5">
          <div className="d-flex flex-column align-items-center">
            <div className="h2 font-default fw-bold">
              {$t('pages.home.testimonials.title')}
            </div>
            <div className="block-description">
              {$t('pages.home.testimonials.description')}
            </div>
          </div>
          <TestimonialCarousel items={testimonials} />
        </div>
      </div>
    </>
  );
}
