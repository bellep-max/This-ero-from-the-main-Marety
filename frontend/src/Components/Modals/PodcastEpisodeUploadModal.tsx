import React, { useState, useEffect, useMemo } from 'react';
import { Modal, Form, Spinner, Tab, Tabs } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import apiClient from '@/api/client';
import { useAuthStore } from '@/stores/auth';

interface PodcastEpisodeUploadModalProps {
    show: boolean;
    onClose: () => void;
}

export default function PodcastEpisodeUploadModal({ show, onClose }: PodcastEpisodeUploadModalProps) {
    const user = useAuthStore((state) => state.user);
    const allowedFileSize = 92970;
    const maxAllowed = 60;

    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [isVisible, setIsVisible] = useState(false);
    const [allowComments, setAllowComments] = useState(false);
    const [allowDownload, setAllowDownload] = useState(false);
    const [explicit, setExplicit] = useState(false);
    const [file, setFile] = useState<File | null>(null);
    const [artwork, setArtwork] = useState<File | null>(null);
    const [artworkPreview, setArtworkPreview] = useState('/assets/images/song.png');
    const [podcastUuid, setPodcastUuid] = useState('');
    const [season, setSeason] = useState<number | null>(null);
    const [episodeNumber, setEpisodeNumber] = useState<number | null>(null);
    const [hasFile, setHasFile] = useState(false);
    const [validateFileSize, setValidateFileSize] = useState(false);
    const [processing, setProcessing] = useState(false);
    const [progress, setProgress] = useState(0);
    const [titleLength, setTitleLength] = useState(0);
    const [inputAllowed, setInputAllowed] = useState(true);
    const [activeTab, setActiveTab] = useState('select');
    const [podcastDataSelected, setPodcastDataSelected] = useState(false);
    const [seasonOptions, setSeasonOptions] = useState<any[]>([]);
    const [episodeNumberOptions, setEpisodeNumberOptions] = useState<any[]>([]);
    const [seasonAdded, setSeasonAdded] = useState(false);

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

    useEffect(() => {
        if (!podcastUuid || !user?.podcasts) return;
        const podcast = user.podcasts.find((p: any) => p.uuid === podcastUuid);
        if (!podcast) return;
        setSeasonOptions(
            Array.from({ length: podcast.seasons }, (_, i) => ({ value: i + 1, text: `Season ${i + 1}` }))
        );
        setSeasonAdded(false);
        setSeason(null);
        setEpisodeNumber(null);
        validateSelection();
    }, [podcastUuid]);

    useEffect(() => {
        validateSelection();
    }, [podcastUuid, season, episodeNumber]);

    const validateSelection = () => {
        setPodcastDataSelected(!!podcastUuid && !!season && !!episodeNumber);
    };

    const podcasts = useMemo(() => {
        if (user?.podcasts?.length) {
            return user.podcasts.map((podcast: any) => ({
                value: podcast.uuid,
                text: podcast.title,
            }));
        }
        return [];
    }, [user]);

    const addSeason = () => {
        if (!podcastUuid || !user?.podcasts) return;
        const podcast = user.podcasts.find((p: any) => p.uuid === podcastUuid);
        if (!podcast) return;
        setSeasonOptions(
            Array.from({ length: podcast.seasons + 1 }, (_, i) => ({ value: i + 1, text: `Season ${i + 1}` }))
        );
        setSeason(podcast.seasons + 1);
        setEpisodeNumber(0);
        setSeasonAdded(true);
    };

    const upload = async () => {
        setProcessing(true);
        try {
            const formData = new FormData();
            formData.append('title', title);
            formData.append('description', description || '');
            formData.append('is_visible', String(isVisible));
            formData.append('allow_comments', String(allowComments));
            formData.append('allow_download', String(allowDownload));
            formData.append('explicit', String(explicit));
            formData.append('podcast_uuid', podcastUuid);
            if (season) formData.append('season', String(season));
            if (episodeNumber) formData.append('number', String(episodeNumber));
            if (file) formData.append('file', file);
            if (artwork) formData.append('artwork', artwork);

            await apiClient.post('/upload/episodes', formData, {
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
        setFile(null);
        setArtwork(null);
        setPodcastUuid('');
        setSeason(null);
        setEpisodeNumber(null);
        setActiveTab('select');
        setProcessing(false);
        setProgress(0);
        onClose();
    };

    const proceed = () => setActiveTab('details');

    return (
        <Modal show={show} onHide={close} centered size="lg" backdrop={processing ? 'static' : true} keyboard={!processing}>
            <Modal.Body>
                <div className="container-fluid overflow-y-auto">
                    <div className="w-100 text-center font-default fs-5 mb-2">Upload Podcast Episode</div>
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
                        <Tab eventKey="select" title="Select Podcast" disabled={activeTab === 'details'}>
                            <div className="d-flex flex-column justify-content-start align-items-start gap-1 pt-3">
                                <label className="text-start font-default fs-14 default-text-color">Podcast</label>
                                <Form.Select value={podcastUuid} onChange={(e) => setPodcastUuid(e.target.value)}>
                                    <option value="">Select podcast...</option>
                                    {podcasts.map((p: any) => (
                                        <option key={p.value} value={p.value}>{p.text}</option>
                                    ))}
                                </Form.Select>
                            </div>
                            <div className="row mt-4">
                                <div className="col-6 d-flex flex-column justify-content-end align-items-start gap-1">
                                    <label className="text-start font-default fs-14 default-text-color d-flex flex-row align-items-center">
                                        <span>Season</span>
                                        <button
                                            className="btn btn-sm btn-default btn-pink ms-2"
                                            disabled={!podcastUuid || seasonAdded}
                                            onClick={addSeason}
                                        >
                                            <FontAwesomeIcon icon={['fas', 'plus']} />
                                        </button>
                                    </label>
                                    <Form.Select
                                        value={season || ''}
                                        onChange={(e) => setSeason(Number(e.target.value))}
                                        disabled={!podcastUuid}
                                    >
                                        <option value="">Select season...</option>
                                        {seasonOptions.map((s: any) => (
                                            <option key={s.value} value={s.value}>{s.text}</option>
                                        ))}
                                    </Form.Select>
                                </div>
                                <div className="col-6 d-flex flex-column justify-content-end align-items-start gap-1">
                                    <label className="text-start font-default fs-14 default-text-color">Episode Number</label>
                                    <Form.Select
                                        value={episodeNumber || ''}
                                        onChange={(e) => setEpisodeNumber(Number(e.target.value))}
                                        disabled={!podcastUuid}
                                    >
                                        <option value="">Select...</option>
                                        {episodeNumberOptions.map((ep: any) => (
                                            <option key={ep.value} value={ep.value} disabled={ep.disabled}>{ep.text}</option>
                                        ))}
                                    </Form.Select>
                                </div>
                            </div>
                            <div className="d-flex flex-row justify-content-center align-items-center w-100 gap-4 mt-4">
                                <DefaultButton classList="btn-pink" onClick={proceed} disabled={processing || !podcastDataSelected}>
                                    <FontAwesomeIcon icon={['fas', 'angles-right']} /> Proceed
                                </DefaultButton>
                                <DefaultButton classList="btn-outline" disabled={processing} onClick={close}>
                                    <FontAwesomeIcon icon={['fas', 'trash']} /> Cancel
                                </DefaultButton>
                            </div>
                        </Tab>
                        <Tab eventKey="details" title="Input Details" disabled={!podcastDataSelected}>
                            <div className="d-flex flex-column w-100 gap-4 pt-3">
                                <div className="d-flex flex-column justify-content-center align-items-start gap-1 w-100">
                                    <label className="text-center font-default fs-14 default-text-color">Select File</label>
                                    <Form.Control type="file" accept="audio/*" onChange={(e: React.ChangeEvent<HTMLInputElement>) => setFile(e.target.files?.[0] || null)} />
                                </div>
                                <div className="row">
                                    <div className="col-12 col-xl-4 d-flex flex-column justify-content-start gap-4">
                                        <div className="d-flex flex-column justify-content-center align-items-start gap-1">
                                            <label className="text-center font-default fs-14 default-text-color">Artwork</label>
                                            <img src={artworkPreview} className="img-fluid rounded-4 border-pink" alt="artwork" />
                                        </div>
                                        <Form.Control type="file" accept="image/*" onChange={(e: React.ChangeEvent<HTMLInputElement>) => setArtwork(e.target.files?.[0] || null)} />
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
                                            <div className="row">
                                                <div className="col-6">
                                                    <Form.Check type="switch" label="Make Public" checked={isVisible} onChange={(e) => setIsVisible(e.target.checked)} />
                                                    <Form.Check type="switch" label="Explicit" checked={explicit} onChange={(e) => setExplicit(e.target.checked)} />
                                                </div>
                                                <div className="col-6">
                                                    <Form.Check type="switch" label="Allow Comments" checked={allowComments} onChange={(e) => setAllowComments(e.target.checked)} />
                                                    <Form.Check type="switch" label="Allow Download" checked={allowDownload} onChange={(e) => setAllowDownload(e.target.checked)} />
                                                </div>
                                            </div>
                                            <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                                <label className="text-start font-default fs-14 default-text-color">Description</label>
                                                <Form.Control as="textarea" value={description} onChange={(e) => setDescription(e.target.value)} />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="d-flex flex-row justify-content-center align-items-center w-100 gap-4">
                                    <DefaultButton classList="btn-pink" onClick={upload} disabled={processing || !hasFile}>
                                        <FontAwesomeIcon icon={['fas', 'floppy-disk']} /> Upload
                                    </DefaultButton>
                                    <DefaultButton classList="btn-outline" disabled={processing} onClick={close}>
                                        <FontAwesomeIcon icon={['fas', 'trash']} /> Cancel
                                    </DefaultButton>
                                </div>
                            </div>
                        </Tab>
                    </Tabs>
                </div>
            </Modal.Body>
        </Modal>
    );
}
