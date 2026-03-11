import React, { useState, useEffect, useCallback } from 'react';
import { Modal, Form, Spinner, Tab, Tabs, Collapse } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import apiClient from '@/api/client';
import { useAuthStore } from '@/stores/auth';

interface AdventureUploadModalProps {
    show: boolean;
    onClose: () => void;
}

interface RootForm {
    is_uploaded: boolean;
    parent_uuid: string | null;
    title: string | null;
    description: string | null;
    file: File | null;
    artwork: File | null;
    order: number;
    finals_number: number;
    finals: FinalForm[];
}

interface FinalForm {
    title: string | null;
    description: string | null;
    file: File | null;
    artwork: File | null;
    order: number;
}

interface DataLayout {
    inputAllowed: boolean;
    titleLength: number;
    artworkPreview: string;
    validateFileSize: boolean;
}

const defaultDataLayout: DataLayout = {
    inputAllowed: true,
    titleLength: 0,
    artworkPreview: '/assets/images/song.png',
    validateFileSize: false,
};

export default function AdventureUploadModal({ show, onClose }: AdventureUploadModalProps) {
    const user = useAuthStore((state) => state.user);
    const maxAllowed = 60;
    const allowedFileSize = 92970;

    const [isLoading, setIsLoading] = useState(false);
    const [loadingMessage, setLoadingMessage] = useState<string | null>(null);
    const [parentUuid, setParentUuid] = useState<string | null>(null);
    const [activeTab, setActiveTab] = useState('heading');
    const [errors, setErrors] = useState<Record<string, string | null>>({});

    const [headingTitle, setHeadingTitle] = useState('');
    const [headingDescription, setHeadingDescription] = useState('');
    const [headingFile, setHeadingFile] = useState<File | null>(null);
    const [headingArtwork, setHeadingArtwork] = useState<File | null>(null);
    const [headingArtworkPreview, setHeadingArtworkPreview] = useState('/assets/images/song.png');
    const [headingIsVisible, setHeadingIsVisible] = useState(false);
    const [headingAllowComments, setHeadingAllowComments] = useState(false);
    const [headingRootsCount, setHeadingRootsCount] = useState(1);
    const [headingTitleLength, setHeadingTitleLength] = useState(0);
    const [headingInputAllowed, setHeadingInputAllowed] = useState(true);
    const [headingValidateFileSize, setHeadingValidateFileSize] = useState(false);

    const [rootsForms, setRootsForms] = useState<RootForm[]>([]);
    const [rootsData, setRootsData] = useState<(DataLayout & { finals: DataLayout[] })[]>([]);
    const [openFinals, setOpenFinals] = useState<Record<string, boolean>>({});

    const hasUploadedHeading = !!parentUuid;

    useEffect(() => {
        setHeadingTitleLength(headingTitle?.length ?? 0);
        setHeadingInputAllowed((headingTitle?.length ?? 0) < maxAllowed);
    }, [headingTitle]);

    useEffect(() => {
        if (headingArtwork) {
            setHeadingArtworkPreview(URL.createObjectURL(headingArtwork));
        } else {
            setHeadingArtworkPreview('/assets/images/song.png');
        }
    }, [headingArtwork]);

    useEffect(() => {
        if (headingFile) {
            setHeadingValidateFileSize(Math.round(headingFile.size / 1024) <= allowedFileSize);
        } else {
            setHeadingValidateFileSize(false);
        }
    }, [headingFile]);

    useEffect(() => {
        generateForms();
    }, [headingRootsCount]);

    const generateForms = useCallback((rootIndex?: number) => {
        if (rootIndex === undefined) {
            const newRoots: RootForm[] = [];
            const newData: (DataLayout & { finals: DataLayout[] })[] = [];
            for (let i = 0; i < headingRootsCount; i++) {
                newRoots.push({
                    is_uploaded: false,
                    parent_uuid: parentUuid,
                    title: null,
                    description: null,
                    file: null,
                    artwork: null,
                    order: i + 1,
                    finals_number: 1,
                    finals: [{ title: null, description: null, file: null, artwork: null, order: 1 }],
                });
                newData.push({ ...defaultDataLayout, finals: [{ ...defaultDataLayout }] });
            }
            setRootsForms(newRoots);
            setRootsData(newData);
        } else {
            setRootsForms((prev) => {
                const updated = [...prev];
                const root = updated[rootIndex];
                root.finals = [];
                for (let i = 0; i < root.finals_number; i++) {
                    root.finals.push({ title: null, description: null, file: null, artwork: null, order: i + 1 });
                }
                return updated;
            });
            setRootsData((prev) => {
                const updated = [...prev];
                updated[rootIndex].finals = [];
                for (let i = 0; i < rootsForms[rootIndex]?.finals_number; i++) {
                    updated[rootIndex].finals.push({ ...defaultDataLayout });
                }
                return updated;
            });
        }
    }, [headingRootsCount, parentUuid]);

    const uploadHeading = async () => {
        setIsLoading(true);
        setLoadingMessage('Uploading heading...');
        try {
            const formData = new FormData();
            formData.append('type', 'heading');
            formData.append('title', headingTitle);
            formData.append('description', headingDescription || '');
            formData.append('is_visible', String(headingIsVisible));
            formData.append('allow_comments', String(headingAllowComments));
            formData.append('roots', String(headingRootsCount));
            if (headingFile) formData.append('file', headingFile);
            if (headingArtwork) formData.append('artwork', headingArtwork);

            const response = await apiClient.post('/upload/adventures/heading', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            setParentUuid(response.data.uuid);
            setLoadingMessage(null);
            setIsLoading(false);
            setActiveTab('root-0');
        } catch {
            setIsLoading(false);
            setLoadingMessage(null);
        }
    };

    const uploadRoot = async (index: number) => {
        setIsLoading(true);
        setLoadingMessage(`Uploading root ${index + 1}...`);
        try {
            const root = rootsForms[index];
            const formData = new FormData();
            formData.append('parent_uuid', parentUuid || '');
            formData.append('title', root.title || '');
            formData.append('description', root.description || '');
            formData.append('order', String(root.order));
            if (root.file) formData.append('file', root.file);
            if (root.artwork) formData.append('artwork', root.artwork);

            root.finals.forEach((final, fi) => {
                formData.append(`finals[${fi}][title]`, final.title || '');
                formData.append(`finals[${fi}][description]`, final.description || '');
                formData.append(`finals[${fi}][order]`, String(final.order));
                if (final.file) formData.append(`finals[${fi}][file]`, final.file);
                if (final.artwork) formData.append(`finals[${fi}][artwork]`, final.artwork);
            });

            await apiClient.post('/upload/adventures/root', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            setIsLoading(false);
            setLoadingMessage(null);
            setRootsForms((prev) => {
                const updated = [...prev];
                updated[index].is_uploaded = true;
                return updated;
            });
            const nextTab = index + 1 < rootsForms.length ? `root-${index + 1}` : 'heading';
            setActiveTab(nextTab);
        } catch {
            setIsLoading(false);
            setLoadingMessage(null);
        }
    };

    const updateRootField = (rootIndex: number, field: string, value: any) => {
        setRootsForms((prev) => {
            const updated = [...prev];
            (updated[rootIndex] as any)[field] = value;
            return updated;
        });
    };

    const updateFinalField = (rootIndex: number, finalIndex: number, field: string, value: any) => {
        setRootsForms((prev) => {
            const updated = [...prev];
            (updated[rootIndex].finals[finalIndex] as any)[field] = value;
            return updated;
        });
    };

    const close = () => {
        setHeadingTitle('');
        setHeadingDescription('');
        setHeadingFile(null);
        setHeadingArtwork(null);
        setParentUuid(null);
        setActiveTab('heading');
        setRootsForms([]);
        setRootsData([]);
        onClose();
    };

    const cancel = async () => {
        if (hasUploadedHeading) {
            try {
                await apiClient.post('/upload/adventure/destroy', { uuid: parentUuid });
            } catch (err) {
                console.error('Error cancelling upload:', err);
            }
        }
        close();
    };

    return (
        <Modal show={show} onHide={cancel} centered size="xl" backdrop={isLoading || hasUploadedHeading ? 'static' : true} keyboard={!isLoading && !hasUploadedHeading}>
            <Modal.Body>
                <div className="container-fluid overflow-y-auto">
                    <div className="w-100 text-center font-default fs-5 mb-2">Upload Adventure</div>
                    {isLoading && (
                        <div className="d-flex flex-column w-100 text-center align-items-center justify-content-center gap-4 my-3">
                            <span className="font-default">Please wait...</span>
                            {loadingMessage && <span>{loadingMessage}</span>}
                            <Spinner animation="border" variant="danger" />
                        </div>
                    )}
                    <Tabs activeKey={activeTab} onSelect={(k) => k && setActiveTab(k)} className="tabs-header w-100" fill>
                        <Tab eventKey="heading" title="Heading" disabled={hasUploadedHeading}>
                            <div className="row pt-3">
                                <div className="col-12 mb-4">
                                    <div className="d-flex flex-column justify-content-center align-items-start gap-1">
                                        <label className="text-center font-default fs-14 default-text-color">Select File</label>
                                        <Form.Control type="file" accept="audio/*" onChange={(e: React.ChangeEvent<HTMLInputElement>) => setHeadingFile(e.target.files?.[0] || null)} />
                                    </div>
                                </div>
                                <div className="col-12 col-xl-4">
                                    <div className="d-flex flex-column justify-content-start w-100 gap-4">
                                        <div className="d-flex flex-column justify-content-center align-items-start gap-1">
                                            <label className="text-center font-default fs-14 default-text-color">Artwork</label>
                                            <img src={headingArtworkPreview} className="img-fluid rounded-4 border-pink" alt="artwork" />
                                        </div>
                                        <Form.Control type="file" accept="image/*" onChange={(e: React.ChangeEvent<HTMLInputElement>) => setHeadingArtwork(e.target.files?.[0] || null)} />
                                    </div>
                                </div>
                                <div className="col">
                                    <div className="d-flex flex-column justify-content-start w-100 gap-4">
                                        <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                            <label className="text-start font-default fs-14 default-text-color">Title</label>
                                            <Form.Control type="text" value={headingTitle} onChange={(e) => setHeadingTitle(e.target.value)} maxLength={maxAllowed} />
                                            <span className={`fs-12 font-merge ${!headingInputAllowed ? 'color-pink' : 'color-grey'}`}>
                                                {headingTitleLength}/{maxAllowed} characters
                                            </span>
                                        </div>
                                        <div className="d-flex flex-row justify-content-between align-items-end gap-3">
                                            <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                                <label className="text-start font-default fs-14 default-text-color">Roots</label>
                                                <Form.Select value={headingRootsCount} onChange={(e) => setHeadingRootsCount(Number(e.target.value))}>
                                                    {[1, 2, 3, 4, 5].map((n) => (
                                                        <option key={n} value={n}>{n}</option>
                                                    ))}
                                                </Form.Select>
                                            </div>
                                            <div className="d-flex flex-column justify-content-start align-items-center gap-3">
                                                <div className="d-flex flex-row justify-content-between align-items-center gap-3">
                                                    <Form.Check type="switch" label="Make Public" checked={headingIsVisible} onChange={(e) => setHeadingIsVisible(e.target.checked)} />
                                                    <Form.Check type="switch" label="Allow Comments" checked={headingAllowComments} onChange={(e) => setHeadingAllowComments(e.target.checked)} />
                                                </div>
                                            </div>
                                        </div>
                                        <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                            <label className="text-start font-default fs-14 default-text-color">Description</label>
                                            <Form.Control as="textarea" value={headingDescription} onChange={(e) => setHeadingDescription(e.target.value)} />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="d-flex flex-row justify-content-center align-items-center w-100 gap-4 mt-4">
                                <DefaultButton classList="btn-pink" disabled={isLoading || !headingFile} onClick={uploadHeading}>
                                    <FontAwesomeIcon icon={['fas', 'angles-right']} /> Upload Heading & Proceed
                                </DefaultButton>
                                <DefaultButton classList="btn-outline" disabled={isLoading} onClick={close}>
                                    <FontAwesomeIcon icon={['fas', 'trash']} /> Cancel
                                </DefaultButton>
                            </div>
                        </Tab>
                        {rootsForms.map((root, rootIndex) => (
                            <Tab
                                key={rootIndex}
                                eventKey={`root-${rootIndex}`}
                                title={`Root ${rootIndex + 1}`}
                                disabled={!hasUploadedHeading || root.is_uploaded}
                            >
                                <div className="row py-2 px-3 gap-3 pt-3">
                                    <Form.Control type="file" accept="audio/*" onChange={(e: React.ChangeEvent<HTMLInputElement>) => updateRootField(rootIndex, 'file', e.target.files?.[0] || null)} />
                                    <div className="col-12 col-xl-4">
                                        <div className="d-flex flex-column justify-content-start w-100 gap-4">
                                            <div className="d-flex flex-column justify-content-center align-items-start gap-1">
                                                <label className="text-center font-default fs-14 default-text-color">Artwork</label>
                                                <img src={rootsData[rootIndex]?.artworkPreview || '/assets/images/song.png'} className="img-fluid rounded-4 border-pink" alt="artwork" />
                                            </div>
                                            <Form.Control type="file" accept="image/*" onChange={(e: React.ChangeEvent<HTMLInputElement>) => updateRootField(rootIndex, 'artwork', e.target.files?.[0] || null)} />
                                        </div>
                                    </div>
                                    <div className="col">
                                        <div className="d-flex flex-column justify-content-start w-100 gap-4">
                                            <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                                <label className="text-start font-default fs-14 default-text-color">Title</label>
                                                <Form.Control type="text" value={root.title || ''} onChange={(e) => updateRootField(rootIndex, 'title', e.target.value)} maxLength={maxAllowed} />
                                            </div>
                                            <div className="d-flex flex-column w-100 justify-content-start align-items-start gap-1">
                                                <label className="text-start font-default fs-14 default-text-color">Finals</label>
                                                <Form.Select value={root.finals_number} onChange={(e) => { updateRootField(rootIndex, 'finals_number', Number(e.target.value)); generateForms(rootIndex); }}>
                                                    {[1, 2, 3].map((n) => (
                                                        <option key={n} value={n}>{n}</option>
                                                    ))}
                                                </Form.Select>
                                            </div>
                                            <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                                <label className="text-start font-default fs-14 default-text-color">Description</label>
                                                <Form.Control as="textarea" value={root.description || ''} onChange={(e) => updateRootField(rootIndex, 'description', e.target.value)} />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="row px-4">
                                    {root.finals.map((final, finalIndex) => {
                                        const key = `${rootIndex}-${finalIndex}`;
                                        const isOpen = openFinals[key] || false;
                                        return (
                                            <div key={finalIndex} className="w-100">
                                                <DefaultButton
                                                    classList={`my-2 w-100 ${isOpen ? 'btn-pink' : 'btn-outline'}`}
                                                    onClick={() => setOpenFinals((prev) => ({ ...prev, [key]: !prev[key] }))}
                                                >
                                                    {isOpen ? 'Close' : 'Open'} Final {finalIndex + 1}
                                                </DefaultButton>
                                                <Collapse in={isOpen}>
                                                    <div className="row py-2 px-3 gap-3">
                                                        <Form.Control type="file" accept="audio/*" onChange={(e: React.ChangeEvent<HTMLInputElement>) => updateFinalField(rootIndex, finalIndex, 'file', e.target.files?.[0] || null)} />
                                                        <div className="col-12 col-xl-4">
                                                            <div className="d-flex flex-column justify-content-start w-100 gap-4">
                                                                <label className="text-center font-default fs-14 default-text-color">Artwork</label>
                                                                <img src={rootsData[rootIndex]?.finals?.[finalIndex]?.artworkPreview || '/assets/images/song.png'} className="img-fluid rounded-4 border-pink" alt="artwork" />
                                                                <Form.Control type="file" accept="image/*" onChange={(e: React.ChangeEvent<HTMLInputElement>) => updateFinalField(rootIndex, finalIndex, 'artwork', e.target.files?.[0] || null)} />
                                                            </div>
                                                        </div>
                                                        <div className="col">
                                                            <div className="d-flex flex-column justify-content-start w-100 gap-4">
                                                                <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                                                    <label className="text-start font-default fs-14 default-text-color">Title</label>
                                                                    <Form.Control type="text" value={final.title || ''} onChange={(e) => updateFinalField(rootIndex, finalIndex, 'title', e.target.value)} maxLength={maxAllowed} />
                                                                </div>
                                                                <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                                                    <label className="text-start font-default fs-14 default-text-color">Description</label>
                                                                    <Form.Control as="textarea" value={final.description || ''} onChange={(e) => updateFinalField(rootIndex, finalIndex, 'description', e.target.value)} />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </Collapse>
                                            </div>
                                        );
                                    })}
                                </div>
                                <div className="d-flex flex-row justify-content-center align-items-center w-100 gap-4 mt-4">
                                    <DefaultButton
                                        classList="btn-pink w-100"
                                        disabled={isLoading || !root.file || root.is_uploaded}
                                        onClick={() => uploadRoot(rootIndex)}
                                    >
                                        <FontAwesomeIcon icon={['fas', 'angles-right']} /> Upload Root {rootIndex + 1} & Finals
                                    </DefaultButton>
                                </div>
                            </Tab>
                        ))}
                    </Tabs>
                </div>
            </Modal.Body>
        </Modal>
    );
}
