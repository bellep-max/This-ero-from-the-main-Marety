import React, { useState, useEffect, useMemo } from 'react';
import { useParams, Link } from 'react-router-dom';
import apiClient from '@/api/client';
import { useAuthStore } from '@/stores/auth';
import UserLayout from '@/Layouts/UserLayout';

export default function TrackShow() {
    const { uuid } = useParams<{ uuid: string }>();
    const [track, setTrack] = useState<any>(null);
    const [user, setUser] = useState<any>(null);
    const [comments, setComments] = useState<any>(null);
    const [loading, setLoading] = useState(true);

    const loggedUser = useAuthStore((s) => s.user);
    const isLogged = useAuthStore((s) => s.isLogged);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await apiClient.get(`/songs/${uuid}`);
                const apiData = response.data;
                setTrack(apiData.track ?? null);
                setUser(apiData.user ?? null);
                setComments(apiData.comments ?? null);
            } catch (error) {
                console.error('Failed to load page data:', error);
            } finally {
                setLoading(false);
            }
        };
        fetchData();
    }, [uuid]);

    const isOwned = useMemo(() => loggedUser && loggedUser.uuid === user?.uuid, [loggedUser, user]);

    const updateComments = async () => {
        try {
            const response = await apiClient.get(`/songs/${uuid}`);
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

    if (!track) return null;

    return (
        <UserLayout user={user} slides={track.slides}>
            <div className="row gy-3">
                <div className="col-12 col-lg-4 d-flex flex-column justify-content-center justify-content-xl-start align-items-start">
                    <img src={track.artwork} alt={track.title} className="img-fluid rounded-4 border-pink" />
                </div>
                <div className="col">
                    <div className="d-flex flex-column gap-3">
                        {isLogged && (
                            <div className="d-flex flex-row gap-4 justify-content-xl-start justify-content-center align-items-start flex-wrap">
                                <div className="text-center">
                                    <div className="font-default">{track.total_plays}</div>
                                    <div className="font-merge">Total Plays</div>
                                </div>
                                <div className="text-center">
                                    <div className="font-default">{track.likes}</div>
                                    <div className="font-merge">Likes</div>
                                </div>
                            </div>
                        )}
                        <div className="d-flex flex-row align-items-center py-3 border-top border-bottom gap-3">
                            <div className="d-block">
                                <button className="btn-play lg" disabled={!track.streamable && !isLogged}>
                                    <i className="fas fa-play ms-1" />
                                </button>
                            </div>
                            <div className="d-flex flex-column align-items-start justify-content-start gap-2">
                                <span className="font-default fs-4">{track.title}</span>
                                <span className="font-merge">{track.user?.name}</span>
                                <div className="d-flex flex-row justify-content-start align-items-center gap-2">
                                    <i className="fas fa-calendar-check color-pink" />
                                    <span className="font-merge">{track.created_at}</span>
                                </div>
                            </div>
                        </div>
                        {track.tags && track.tags.length > 0 && (
                            <div className="d-flex flex-row gap-2 border-bottom pb-3 flex-wrap">
                                {track.tags.map((tag: any, idx: number) => (
                                    <Link key={idx} to={`/discover?tags=${tag.tag}`} className="btn-default btn-outline btn-narrow">
                                        {tag.tag}
                                    </Link>
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </div>
            {track.description && (
                <div className="d-flex flex-row text-start my-3">
                    <span className="font-default">{track.description}</span>
                </div>
            )}
        </UserLayout>
    );
}
