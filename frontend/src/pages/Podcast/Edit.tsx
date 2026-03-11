import React, { useState, useEffect } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import apiClient from '@/api/client';
import { useAuthStore } from '@/stores/auth';
import UserLayout from '@/Layouts/UserLayout';

export default function PodcastEdit() {
    const { uuid } = useParams<{ uuid: string }>();
    const navigate = useNavigate();
    const [podcast, setPodcast] = useState<any>(null);
    const [loading, setLoading] = useState(true);

    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [isVisible, setIsVisible] = useState(true);
    const [allowComments, setAllowComments] = useState(true);
    const [allowDownload, setAllowDownload] = useState(false);
    const [explicit, setExplicit] = useState(false);
    const [artworkPreview, setArtworkPreview] = useState<string>('');
    const [artworkFile, setArtworkFile] = useState<File | null>(null);
    const [artworkSelected, setArtworkSelected] = useState(false);
    const [submitting, setSubmitting] = useState(false);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await apiClient.get(`/podcasts/${uuid}/edit`);
                const apiData = response.data;
                const podcastData = apiData.podcast ?? null;
                setPodcast(podcastData);
                if (podcastData) {
                    setTitle(podcastData.title || '');
                    setDescription(podcastData.description || '');
                    setIsVisible(podcastData.is_visible ?? true);
                    setAllowComments(podcastData.allow_comments ?? true);
                    setAllowDownload(podcastData.allow_download ?? false);
                    setExplicit(podcastData.explicit ?? false);
                    setArtworkPreview(podcastData.artwork || '/assets/images/podcast.png');
                }
            } catch (error) {
                console.error('Failed to load page data:', error);
            } finally {
                setLoading(false);
            }
        };
        fetchData();
    }, [uuid]);

    const handleArtworkChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0] || null;
        setArtworkFile(file);
        if (file) {
            setArtworkPreview(URL.createObjectURL(file));
            setArtworkSelected(true);
        } else {
            setArtworkPreview('/assets/images/podcast.png');
            setArtworkSelected(false);
        }
    };

    const resetArtwork = () => {
        setArtworkFile(null);
        setArtworkPreview(podcast?.artwork || '/assets/images/podcast.png');
        setArtworkSelected(false);
    };

    const handleSubmit = async () => {
        if (submitting) return;
        setSubmitting(true);
        try {
            const formData = new FormData();
            formData.append('_method', 'PATCH');
            formData.append('title', title);
            formData.append('description', description);
            formData.append('is_visible', isVisible ? '1' : '0');
            formData.append('allow_comments', allowComments ? '1' : '0');
            formData.append('allow_download', allowDownload ? '1' : '0');
            formData.append('explicit', explicit ? '1' : '0');
            if (artworkFile) formData.append('artwork', artworkFile);
            await apiClient.post(`/podcasts/${uuid}`, formData);
        } catch (error) {
            console.error('Failed to update podcast:', error);
        } finally {
            setSubmitting(false);
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

    if (!podcast) return null;

    return (
        <UserLayout user={podcast.user}>
            <div className="d-flex flex-column gap-3">
                <div className="row gy-3">
                    <div className="col-12 col-lg-4 d-flex flex-column gap-4 justify-content-center justify-content-xl-start align-items-center">
                        <img src={artworkPreview} alt={podcast.title} className="img-fluid rounded-4 border-pink" />
                        {artworkSelected && (
                            <button className="btn btn-default btn-outline" onClick={resetArtwork}>
                                <i className="fas fa-trash" /> Clear
                            </button>
                        )}
                        <label className="btn-default btn-pink" style={{ cursor: 'pointer' }}>
                            Artwork
                            <input className="d-none" accept="image/*" type="file" onChange={handleArtworkChange} />
                        </label>
                    </div>
                    <div className="col d-flex flex-column justify-content-start align-items-center gap-4">
                        <div className="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                            <label className="font-default fs-14 color-text">Title</label>
                            <input type="text" className="form-control" maxLength={175} value={title} onChange={(e) => setTitle(e.target.value)} required />
                        </div>
                        <div className="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                            <label className="font-default fs-14 color-text">Description</label>
                            <textarea className="form-control" maxLength={180} value={description} onChange={(e) => setDescription(e.target.value)} />
                        </div>
                    </div>
                    <div className="col-12 d-flex flex-column gap-3">
                        <div className="d-flex flex-row justify-content-between align-items-center w-100 flex-wrap">
                            <div className="form-check form-switch">
                                <input className="form-check-input" type="checkbox" checked={isVisible} onChange={(e) => setIsVisible(e.target.checked)} />
                                <label className="form-check-label">Make Public</label>
                            </div>
                            <div className="form-check form-switch">
                                <input className="form-check-input" type="checkbox" checked={allowComments} onChange={(e) => setAllowComments(e.target.checked)} />
                                <label className="form-check-label">Allow Comments</label>
                            </div>
                            <div className="form-check form-switch">
                                <input className="form-check-input" type="checkbox" checked={allowDownload} onChange={(e) => setAllowDownload(e.target.checked)} />
                                <label className="form-check-label">Allow Download</label>
                            </div>
                            <div className="form-check form-switch">
                                <input className="form-check-input" type="checkbox" checked={explicit} onChange={(e) => setExplicit(e.target.checked)} />
                                <label className="form-check-label">Explicit</label>
                            </div>
                        </div>
                        <div className="d-flex flex-row justify-content-end align-items-center gap-3 w-100">
                            <Link to={`/podcasts/${podcast.uuid}`} className="btn btn-default btn-outline">Cancel</Link>
                            <button className="btn btn-default btn-pink" onClick={handleSubmit} disabled={submitting}>Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </UserLayout>
    );
}
