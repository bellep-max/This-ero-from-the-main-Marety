import React, { useState, useEffect, useMemo } from 'react';
import { useParams } from 'react-router-dom';
import apiClient from '@/api/client';
import { useAuthStore } from '@/stores/auth';

export default function ChannelShow() {
    const { slug } = useParams<{ slug: string }>();
    const [channel, setChannel] = useState<any>(null);
    const [objects, setObjects] = useState<any[]>([]);
    const [pagination, setPagination] = useState<any>(null);
    const [loading, setLoading] = useState(true);

    const user = useAuthStore((s) => s.user);
    const isLogged = useAuthStore((s) => s.isLogged);

    const lastPage = useMemo(
        () => !pagination || pagination.current_page >= pagination.last_page,
        [pagination]
    );

    const fetchData = async (page = 1) => {
        try {
            const response = await apiClient.get(`/channels/${slug}`, { params: { page } });
            const apiData = response.data;
            setChannel(apiData.channel ?? null);
            if (page === 1) {
                setObjects(apiData.objects ?? []);
            } else {
                setObjects((prev) => [...prev, ...(apiData.objects ?? [])]);
            }
            setPagination(apiData.pagination ?? null);
        } catch (error) {
            console.error('Failed to load page data:', error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchData();
    }, [slug]);

    const loadMore = () => {
        if (pagination) {
            fetchData(pagination.current_page + 1);
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
                            <div className="block-title color-light text-truncate">{channel?.title}</div>
                            <div className="block-description color-light">{channel?.description}</div>
                        </div>
                    </div>
                </div>
                <div className="row mt-3">
                    <div className="col flex-column">
                        {objects.map((obj: any) => (
                            <div key={obj.uuid} className="d-flex flex-row align-items-center py-2">
                                <span className="font-merge">{obj.title}</span>
                            </div>
                        ))}
                        <button
                            className="btn btn-default btn-outline mt-2 mx-auto d-block"
                            onClick={loadMore}
                            disabled={lastPage}
                        >
                            Load More
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}
