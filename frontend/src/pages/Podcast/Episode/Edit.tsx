import React, { useState, useEffect } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import apiClient from '@/api/client';
import UserLayout from '@/Layouts/UserLayout';

export default function PodcastEpisodeEdit() {
    const { episodeUuid } = useParams<{ episodeUuid: string }>();
    const navigate = useNavigate();
    const [episode, setEpisode] = useState<any>(null);
    const [user, setUser] = useState<any>(null);
    const [loading, setLoading] = useState(true);

    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [isVisible, setIsVisible] = useState(true);
    const [allowComments, setAllowComments] = useState(true);
    const [allowDownload, setAllowDownload] = useState(false);
    const [explicit, setExplicit] = useState(false);
    const [artworkPreview, setArtworkPreview] = useState<string>('');
    const [artworkFile, setArtworkFile] = useState<File | null>(null);
    const [audioFile, setAudioFile] = useState<File | null>(null);
    const [submitting, setSubmitting] = useState(false);

    const maxAllowed = 60;
    const titleLength = title.length;
    const inputAllowed = titleLength < maxAllowed;

    const allowedFileSize = 92970;
    const validateFileSize = audioFile ? Math.round(audioFile.size / 1024) <= allowedFileSize : false;

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await apiClient.get(`/episodes/${episodeUuid}/edit`);
                const apiData = response.data;
                const episodeData = apiData.episode ?? null;
                setEpisode(episodeData);
                setUser(apiData.user ?? null);
                if (episodeData) {
                    setTitle(episodeData.title || '');
                    setDescription(episodeData.description || '');
                    setIsVisible(episodeData.is_visible ?? true);
                    setAllowComments(episodeData.allow_comments ?? true);
                    setAllowDownload(episodeData.allow_download ?? false);
                    setExplicit(episodeData.explicit ?? false);
                    setArtworkPreview(episodeData.artwork || '');
                }
            } catch (error) {
                console.error('Failed to load page data:', error);
            } finally {
                setLoading(false);
            }
        };
        fetchData();
    }, [episodeUuid]);

    const handleArtworkChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0] || null;
        setArtworkFile(file);
        if (file) {
            setArtworkPreview(URL.createObjectURL(file));
        } else if (episode) {
            setArtworkPreview(episode.artwork || '');
        }
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
            if (audioFile) formData.append('file', audioFile);
            await apiClient.post(`/episodes/${episodeUuid}`, formData);
        } catch (error) {
            console.error('Failed to update episode:', error);
        } finally {
            setSubmitting(false);
        }
    };

    const handleDelete = async () => {
        if (window.confirm('Are you sure you want to delete this episode?')) {
            try {
                await apiClient.delete(`/episodes/${episodeUuid}`);
                navigate('/');
            } catch (error) {
                console.error('Failed to delete:', error);
            }
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

    if (!episode) return null;

    return (
        <UserLayout user={user}>
            <div className="row gy-3">
                <div className="d-flex flex-column w-100 justify-content-start align-items-start gap-1">
                    <input
                        type="file"
                        accept="audio/*"
                        className={`form-control ${audioFile ? (validateFileSize ? 'is-valid' : 'is-invalid') : ''}`}
                        onChange={(e) => setAudioFile(e.target.files?.[0] || null)}
                    />
                </div>
                <div className="col-12 col-lg-4 d-flex justify-content-center justify-content-xl-start align-items-start">
                    <div className="d-flex flex-column justify-content-start w-100 gap-4">
                        <div className="d-flex flex-column justify-content-center align-items-start gap-1">
                            <label className="text-center font-default fs-14 default-text-color">Artwork</label>
                            <img src={artworkPreview} className="img-fluid rounded-4 border-pink" alt="artwork" />
                        </div>
                        <input type="file" accept="image/*" className="form-control" onChange={handleArtworkChange} />
                    </div>
                </div>
                <div className="col">
                    <div className="d-flex flex-column justify-content-start w-100 gap-4">
                        <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                            <label className="text-start font-default fs-14 default-text-color">Title</label>
                            <input
                                type="text"
                                className={`form-control ${!inputAllowed ? 'is-invalid' : ''}`}
                                value={title}
                                onChange={(e) => setTitle(e.target.value)}
                                maxLength={maxAllowed}
                            />
                            <span className={`fs-12 font-merge ${!inputAllowed ? 'color-pink' : 'color-grey'}`}>
                                {titleLength}/{maxAllowed} characters
                            </span>
                        </div>
                        <div className="row">
                            <div className="col-6">
                                <div className="d-flex flex-column justify-content-start align-items-start">
                                    <div className="form-check form-switch">
                                        <input className="form-check-input" type="checkbox" checked={isVisible} onChange={(e) => setIsVisible(e.target.checked)} />
                                        <label className="form-check-label">Make Public</label>
                                    </div>
                                    <div className="form-check form-switch">
                                        <input className="form-check-input" type="checkbox" checked={allowComments} onChange={(e) => setAllowComments(e.target.checked)} />
                                        <label className="form-check-label">Allow Comments</label>
                                    </div>
                                </div>
                            </div>
                            <div className="col-6">
                                <div className="d-flex flex-column justify-content-start align-items-start">
                                    <div className="form-check form-switch">
                                        <input className="form-check-input" type="checkbox" checked={allowDownload} onChange={(e) => setAllowDownload(e.target.checked)} />
                                        <label className="form-check-label">Allow Download</label>
                                    </div>
                                    <div className="form-check form-switch">
                                        <input className="form-check-input" type="checkbox" checked={explicit} onChange={(e) => setExplicit(e.target.checked)} />
                                        <label className="form-check-label">Explicit</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                            <label className="text-start font-default fs-14 default-text-color">Description</label>
                            <textarea className="form-control" rows={5} value={description} onChange={(e) => setDescription(e.target.value)} />
                        </div>
                    </div>
                </div>
            </div>
            <div className="d-flex flex-row justify-content-center align-items-center gap-4 w-100 mt-5">
                <Link to={`/episodes/${episode.uuid}`} className="btn btn-default btn-outline">Cancel</Link>
                <button className="btn btn-default btn-pink" onClick={handleSubmit} disabled={submitting}>Save</button>
                <button className="btn btn-default btn-outline" onClick={handleDelete}>
                    <i className="fas fa-trash" /> Delete
                </button>
            </div>
        </UserLayout>
    );
}
