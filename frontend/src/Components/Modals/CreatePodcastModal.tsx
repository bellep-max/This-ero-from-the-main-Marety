import React, { useState, useEffect } from 'react';
import { Modal, Form, Spinner } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import apiClient from '@/api/client';
import { useAuthStore } from '@/stores/auth';

interface CreatePodcastModalProps {
    show: boolean;
    onClose: () => void;
}

export default function CreatePodcastModal({ show, onClose }: CreatePodcastModalProps) {
    const user = useAuthStore((state) => state.user);
    const maxAllowed = 60;

    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [isVisible, setIsVisible] = useState(false);
    const [allowComments, setAllowComments] = useState(false);
    const [allowDownload, setAllowDownload] = useState(false);
    const [explicit, setExplicit] = useState(false);
    const [artwork, setArtwork] = useState<File | null>(null);
    const [artworkPreview, setArtworkPreview] = useState('/assets/images/song.png');
    const [processing, setProcessing] = useState(false);
    const [titleLength, setTitleLength] = useState(0);
    const [inputAllowed, setInputAllowed] = useState(true);

    useEffect(() => {
        setTitleLength(title?.length ?? 0);
        setInputAllowed((title?.length ?? 0) < maxAllowed);
    }, [title]);

    useEffect(() => {
        if (artwork) {
            setArtworkPreview(URL.createObjectURL(artwork));
        } else {
            setArtworkPreview('/assets/images/song.png');
        }
    }, [artwork]);

    const create = async () => {
        setProcessing(true);
        try {
            const formData = new FormData();
            formData.append('title', title);
            formData.append('description', description || '');
            formData.append('is_visible', String(isVisible));
            formData.append('allow_comments', String(allowComments));
            formData.append('allow_download', String(allowDownload));
            formData.append('explicit', String(explicit));
            if (artwork) formData.append('artwork', artwork);

            await apiClient.post('/podcasts', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            resetForm();
            onClose();
        } catch (err) {
            console.error('Create podcast failed:', err);
        } finally {
            setProcessing(false);
        }
    };

    const resetForm = () => {
        setTitle('');
        setDescription('');
        setIsVisible(false);
        setAllowComments(false);
        setAllowDownload(false);
        setExplicit(false);
        setArtwork(null);
    };

    const close = () => {
        resetForm();
        onClose();
    };

    return (
        <Modal show={show} onHide={onClose} centered size="lg" backdrop={processing ? 'static' : true} keyboard={!processing}>
            <Modal.Body className="d-flex flex-column p-4">
                <div className="container-fluid overflow-y-auto">
                    <div className="w-100 text-center font-default fs-5 mb-2">Create Podcast</div>
                    {processing && (
                        <div className="d-flex flex-column w-100 text-center align-items-center justify-content-center gap-4 my-3">
                            <span className="font-default">Please wait...</span>
                            <Spinner animation="border" variant="danger" />
                        </div>
                    )}
                    <div className="row">
                        <div className="col-12 col-xl-4">
                            <div className="d-flex flex-column justify-content-start w-100 gap-4">
                                <div className="d-flex flex-column justify-content-center align-items-start gap-1">
                                    <label className="text-center font-default fs-14 default-text-color">Artwork</label>
                                    <img src={artworkPreview} className="img-fluid rounded-4 border-pink" alt="artwork" />
                                </div>
                                <Form.Control type="file" accept="image/*" onChange={(e: React.ChangeEvent<HTMLInputElement>) => setArtwork(e.target.files?.[0] || null)} />
                                <div className="d-flex flex-column fw-light text-secondary text-center font-merge">
                                    <span>Recommended size: 500x500px</span>
                                    <span>Max file size: 2MB</span>
                                    <span>Formats: JPG, PNG</span>
                                </div>
                            </div>
                        </div>
                        <div className="col">
                            <div className="d-flex flex-column justify-content-start w-100 gap-4">
                                <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                    <label className="text-start font-default fs-14 default-text-color">Title</label>
                                    <Form.Control
                                        type="text"
                                        value={title}
                                        onChange={(e) => setTitle(e.target.value)}
                                        isValid={inputAllowed}
                                        isInvalid={!inputAllowed}
                                        maxLength={maxAllowed}
                                    />
                                    <span className={`fs-12 font-merge ${!inputAllowed ? 'color-pink' : 'color-grey'}`}>
                                        {titleLength}/{maxAllowed} characters
                                    </span>
                                </div>
                                <div className="row">
                                    <div className="col-6">
                                        <div className="d-flex flex-column justify-content-start align-items-start">
                                            <Form.Check type="switch" label="Make Public" checked={isVisible} onChange={(e) => setIsVisible(e.target.checked)} />
                                            <Form.Check type="switch" label="Explicit" checked={explicit} onChange={(e) => setExplicit(e.target.checked)} />
                                        </div>
                                    </div>
                                    <div className="col-6">
                                        <div className="d-flex flex-column justify-content-start align-items-start">
                                            <Form.Check type="switch" label="Allow Comments" checked={allowComments} onChange={(e) => setAllowComments(e.target.checked)} />
                                            <Form.Check type="switch" label="Allow Download" checked={allowDownload} onChange={(e) => setAllowDownload(e.target.checked)} />
                                        </div>
                                    </div>
                                </div>
                                <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                    <label className="text-start font-default fs-14 default-text-color">Description</label>
                                    <Form.Control as="textarea" value={description} onChange={(e) => setDescription(e.target.value)} />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="d-flex flex-row justify-content-center align-items-center w-100 gap-4 mt-4">
                        <DefaultButton classList="btn-pink" onClick={create} disabled={processing}>
                            <FontAwesomeIcon icon={['fas', 'floppy-disk']} /> Create
                        </DefaultButton>
                        <DefaultButton classList="btn-outline" disabled={processing} onClick={close}>
                            <FontAwesomeIcon icon={['fas', 'trash']} /> Cancel
                        </DefaultButton>
                    </div>
                </div>
            </Modal.Body>
        </Modal>
    );
}
