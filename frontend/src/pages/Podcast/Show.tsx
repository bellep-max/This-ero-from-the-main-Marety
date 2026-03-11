import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import apiClient from '@/api/client';

export default function PodcastShow() {
    const { uuid } = useParams<{ uuid: string }>();
    const [podcast, setPodcast] = useState<any>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await apiClient.get(`/podcasts/${uuid}`);
                const apiData = response.data;
                setPodcast(apiData.podcast ?? null);
            } catch (error) {
                console.error('Failed to load page data:', error);
            } finally {
                setLoading(false);
            }
        };
        fetchData();
    }, [uuid]);

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

    return (
        <div className="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
            <div className="container">
                <div className="row">
                    <div className="col text-start">
                        <div className="d-block">
                            <div className="block-title color-light text-truncate">{podcast.title}</div>
                            <Link to="/podcasts" className="block-description color-light text-decoration-none">
                                See All Podcasts
                            </Link>
                        </div>
                    </div>
                </div>
                <div className="row mt-3">
                    <div className="col">
                        <div className="d-flex flex-row justify-content-center flex-wrap align-items-center gap-2 gap-lg-4">
                            {Array.isArray(podcast.seasons) ? (
                                podcast.seasons.map((n: number) => (
                                    <Link key={n} to={`/podcasts/${podcast.uuid}/seasons/${n}`} className="text-decoration-none">
                                        <div className="d-flex flex-column align-items-center gap-2">
                                            <img
                                                src={podcast.artwork || podcast.image}
                                                alt={`Season ${n}`}
                                                className="img-fluid rounded-4"
                                                style={{ width: 180, height: 180, objectFit: 'cover' }}
                                            />
                                            <span className="font-merge text-center color-light">Season {n}</span>
                                        </div>
                                    </Link>
                                ))
                            ) : (
                                typeof podcast.seasons === 'number' && Array.from({ length: podcast.seasons }, (_, i) => i + 1).map((n) => (
                                    <Link key={n} to={`/podcasts/${podcast.uuid}/seasons/${n}`} className="text-decoration-none">
                                        <div className="d-flex flex-column align-items-center gap-2">
                                            <img
                                                src={podcast.artwork || podcast.image}
                                                alt={`Season ${n}`}
                                                className="img-fluid rounded-4"
                                                style={{ width: 180, height: 180, objectFit: 'cover' }}
                                            />
                                            <span className="font-merge text-center color-light">Season {n}</span>
                                        </div>
                                    </Link>
                                ))
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
