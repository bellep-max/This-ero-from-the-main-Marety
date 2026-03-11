import React, { useState, useEffect, useMemo } from 'react';
import { useParams, Link } from 'react-router-dom';
import apiClient from '@/api/client';
import { useAuthStore } from '@/stores/auth';
import UserLayout from '@/Layouts/UserLayout';

export default function PlaylistShow() {
    const { uuid } = useParams<{ uuid: string }>();
    const [playlist, setPlaylist] = useState<any>(null);
    const [following, setFollowing] = useState<any>(null);
    const [related, setRelated] = useState<any>(null);
    const [comments, setComments] = useState<any>(null);
    const [loading, setLoading] = useState(true);
    const [isSubscribed, setIsSubscribed] = useState(false);
    const [activeTab, setActiveTab] = useState(0);

    const user = useAuthStore((s) => s.user);
    const isLogged = useAuthStore((s) => s.isLogged);

    const ownPlaylist = useMemo(() => playlist?.user?.own_profile, [playlist]);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await apiClient.get(`/playlists/${uuid}`);
                const apiData = response.data;
                setPlaylist(apiData.playlist ?? null);
                setFollowing(apiData.following ?? null);
                setRelated(apiData.related ?? null);
                setComments(apiData.comments ?? null);
                if (apiData.playlist) {
                    setIsSubscribed(apiData.playlist.favorite ?? false);
                }
            } catch (error) {
                console.error('Failed to load page data:', error);
            } finally {
                setLoading(false);
            }
        };
        fetchData();
    }, [uuid]);

    const updateComments = async () => {
        try {
            const response = await apiClient.get(`/playlists/${uuid}`);
            const apiData = response.data;
            if (apiData.comments) setComments(apiData.comments);
        } catch (error) {
            console.error('Failed to refresh comments:', error);
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

    if (!playlist) return null;

    const tabs = ['Overview', 'Subscribers', 'Collaborators', 'Related'];

    return (
        <UserLayout user={playlist.user}>
            <div className="d-flex flex-column gap-3">
                <div className="row gy-3">
                    <div className="col-12 col-lg-4 d-flex justify-content-center justify-content-xl-start align-items-start">
                        <img src={playlist.artwork} alt={playlist.title} className="img-fluid rounded-4 border-pink" />
                    </div>
                    <div className="col">
                        <div className="d-flex flex-column gap-4">
                            {isLogged && (
                                <div className="d-flex flex-row gap-4 justify-content-xl-start justify-content-center align-items-start flex-wrap">
                                    <div className="text-center">
                                        <div className="font-default">{playlist.songs_count}</div>
                                        <div className="font-merge">Songs</div>
                                    </div>
                                    <div className="text-center">
                                        <div className="font-default">{playlist.subscribers_count}</div>
                                        <div className="font-merge">Subscribers</div>
                                    </div>
                                    <div className="text-center">
                                        <div className="font-default">{playlist.duration}</div>
                                        <div className="font-merge">Duration</div>
                                    </div>
                                </div>
                            )}
                            <div className="d-flex flex-row align-items-center py-3 border-top border-bottom gap-3">
                                <button className="btn-play lg">
                                    <i className="fas fa-play ms-1" />
                                </button>
                                <div className="d-flex flex-column align-items-start justify-content-start gap-2">
                                    <span className="font-default fs-4">{playlist.title}</span>
                                    <span className="font-merge">{playlist.user?.name}</span>
                                    <div className="d-flex flex-row justify-content-start align-items-center gap-2">
                                        <i className="fas fa-calendar-check color-pink" />
                                        <span className="font-merge">{playlist.created_at}</span>
                                    </div>
                                </div>
                            </div>
                            {playlist.tags && playlist.tags.length > 0 && (
                                <div className="d-flex flex-row gap-2 border-bottom py-3">
                                    {playlist.tags.map((tag: any, idx: number) => (
                                        <Link key={idx} to={`/discover?tags=${tag.tag}`} className="btn-default btn-outline btn-narrow">
                                            {tag.tag}
                                        </Link>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
                {playlist.description && (
                    <div className="d-flex flex-row text-start my-3">
                        <span className="font-default">{playlist.description}</span>
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
                                {idx === 1 && ` (${playlist.subscribers_count || 0})`}
                                {idx === 2 && ` (${playlist.collaborators_count || 0})`}
                            </button>
                        </li>
                    ))}
                </ul>
                <div className="py-4">
                    {activeTab === 0 && (
                        <div className="row playlist-container">
                            <div className="col-12 col-lg-7 flex-column overflow-y-auto">
                                {playlist.songs?.map((song: any) => (
                                    <div key={song.uuid} className="d-flex flex-row align-items-center py-2">
                                        <span className="font-merge">{song.title}</span>
                                    </div>
                                ))}
                            </div>
                            <div className="col-12 col-lg-5">
                                <h3 className="tab-item text-center">Recent Activity</h3>
                            </div>
                        </div>
                    )}
                    {activeTab === 1 && (
                        <div className="row w-100 playlist-container overflow-y-auto">
                            {playlist.subscribers?.map((sub: any, idx: number) => (
                                <div key={idx} className="col-auto p-2">
                                    <span className="font-merge">{sub.name}</span>
                                </div>
                            ))}
                        </div>
                    )}
                    {activeTab === 2 && (
                        <div className="row w-100 playlist-container overflow-y-auto">
                            {playlist.collaborators?.length ? (
                                playlist.collaborators.map((collab: any, idx: number) => (
                                    <div key={idx} className="col-auto p-2">
                                        <span className="font-merge">{collab.name}</span>
                                    </div>
                                ))
                            ) : (
                                <div className="d-flex flex-row w-100 justify-content-center align-items-center">
                                    <button className="btn btn-default btn-pink" disabled={!playlist.collaboration}>
                                        Invite Collaborators
                                    </button>
                                </div>
                            )}
                        </div>
                    )}
                    {activeTab === 3 && (
                        <div className="row w-100 playlist-container overflow-y-auto">
                            {related && related.length > 0 ? (
                                related.map((rel: any, idx: number) => (
                                    <div key={idx} className="col-auto p-2">
                                        <span className="font-merge">{rel.title}</span>
                                    </div>
                                ))
                            ) : (
                                <div className="d-flex flex-column w-100 align-items-center justify-content-center text-center">
                                    No related playlists
                                </div>
                            )}
                        </div>
                    )}
                </div>
            </div>
        </UserLayout>
    );
}
