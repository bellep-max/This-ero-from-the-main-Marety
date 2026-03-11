import React, { useState, useEffect, useMemo } from 'react';
import { useParams, Link } from 'react-router-dom';
import apiClient from '@/api/client';
import { useAuthStore } from '@/stores/auth';
import UserLayout from '@/Layouts/UserLayout';

export default function AdventuresShow() {
    const { uuid } = useParams<{ uuid: string }>();
    const [adventure, setAdventure] = useState<any>(null);
    const [user, setUser] = useState<any>(null);
    const [comments, setComments] = useState<any>(null);
    const [loading, setLoading] = useState(true);

    const loggedUser = useAuthStore((s) => s.user);
    const isLogged = useAuthStore((s) => s.isLogged);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await apiClient.get(`/adventures/${uuid}`);
                const apiData = response.data;
                setAdventure(apiData.adventure ?? null);
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

    const totalTracks = useMemo(() => {
        if (!adventure?.children) return 0;
        let total = adventure.children.length;
        for (const root of adventure.children) {
            if (root.children && root.children.length > 0) {
                total += root.children.length;
            }
        }
        return total + 1;
    }, [adventure]);

    const updateComments = async () => {
        try {
            const response = await apiClient.get(`/adventures/${uuid}`);
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

    if (!adventure) return null;

    return (
        <UserLayout user={user}>
            <div className="row gy-3">
                <div className="col-12 col-lg-4 d-flex flex-column gap-3 justify-content-center justify-content-xl-start align-items-start">
                    <img src={adventure.artwork} alt={adventure.title} className="img-fluid rounded-4 border-pink" />
                </div>
                <div className="col">
                    <div className="d-flex flex-column gap-3">
                        {isLogged && (
                            <div className="d-flex flex-row gap-4 justify-content-xl-start justify-content-center align-items-start flex-wrap">
                                <div className="text-center">
                                    <div className="font-default">{totalTracks}</div>
                                    <div className="font-merge">Total Tracks</div>
                                </div>
                                <div className="text-center">
                                    <div className="font-default">{adventure.likes}</div>
                                    <div className="font-merge">Likes</div>
                                </div>
                            </div>
                        )}
                        <div className="d-flex flex-row align-items-center py-3 border-top border-bottom gap-3">
                            <div className="d-block">
                                <button className="btn-play lg" disabled={!isLogged}>
                                    <i className="fas fa-play ms-1" />
                                </button>
                            </div>
                            <div className="d-flex flex-column align-items-start justify-content-start gap-2">
                                <span className="font-default fs-4">{adventure.title}</span>
                                <span className="font-merge">{adventure.user?.name}</span>
                                <div className="d-flex flex-row justify-content-start align-items-center gap-2">
                                    <i className="fas fa-calendar-check color-pink" />
                                    <span className="font-merge">{adventure.created_at}</span>
                                </div>
                            </div>
                        </div>
                        {adventure.tags && adventure.tags.length > 0 && (
                            <div className="d-flex flex-row gap-2 border-bottom pb-3 flex-wrap">
                                {adventure.tags.map((tag: any, idx: number) => (
                                    <Link key={idx} to={`/discover?tags=${tag.tag}`} className="btn-default btn-outline btn-narrow">
                                        {tag.tag}
                                    </Link>
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </div>
            {adventure.description && (
                <div className="d-flex flex-row text-start my-3">
                    <span className="font-default">{adventure.description}</span>
                </div>
            )}
        </UserLayout>
    );
}
