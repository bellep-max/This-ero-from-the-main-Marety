import React, { useState, useEffect } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import apiClient from '@/api/client';
import { useAuthStore } from '@/stores/auth';
import UserLayout from '@/Layouts/UserLayout';

export default function PlaylistEdit() {
    const { uuid } = useParams<{ uuid: string }>();
    const navigate = useNavigate();
    const [playlist, setPlaylist] = useState<any>(null);
    const [loading, setLoading] = useState(true);

    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [isVisible, setIsVisible] = useState(true);
    const [allowComments, setAllowComments] = useState(true);
    const [isExplicit, setIsExplicit] = useState(false);
    const [artworkPreview, setArtworkPreview] = useState<string>('');
    const [artworkFile, setArtworkFile] = useState<File | null>(null);
    const [artworkSelected, setArtworkSelected] = useState(false);
    const [submitting, setSubmitting] = useState(false);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await apiClient.get(`/playlists/${uuid}/edit`);
                const apiData = response.data;
                const playlistData = apiData.playlist ?? null;
                setPlaylist(playlistData);
                if (playlistData) {
                    setTitle(playlistData.title || '');
                    setDescription(playlistData.description || '');
                    setIsVisible(playlistData.is_visible ?? true);
                    setAllowComments(playlistData.allow_comments ?? true);
                    setIsExplicit(playlistData.is_explicit ?? false);
                    setArtworkPreview(playlistData.artwork || '/assets/images/playlist.png');
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
            setArtworkPreview('/assets/images/playlist.png');
            setArtworkSelected(false);
        }
    };

    const resetArtwork = () => {
        setArtworkFile(null);
        setArtworkPreview(playlist?.artwork || '/assets/images/playlist.png');
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
            formData.append('is_explicit', isExplicit ? '1' : '0');
            if (artworkFile) formData.append('artwork', artworkFile);
            await apiClient.post(`/playlists/${uuid}`, formData);
        } catch (error) {
            console.error('Failed to update playlist:', error);
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

    if (!playlist) return null;

    return (
        <UserLayout user={playlist.user}>
            <div className="d-flex flex-column gap-3">
                <div className="row gy-3">
                    <div className="col-12 col-lg-4 d-flex flex-column gap-4 justify-content-center justify-content-xl-start align-items-center">
                        <img src={artworkPreview} alt={playlist.title} className="img-fluid rounded-4 border-pink" />
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
                    <div className="col">
                        <div className="d-flex flex-column justify-content-start align-items-center gap-4">
                            <div className="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                                <label className="font-default fs-14 color-text">Title</label>
                                <input
                                    type="text"
                                    className="form-control"
                                    maxLength={175}
                                    value={title}
                                    onChange={(e) => setTitle(e.target.value)}
                                    required
                                />
                            </div>
                            <div className="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                                <label className="font-default fs-14 color-text">Description</label>
                                <textarea
                                    className="form-control"
                                    maxLength={180}
                                    value={description}
                                    onChange={(e) => setDescription(e.target.value)}
                                />
                            </div>
                        </div>
                    </div>
                    <div className="col-12">
                        <div className="d-flex flex-column flex-md-row justify-content-center justify-content-md-between align-items-center flex-wrap">
                            <div className="d-flex flex-row justify-content-start gap-3">
                                <div className="form-check">
                                    <input className="form-check-input" type="checkbox" checked={allowComments} onChange={(e) => setAllowComments(e.target.checked)} />
                                    <label className="form-check-label font-default fs-14 color-text">Allow Comments</label>
                                </div>
                                <div className="form-check">
                                    <input className="form-check-input" type="checkbox" checked={isVisible} onChange={(e) => setIsVisible(e.target.checked)} />
                                    <label className="form-check-label font-default fs-14 color-text">Make Public</label>
                                </div>
                                <div className="form-check">
                                    <input className="form-check-input" type="checkbox" checked={isExplicit} onChange={(e) => setIsExplicit(e.target.checked)} />
                                    <label className="form-check-label font-default fs-14 color-text">Explicit</label>
                                </div>
                            </div>
                            <div className="d-flex flex-row justify-content-center align-items-center gap-3">
                                <Link to={`/playlists/${playlist.uuid}`} className="btn btn-default btn-outline">Cancel</Link>
                                <button className="btn btn-default btn-pink" onClick={handleSubmit} disabled={submitting}>Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </UserLayout>
    );
}
