import React, { useState, useEffect, useCallback, useMemo } from 'react';
import { useParams } from 'react-router-dom';
import { Tab, Tabs } from 'react-bootstrap';
import apiClient from '@/api/client';
import { $t } from '@/i18n';
import { useAuthStore } from '@/stores/auth';
import { isLogged } from '@/Services/AuthService';
import { addToFavorites, removeFromFavorites } from '@/Services/FavoriteService';
import ObjectTypes from '@/Enums/ObjectTypes';
import Adventure from '@/Components/Adventure';
import Song from '@/Components/Song';
import DefaultLink from '@/Components/Links/DefaultLink';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import route from '@/helpers/route';

export default function GenreShowPage() {
  const { slug } = useParams<{ slug: string }>();
  const [genre, setGenre] = useState<any>(null);
  const [slides, setSlides] = useState<any>(null);
  const [channels, setChannels] = useState<any>(null);
  const [related, setRelated] = useState<any>(null);
  const [songs, setSongs] = useState<any>({ data: [], meta: {}, links: {} });
  const [adventures, setAdventures] = useState<any>({ data: [], meta: {}, links: {} });
  const [isLastSongPage, setIsLastSongPage] = useState(false);
  const [isLastAdventurePage, setIsLastAdventurePage] = useState(false);
  const [loading, setLoading] = useState(true);

  const authStore = useAuthStore();
  const user = authStore.user;

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await apiClient.get(`/genres/${slug}`);
        const apiData = response.data;
        setGenre(apiData.genre ?? null);
        setSlides(apiData.slides ?? null);
        setChannels(apiData.channels ?? null);
        setRelated(apiData.related ?? null);
        const songsData = apiData.songs || { data: [], meta: {}, links: {} };
        const adventuresData = apiData.adventures || { data: [], meta: {}, links: {} };
        setSongs(songsData);
        setAdventures(adventuresData);
        setIsLastSongPage(songsData.meta?.current_page === songsData.meta?.last_page);
        setIsLastAdventurePage(adventuresData.meta?.current_page === adventuresData.meta?.last_page);
      } catch (error) {
        console.error('Failed to load page data:', error);
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, [slug]);

  const loadMore = useCallback(async (type: string) => {
    const targetData = type === ObjectTypes.Song ? songs : adventures;
    const nextUrl = targetData.links?.next;
    if (!nextUrl) return;

    try {
      const response = await apiClient.get(nextUrl);
      const apiData = response.data;
      const newData = type === ObjectTypes.Song ? apiData.songs : apiData.adventures;
      if (newData) {
        if (type === ObjectTypes.Song) {
          const updated = {
            data: [...songs.data, ...(newData.data || [])],
            links: newData.links,
            meta: newData.meta,
          };
          setSongs(updated);
          setIsLastSongPage(updated.meta?.current_page === updated.meta?.last_page);
        } else {
          const updated = {
            data: [...adventures.data, ...(newData.data || [])],
            links: newData.links,
            meta: newData.meta,
          };
          setAdventures(updated);
          setIsLastAdventurePage(updated.meta?.current_page === updated.meta?.last_page);
        }
      }
    } catch (error) {
      console.error('Failed to load more:', error);
    }
  }, [songs, adventures]);

  if (loading) {
    return (
      <div className="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
        <div className="spinner-border text-light" role="status">
          <span className="visually-hidden">Loading...</span>
        </div>
      </div>
    );
  }

  if (!genre) return null;

  return (
    <div className="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
      <div className="container">
        <div className="row">
          <div className="col text-start">
            <div className="d-block">
              <div className="block-title color-light text-truncate">
                {genre.title}
              </div>
              <DefaultLink
                classList="block-description color-light text-decoration-none"
                link={route('genres.index')}
              >
                {$t('pages.genres.see_all')}
              </DefaultLink>
            </div>
          </div>
        </div>
        <div className="row mt-3">
          <div className="col">
            <Tabs className="tabs-header w-100 px-4" fill>
              <Tab
                eventKey="songs"
                title={$t('pages.discover.tabs.songs', { count: songs.meta?.total ?? 0 })}
                tabClassName="tab-item color-light px-4 fs-5 font-merge"
              >
                <div className="py-4">
                  <div className="row">
                    <div className="col flex-column overflow-y-auto p-1">
                      {songs.data?.map((song: any) => (
                        <Song
                          key={song.uuid}
                          song={song}
                          canView={isLogged}
                          isOwned={song.user?.uuid === user?.uuid}
                          onAddToFavorites={(e: any) => addToFavorites(e, user?.uuid)}
                          onRemoveFromFavorites={(e: any) => removeFromFavorites(e, user?.uuid)}
                        />
                      ))}
                      <DefaultButton
                        classList="btn-outline mt-2 mx-auto"
                        onClick={() => loadMore(ObjectTypes.Song)}
                        disabled={isLastSongPage}
                      >
                        {$t('buttons.load_more')}
                      </DefaultButton>
                    </div>
                  </div>
                </div>
              </Tab>
              <Tab
                eventKey="adventures"
                title={$t('pages.discover.tabs.adventures', { count: adventures.meta?.total ?? 0 })}
                tabClassName="tab-item color-light px-4 fs-5 font-merge"
              >
                <div className="py-4">
                  <div className="row">
                    <div className="col flex-column overflow-y-auto p-1">
                      {adventures.data?.map((adventure: any) => (
                        <Adventure
                          key={adventure.uuid}
                          adventure={adventure}
                          canView={isLogged}
                          isOwned={adventure.user?.uuid === user?.uuid}
                          onAddToFavorites={(e: any) => addToFavorites(e, user?.uuid)}
                          onRemoveFromFavorites={(e: any) => removeFromFavorites(e, user?.uuid)}
                        />
                      ))}
                      <DefaultButton
                        classList="btn-outline mt-2 mx-auto"
                        onClick={() => loadMore(ObjectTypes.Adventure)}
                        disabled={isLastAdventurePage}
                      >
                        {$t('buttons.load_more')}
                      </DefaultButton>
                    </div>
                  </div>
                </div>
              </Tab>
            </Tabs>
          </div>
        </div>
      </div>
    </div>
  );
}
