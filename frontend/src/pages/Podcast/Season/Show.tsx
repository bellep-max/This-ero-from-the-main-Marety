import React, { useState, useEffect, useMemo } from 'react';
import { useParams, Link } from 'react-router-dom';
import apiClient from '@/api/client';
import { useAuthStore } from '@/stores/auth';
import UserLayout from '@/Layouts/UserLayout';

export default function PodcastSeasonShow() {
    const { uuid, seasonUuid } = useParams<{ uuid: string; seasonUuid: string }>();
    const [podcast, setPodcast] = useState<any>(null);
    const [episodes, setEpisodes] = useState<any[]>([]);
    const [season, setSeason] = useState<any>(null);
    const [loading, setLoading] = useState(true);
    const [activeTab, setActiveTab] = useState(0);

    const user = useAuthStore((s) => s.user);
    const isLogged = useAuthStore((s) => s.isLogged);
    const isOwner = useAuthStore((s) => s.isOwner);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await apiClient.get(`/podcasts/${uuid}/seasons/${seasonUuid}`);
                const apiData = response.data;
                setPodcast(apiData.podcast ?? null);
                setEpisodes(apiData.episodes ?? []);
                setSeason(apiData.season ?? null);
            } catch (error) {
                console.error('Failed to load page data:', error);
            } finally {
                setLoading(false);
            }
        };
        fetchData();
    }, [uuid, seasonUuid]);

    const isOwned = useMemo(() => podcast?.user?.uuid ? isOwner(podcast.user.uuid) : false, [podcast, isOwner]);

    if (loading) {
        return (
            <div className="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
                <div className="spinner-border text-light" role="status">
                    <span className="visually-hidden">Loading...</span>
                </div>
            </div>
        );
    }

    if (!podcast) return null;

    const tabs = ['Overview', `Subscribers (${podcast.subscribers_count || 0})`, 'Related'];

    return (
        <UserLayout user={podcast.user}>
            <div className="d-flex flex-column gap-3">
                <div className="row gy-3">
                    <div className="col-12 col-lg-4 d-flex flex-column justify-content-center justify-content-xl-start align-items-start">
                        <img src={podcast.artwork} alt={podcast.title} className="img-fluid rounded-4 border-pink" />
                    </div>
                    <div className="col">
                        <div className="d-flex flex-column gap-3">
                            {isLogged && (
                                <div className="d-flex flex-row gap-4 justify-content-xl-start justify-content-center align-items-start flex-wrap">
                                    <div className="text-center">
                                        <div className="font-default">{season}</div>
                                        <div className="font-merge">Season</div>
                                    </div>
                                    <div className="text-center">
                                        <div className="font-default">{episodes.length}</div>
                                        <div className="font-merge">Episodes</div>
                                    </div>
                                </div>
                            )}
                            <div className="d-flex flex-row align-items-center py-3 border-top border-bottom gap-3">
                                <button className="btn-play lg">
                                    <i className="fas fa-play ms-1" />
                                </button>
                                <div className="d-flex flex-column align-items-start justify-content-start gap-2">
                                    <span className="font-default fs-4">{podcast.title}</span>
                                    <span className="font-merge">{podcast.user?.name}</span>
                                    <div className="d-flex flex-row justify-content-start align-items-center gap-2">
                                        <i className="fas fa-calendar-check color-pink" />
                                        <span className="font-merge">{podcast.created_at}</span>
                                    </div>
                                </div>
                            </div>
                            {podcast.tags && podcast.tags.length > 0 && (
                                <div className="d-flex flex-row gap-2 border-bottom pb-3 flex-wrap">
                                    {podcast.tags.map((tag: any, idx: number) => (
                                        <Link key={idx} to={`/discover?tags=${tag.tag}`} className="btn-default btn-outline btn-narrow">
                                            {tag.tag}
                                        </Link>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
                {podcast.description && (
                    <div className="d-flex flex-row text-start my-3">
                        <span className="font-default">{podcast.description}</span>
                    </div>
                )}
                <ul className="nav nav-tabs tabs-header w-100">
                    {tabs.map((tab, idx) => (
                        <li key={idx} className="nav-item">
                            <button
                                className={`nav-link tab-item default-text-color px-4 fs-5 font-merge ${activeTab === idx ? 'active tab-item-active' : ''}`}
                                onClick={() => setActiveTab(idx)}
                            >
                                {tab}
                            </button>
                        </li>
                    ))}
                </ul>
                <div className="py-4">
                    {activeTab === 0 && (
                        <div className="d-flex flex-column overflow-y-auto playlist-container w-100 justify-content-start">
                            {episodes.map((episode: any) => (
                                <div key={episode.uuid} className="d-flex flex-row align-items-center py-2 border-bottom">
                                    <span className="font-merge">{episode.title}</span>
                                </div>
                            ))}
                        </div>
                    )}
                    {activeTab === 1 && (
                        <div className="row w-100 playlist-container overflow-y-auto">
                            {podcast.subscribers?.map((sub: any, idx: number) => (
                                <div key={idx} className="col-auto p-2">
                                    <span className="font-merge">{sub.name}</span>
                                </div>
                            ))}
                        </div>
                    )}
                    {activeTab === 2 && (
                        <div className="d-flex flex-column w-100 align-items-center justify-content-center text-center">
                            No related podcasts
                        </div>
                    )}
                </div>
            </div>
        </UserLayout>
    );
}
