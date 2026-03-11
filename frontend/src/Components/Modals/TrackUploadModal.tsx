import React, { useState, useEffect } from 'react';
import { Modal, Form, Spinner, Tab, Tabs } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import apiClient from '@/api/client';
import { useAuthStore } from '@/stores/auth';

interface TrackUploadModalProps {
    show: boolean;
    onClose: () => void;
}

export default function TrackUploadModal({ show, onClose }: TrackUploadModalProps) {
    const user = useAuthStore((state) => state.user);
    const allowedFileSize = 92970;
    const maxAllowed = 60;
    const today = new Date(Date.now()).toJSON().substring(0, 10);

    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [script, setScript] = useState('');
    const [releasedAt, setReleasedAt] = useState(today);
    const [publishedAt, setPublishedAt] = useState(today);
    const [isVisible, setIsVisible] = useState(false);
    const [allowComments, setAllowComments] = useState(false);
    const [allowDownload, setAllowDownload] = useState(false);
    const [isExplicit, setIsExplicit] = useState(true);
    const [notify, setNotify] = useState(false);
    const [isPatron, setIsPatron] = useState(false);
    const [file, setFile] = useState<File | null>(null);
    const [artwork, setArtwork] = useState<File | null>(null);
    const [artworkPreview, setArtworkPreview] = useState('/assets/images/song.png');
    const [hasFile, setHasFile] = useState(false);
    const [validateFileSize, setValidateFileSize] = useState(false);
    const [processing, setProcessing] = useState(false);
    const [progress, setProgress] = useState(0);
    const [titleLength, setTitleLength] = useState(0);
    const [inputAllowed, setInputAllowed] = useState(true);
    const [activeTab, setActiveTab] = useState('select');

    useEffect(() => {
        setTitleLength(title?.length ?? 0);
        setInputAllowed((title?.length ?? 0) < maxAllowed);
    }, [title]);

    useEffect(() => {
        if (file) {
            const isEmpty = !file || file.size <= 0;
            const valid = isEmpty ? false : Math.round(file.size / 1024) <= allowedFileSize;
            setValidateFileSize(valid);
            setHasFile(!isEmpty && valid);
            if (valid) setActiveTab('details');
        } else {
            setValidateFileSize(false);
            setHasFile(false);
        }
    }, [file]);

    useEffect(() => {
        if (artwork) {
            setArtworkPreview(URL.createObjectURL(artwork));
        } else {
            setArtworkPreview('/assets/images/song.png');
        }
    }, [artwork]);

    const upload = async () => {
        setProcessing(true);
        try {
            const formData = new FormData();
            formData.append('title', title);
            formData.append('description', description || '');
            formData.append('script', script || '');
            formData.append('released_at', releasedAt);
            formData.append('published_at', publishedAt);
            formData.append('is_visible', String(isVisible));
            formData.append('allow_comments', String(allowComments));
            formData.append('allow_download', String(allowDownload));
            formData.append('is_explicit', String(isExplicit));
            formData.append('notify', String(notify));
            formData.append('is_patron', String(isPatron));
            if (file) formData.append('file', file);
            if (artwork) formData.append('artwork', artwork);

            await apiClient.post('/upload/tracks', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
                onUploadProgress: (e) => {
                    if (e.total) setProgress(Math.round((e.loaded * 100) / e.total));
                },
            });
            close();
        } catch (err) {
            console.error('Upload failed:', err);
        } finally {
            setProcessing(false);
        }
    };

    const close = () => {
        setTitle('');
        setDescription('');
        setScript('');
        setFile(null);
        setArtwork(null);
        setActiveTab('select');
        setProcessing(false);
        setProgress(0);
        onClose();
    };

    return (
        <Modal show={show} onHide={close} centered size="lg" backdrop={processing ? 'static' : true} keyboard={!processing}>
            <Modal.Body>
                <div className="container-fluid overflow-y-auto">
                    <div className="w-100 text-center font-default fs-5 mb-2">Upload Track</div>
                    {processing && (
                        <div className="d-flex flex-column w-100 text-center align-items-center justify-content-center gap-4 my-3">
                            <span className="font-default">Please wait...</span>
                            <div className="d-flex flex-row gap-2 align-items-center">
                                <Spinner animation="border" variant="danger" />
                                <span className="font-default fw-bold fs-5">{progress}%</span>
                            </div>
                        </div>
                    )}
                    <Tabs activeKey={activeTab} onSelect={(k) => k && setActiveTab(k)} className="tabs-header w-100" fill>
                        <Tab eventKey="select" title="Select File">
                            <div className="d-flex flex-column w-100 justify-content-start align-items-start gap-1 pt-3">
                                <Form.Control
                                    type="file"
                                    accept="audio/*"
                                    onChange={(e: React.ChangeEvent<HTMLInputElement>) => setFile(e.target.files?.[0] || null)}
                                />
                            </div>
                        </Tab>
                        <Tab eventKey="details" title="Input Details" disabled={!hasFile}>
                            <div className="row pt-3">
                                <div className="col-12 col-xl-4">
                                    <div className="d-flex flex-column justify-content-start w-100 gap-4">
                                        <div className="d-flex flex-column justify-content-center align-items-start gap-1">
                                            <label className="text-center font-default fs-14 default-text-color">Artwork</label>
                                            <img src={artworkPreview} className="img-fluid rounded-4 border-pink" alt="artwork" />
                                        </div>
                                        <Form.Control type="file" accept="image/*" onChange={(e: React.ChangeEvent<HTMLInputElement>) => setArtwork(e.target.files?.[0] || null)} />
                                    </div>
                                </div>
                                <div className="col">
                                    <div className="d-flex flex-column justify-content-start w-100 gap-4">
                                        <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                            <label className="text-start font-default fs-14 default-text-color">Title</label>
                                            <Form.Control type="text" value={title} onChange={(e) => setTitle(e.target.value)} maxLength={maxAllowed} />
                                            <span className={`fs-12 font-merge ${!inputAllowed ? 'color-pink' : 'color-grey'}`}>
                                                {titleLength}/{maxAllowed} characters
                                            </span>
                                        </div>
                                        <div className="d-flex flex-row justify-content-between align-items-end gap-3">
                                            <div className="d-flex flex-column w-50 justify-content-start align-items-start gap-1">
                                                <label className="text-start font-default fs-14 default-text-color">Release Date</label>
                                                <Form.Control type="date" value={releasedAt} onChange={(e) => setReleasedAt(e.target.value)} max={today} />
                                            </div>
                                            <div className="d-flex flex-column w-50 justify-content-start align-items-start gap-1">
                                                <label className="text-start font-default fs-14 default-text-color">Schedule Publish</label>
                                                <Form.Control type="date" value={publishedAt} onChange={(e) => setPublishedAt(e.target.value)} min={today} required />
                                            </div>
                                        </div>
                                        <div className="row">
                                            <div className="col-6">
                                                <Form.Check type="switch" label="Make Public" checked={isVisible} onChange={(e) => setIsVisible(e.target.checked)} />
                                                <Form.Check type="switch" label="Allow Comments" checked={allowComments} onChange={(e) => setAllowComments(e.target.checked)} />
                                                <Form.Check type="switch" label="Allow Download" checked={allowDownload} onChange={(e) => setAllowDownload(e.target.checked)} />
                                            </div>
                                            <div className="col-6">
                                                <Form.Check type="switch" label="Explicit" checked={isExplicit} onChange={(e) => setIsExplicit(e.target.checked)} />
                                                <Form.Check type="switch" label="Notify Fans" checked={notify} onChange={(e) => setNotify(e.target.checked)} />
                                                {user?.has_active_subscription && (
                                                    <Form.Check type="switch" label="Patrons Only" checked={isPatron} onChange={(e) => setIsPatron(e.target.checked)} />
                                                )}
                                            </div>
                                        </div>
                                        <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                            <label className="text-start font-default fs-14 default-text-color">Description</label>
                                            <Form.Control as="textarea" value={description} onChange={(e) => setDescription(e.target.value)} />
                                        </div>
                                        <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                            <label className="text-start font-default fs-14 default-text-color">Script</label>
                                            <Form.Control type="text" value={script} onChange={(e) => setScript(e.target.value)} />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="d-flex flex-row justify-content-center align-items-center w-100 gap-4 mt-4">
                                <DefaultButton classList="btn-pink" onClick={upload} disabled={processing || !hasFile}>
                                    <FontAwesomeIcon icon={['fas', 'floppy-disk']} /> Upload
                                </DefaultButton>
                                <DefaultButton classList="btn-outline" disabled={processing} onClick={close}>
                                    <FontAwesomeIcon icon={['fas', 'trash']} /> Cancel
                                </DefaultButton>
                            </div>
                        </Tab>
                    </Tabs>
                </div>
            </Modal.Body>
        </Modal>
    );
}
