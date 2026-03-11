import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import apiClient from '@/api/client';
import { useAuthStore } from '@/stores/auth';

export default function PodcastIndex() {
    const [podcasts, setPodcasts] = useState<any[]>([]);
    const [regions, setRegions] = useState<any>(null);
    const [loading, setLoading] = useState(true);
    const [isLastPage, setIsLastPage] = useState(false);
    const [pagination, setPagination] = useState<any>(null);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await apiClient.get('/podcasts');
                const apiData = response.data;
                setRegions(apiData.regions ?? null);
                setPodcasts(apiData.podcasts?.data ?? apiData.podcasts ?? []);
                setPagination(apiData.pagination ?? null);
                if (apiData.podcasts?.meta) {
                    setIsLastPage(apiData.podcasts.meta.current_page >= apiData.podcasts.meta.last_page);
                }
            } catch (error) {
                console.error('Failed to load page data:', error);
            } finally {
                setLoading(false);
            }
        };
        fetchData();
    }, []);

    const loadMore = async () => {
        if (!pagination || isLastPage) return;
        try {
            const nextPage = (pagination.current_page || 1) + 1;
            const response = await apiClient.get('/podcasts', { params: { page: nextPage } });
            const apiData = response.data;
            const newPodcasts = apiData.podcasts?.data ?? apiData.podcasts ?? [];
            setPodcasts((prev) => [...prev, ...newPodcasts]);
            setPagination(apiData.pagination ?? null);
            if (apiData.podcasts?.meta) {
                setIsLastPage(apiData.podcasts.meta.current_page >= apiData.podcasts.meta.last_page);
            }
        } catch (error) {
            console.error('Failed to load more podcasts:', error);
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
                            <div className="block-title color-light text-truncate">Podcasts</div>
                            <div className="block-description color-light">Browse all podcasts</div>
                        </div>
                    </div>
                </div>
                <div className="row mt-3">
                    <div className="col flex-column">
                        <div className="d-flex flex-row justify-content-center flex-wrap align-items-center gap-2 gap-lg-4">
                            {podcasts.map((podcast: any) => (
                                <Link key={podcast.uuid} to={`/podcasts/${podcast.uuid}`} className="text-decoration-none">
                                    <div className="d-flex flex-column align-items-center gap-2">
                                        <img
                                            src={podcast.artwork || podcast.image}
                                            alt={podcast.title}
                                            className="img-fluid rounded-4"
                                            style={{ width: 180, height: 180, objectFit: 'cover' }}
                                        />
                                        <span className="font-merge text-center color-light">{podcast.title}</span>
                                    </div>
                                </Link>
                            ))}
                        </div>
                        <button
                            className="btn btn-default btn-outline mt-2 mx-auto d-block"
                            onClick={loadMore}
                            disabled={isLastPage}
                        >
                            Load More
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}
