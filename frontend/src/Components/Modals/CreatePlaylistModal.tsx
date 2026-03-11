import React, { useState, useEffect } from 'react';
import { Modal, Form } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import apiClient from '@/api/client';
import { useAuthStore } from '@/stores/auth';

interface CreatePlaylistModalProps {
    show: boolean;
    onClose: () => void;
}

export default function CreatePlaylistModal({ show, onClose }: CreatePlaylistModalProps) {
    const user = useAuthStore((state) => state.user);
    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [isVisible, setIsVisible] = useState(false);
    const [allowComments, setAllowComments] = useState(false);
    const [isExplicit, setIsExplicit] = useState(true);
    const [artwork, setArtwork] = useState<File | null>(null);
    const [artworkPreview, setArtworkPreview] = useState('/assets/images/playlist.png');
    const [genres, setGenres] = useState<any[]>([]);
    const [selectedGenres, setSelectedGenres] = useState<any[]>([]);
    const [processing, setProcessing] = useState(false);

    const defaultArtworkLink = '/assets/images/playlist.png';

    useEffect(() => {
        if (artwork) {
            setArtworkPreview(URL.createObjectURL(artwork));
        } else {
            setArtworkPreview(defaultArtworkLink);
        }
    }, [artwork]);

    const submit = async () => {
        setProcessing(true);
        try {
            const formData = new FormData();
            formData.append('title', title);
            formData.append('description', description);
            formData.append('is_visible', String(isVisible));
            formData.append('allow_comments', String(allowComments));
            formData.append('is_explicit', String(isExplicit));
            if (artwork) formData.append('artwork', artwork);

            await apiClient.post('/playlists', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            resetForm();
            onClose();
        } catch (err) {
            console.error('Create playlist failed:', err);
        } finally {
            setProcessing(false);
        }
    };

    const resetForm = () => {
        setTitle('');
        setDescription('');
        setIsVisible(false);
        setAllowComments(false);
        setIsExplicit(true);
        setArtwork(null);
        setSelectedGenres([]);
    };

    const resetArtwork = () => {
        setArtwork(null);
    };

    return (
        <Modal show={show} onHide={onClose} centered size="lg">
            <Modal.Body className="d-flex flex-column p-4">
                <div className="text-center font-default fs-5 mb-3">Create Playlist</div>
                <div className="row gy-3 gy-lg-4">
                    <div className="col-12 col-md-5">
                        <div className="d-flex flex-column justify-content-start align-items-center gap-4">
                            <img className="img-fluid rounded-4 border-pink" src={artworkPreview} alt="Playlist artwork" />
                            {artwork && (
                                <DefaultButton classList="btn-outline" onClick={resetArtwork}>
                                    <FontAwesomeIcon icon={['fas', 'trash']} /> Clear
                                </DefaultButton>
                            )}
                            <label className="btn-default btn-pink" style={{ cursor: 'pointer' }}>
                                Artwork
                                <input className="d-none" accept="image/*" type="file" onChange={(e) => setArtwork(e.target.files?.[0] || null)} />
                            </label>
                        </div>
                    </div>
                    <div className="col-12 col-md-7">
                        <div className="d-flex flex-column justify-content-start align-items-center gap-4">
                            <div className="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                                <label className="font-default fs-14 color-text">Title</label>
                                <Form.Control maxLength={175} value={title} onChange={(e) => setTitle(e.target.value)} required />
                            </div>
                            <div className="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                                <label className="font-default fs-14 color-text">Description</label>
                                <Form.Control as="textarea" maxLength={180} value={description} onChange={(e) => setDescription(e.target.value)} />
                            </div>
                        </div>
                    </div>
                    <div className="col-12">
                        <div className="d-flex flex-column flex-md-row justify-content-center justify-content-md-between align-items-center flex-wrap">
                            <div className="d-flex flex-row justify-content-start gap-3">
                                <Form.Check type="checkbox" label="Allow Comments" checked={allowComments} onChange={(e) => setAllowComments(e.target.checked)} />
                                <Form.Check type="checkbox" label="Make Public" checked={isVisible} onChange={(e) => setIsVisible(e.target.checked)} />
                                <Form.Check type="checkbox" label="Explicit" checked={isExplicit} onChange={(e) => setIsExplicit(e.target.checked)} />
                            </div>
                            <DefaultButton classList="btn-pink" onClick={submit} disabled={processing}>Save</DefaultButton>
                        </div>
                    </div>
                </div>
            </Modal.Body>
        </Modal>
    );
}
