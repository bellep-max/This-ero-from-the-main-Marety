import React, { useState, useEffect, useCallback } from 'react';
import { Accordion, Tab, Tabs, Offcanvas } from 'react-bootstrap';
import apiClient from '@/api/client';
import { $t } from '@/i18n';
import { useForm } from '@/helpers/useForm';
import { useAuthStore } from '@/stores/auth';
import { removeEmptyObjectsKeys } from '@/Services/FormService';
import { isLogged } from '@/Services/AuthService';
import { addToFavorites, removeFromFavorites } from '@/Services/FavoriteService';
import { addToPlaylist } from '@/Services/PlaylistService';
import ObjectTypes from '@/Enums/ObjectTypes';
import Song from '@/Components/Song';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import Icon from '@/Components/Icons/Icon';
import Adventure from '@/Components/Adventure';
import route from '@/helpers/route';

export default function DiscoverPage() {
  const [loading, setLoading] = useState(true);
  const [songMaxDuration, setSongMaxDuration] = useState(3600);
  const [genres, setGenres] = useState<any[]>([]);
  const [tags, setTags] = useState<any[]>([]);
  const [vocals, setVocals] = useState<any[]>([]);
  const [hasFilters, setHasFilters] = useState(false);
  const [showFilters, setShowFilters] = useState(false);
  const [isLastSongPage, setIsLastSongPage] = useState(false);
  const [isLastAdventurePage, setIsLastAdventurePage] = useState(false);
  const [filteredTags, setFilteredTags] = useState<any[]>([]);
  const [songs, setSongs] = useState<any>({ data: [], meta: {}, links: {} });
  const [adventures, setAdventures] = useState<any>({ data: [], meta: {}, links: {} });

  const [formGenres, setFormGenres] = useState<any[]>([]);
  const [formVocals, setFormVocals] = useState<any[]>([]);
  const [formDuration, setFormDuration] = useState<number[]>([0, 3600]);
  const [formReleasedAt, setFormReleasedAt] = useState<number | null>(null);
  const [formTags, setFormTags] = useState<any[]>([]);

  const authStore = useAuthStore();
  const user = authStore.user;

  const releaseDates = [
    { text: $t('pages.discover.filters.release_date.options.recent'), value: 1 },
    { text: $t('pages.discover.filters.release_date.options.older'), value: 0 },
  ];

  const checkLastPageForType = useCallback((type: string, songsData: any, adventuresData: any) => {
    if (ObjectTypes.getObjectType(type) === ObjectTypes.Adventure) {
      return adventuresData.meta?.current_page === adventuresData.meta?.last_page;
    }
    return songsData.meta?.current_page === songsData.meta?.last_page;
  }, []);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await apiClient.get('/discover');
        const apiData = response.data;
        const newSongs = apiData.songs || { data: [], meta: {}, links: {} };
        const newAdventures = apiData.adventures || { data: [], meta: {}, links: {} };
        setSongs(newSongs);
        setAdventures(newAdventures);
        if (apiData.filters) {
          setGenres(apiData.filters.genres ?? []);
          setTags(apiData.filters.tags ?? []);
          setVocals(apiData.filters.vocals ?? []);
          setFilteredTags(apiData.filters.tags ?? []);
        }
        if (apiData.song_max_duration) {
          setSongMaxDuration(apiData.song_max_duration);
          setFormDuration([0, apiData.song_max_duration]);
        }
        setIsLastSongPage(newSongs.meta?.current_page === newSongs.meta?.last_page);
        setIsLastAdventurePage(newAdventures.meta?.current_page === newAdventures.meta?.last_page);
      } catch (error) {
        console.error('Failed to load page data:', error);
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, []);

  const getFormData = useCallback(() => {
    const data: any = {};
    if (formGenres.length > 0) data.genres = formGenres;
    if (formVocals.length > 0) data.vocals = formVocals;
    if (formDuration[0] !== 0 || formDuration[1] !== songMaxDuration) data.duration = formDuration;
    if (formReleasedAt !== null) data.released_at = formReleasedAt;
    if (formTags.length > 0) data.tags = formTags;
    return data;
  }, [formGenres, formVocals, formDuration, formReleasedAt, formTags, songMaxDuration]);

  const applyFilters = useCallback(async () => {
    try {
      const params = getFormData();
      const response = await apiClient.get('/discover', { params });
      const apiData = response.data;
      const newSongs = apiData.songs || { data: [], meta: {}, links: {} };
      const newAdventures = apiData.adventures || { data: [], meta: {}, links: {} };
      setSongs(newSongs);
      setAdventures(newAdventures);
      setHasFilters(Object.keys(params).length > 0);
      setIsLastSongPage(newSongs.meta?.current_page === newSongs.meta?.last_page);
      setIsLastAdventurePage(newAdventures.meta?.current_page === newAdventures.meta?.last_page);
    } catch (error) {
      console.error('Failed to apply filters:', error);
    }
  }, [getFormData]);

  const resetFilters = useCallback(() => {
    setFormGenres([]);
    setFormVocals([]);
    setFormDuration([0, songMaxDuration]);
    setFormReleasedAt(null);
    setFormTags([]);
    setHasFilters(false);
    const fetchReset = async () => {
      try {
        const response = await apiClient.get('/discover');
        const apiData = response.data;
        if (apiData.songs) setSongs(apiData.songs);
        if (apiData.adventures) setAdventures(apiData.adventures);
      } catch (error) {
        console.error('Failed to reset filters:', error);
      }
    };
    fetchReset();
  }, [songMaxDuration]);

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

  const handleGenreChange = (genreValue: any) => {
    const updated = formGenres.includes(genreValue)
      ? formGenres.filter((g) => g !== genreValue)
      : [...formGenres, genreValue];
    setFormGenres(updated);
  };

  const handleVocalChange = (vocalValue: any) => {
    const updated = formVocals.includes(vocalValue)
      ? formVocals.filter((v) => v !== vocalValue)
      : [...formVocals, vocalValue];
    setFormVocals(updated);
  };

  useEffect(() => {
    if (!loading) {
      applyFilters();
    }
  }, [formGenres, formVocals, formReleasedAt]);

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
                {$t('pages.discover.title')}
              </div>
              <div className="block-description color-light">
                {$t('pages.discover.description')}
              </div>
            </div>
          </div>
        </div>
        <div className="row mt-3">
          <div className="col-12 col-xl-3 pb-3 pb-xl-0">
            <DefaultButton classList="btn-outline d-xl-none" onClick={() => setShowFilters(!showFilters)}>
              {$t('buttons.filters')}
            </DefaultButton>
            <Offcanvas show={showFilters} onHide={() => setShowFilters(false)} placement="start" responsive="xl">
              <Offcanvas.Body>
                <div className="d-flex flex-column w-100 bg-default rounded-5 px-3 py-4 gap-3">
                  <div className="fs-4 font-default">
                    {$t('buttons.filters')}
                  </div>
                  <Accordion alwaysOpen>
                    <Accordion.Item eventKey="0">
                      <Accordion.Header>
                        <Icon icon={['fas', 'cubes-stacked']} classList="color-pink" />
                        <span className="font-default ms-2">{$t('pages.discover.filters.genre')}</span>
                      </Accordion.Header>
                      <Accordion.Body>
                        {genres.map((genre: any) => (
                          <div key={genre.value || genre.id} className="form-check">
                            <input
                              className="form-check-input"
                              type="checkbox"
                              checked={formGenres.includes(genre.value || genre.id)}
                              onChange={() => handleGenreChange(genre.value || genre.id)}
                            />
                            <label className="form-check-label">{genre.text || genre.name}</label>
                          </div>
                        ))}
                      </Accordion.Body>
                    </Accordion.Item>
                    <Accordion.Item eventKey="1">
                      <Accordion.Header>
                        <Icon icon={['fas', 'microphone']} classList="color-pink" />
                        <span className="font-default ms-2">{$t('pages.discover.filters.voice')}</span>
                      </Accordion.Header>
                      <Accordion.Body>
                        {vocals.map((vocal: any) => (
                          <div key={vocal.value || vocal.id} className="form-check">
                            <input
                              className="form-check-input"
                              type="checkbox"
                              checked={formVocals.includes(vocal.value || vocal.id)}
                              onChange={() => handleVocalChange(vocal.value || vocal.id)}
                            />
                            <label className="form-check-label">{vocal.text || vocal.name}</label>
                          </div>
                        ))}
                      </Accordion.Body>
                    </Accordion.Item>
                    <Accordion.Item eventKey="2">
                      <Accordion.Header>
                        <Icon icon={['fas', 'hashtag']} classList="color-pink" />
                        <span className="font-default ms-2">{$t('pages.discover.filters.tags.name')}</span>
                      </Accordion.Header>
                      <Accordion.Body>
                        <div className="d-flex flex-column gap-2">
                          <select
                            className="form-select"
                            multiple
                            value={formTags}
                            onChange={(e) => {
                              const selected = Array.from(e.target.selectedOptions, (opt) => opt.value);
                              setFormTags(selected);
                            }}
                          >
                            {filteredTags.map((tag: any) => (
                              <option key={tag.tag || tag.id} value={tag.tag || tag.value}>
                                {tag.tag || tag.name}
                              </option>
                            ))}
                          </select>
                        </div>
                      </Accordion.Body>
                    </Accordion.Item>
                    <Accordion.Item eventKey="3">
                      <Accordion.Header>
                        <Icon icon={['fas', 'clock']} classList="color-pink" />
                        <span className="font-default ms-2">{$t('pages.discover.filters.duration')}</span>
                      </Accordion.Header>
                      <Accordion.Body>
                        <div className="py-4">
                          <input
                            type="range"
                            className="form-range"
                            min={0}
                            max={songMaxDuration}
                            value={formDuration[1]}
                            onChange={(e) => setFormDuration([formDuration[0], parseInt(e.target.value)])}
                            onMouseUp={() => applyFilters()}
                          />
                        </div>
                      </Accordion.Body>
                    </Accordion.Item>
                    <Accordion.Item eventKey="4">
                      <Accordion.Header>
                        <Icon icon={['fas', 'calendar-days']} classList="color-pink" />
                        <span className="font-default ms-2">{$t('pages.discover.filters.release_date.name')}</span>
                      </Accordion.Header>
                      <Accordion.Body>
                        {releaseDates.map((rd) => (
                          <div key={rd.value} className="form-check">
                            <input
                              className="form-check-input"
                              type="radio"
                              name="released_at"
                              checked={formReleasedAt === rd.value}
                              onChange={() => setFormReleasedAt(rd.value)}
                            />
                            <label className="form-check-label">{rd.text}</label>
                          </div>
                        ))}
                      </Accordion.Body>
                    </Accordion.Item>
                  </Accordion>
                  {hasFilters && (
                    <div className="d-flex flex-row justify-content-center align-items-center">
                      <DefaultButton classList="btn-outline" onClick={resetFilters}>
                        {$t('buttons.cancel')}
                      </DefaultButton>
                    </div>
                  )}
                </div>
              </Offcanvas.Body>
            </Offcanvas>
          </div>
          <div className="col col-xl-9 flex-column">
            <Tabs
              className="tabs-header w-100 px-4"
              fill
            >
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
                          onAddToPlaylist={(e: any) => addToPlaylist(e)}
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
