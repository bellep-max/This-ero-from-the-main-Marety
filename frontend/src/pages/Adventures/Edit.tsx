import React, { useState, useEffect } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import apiClient from '@/api/client';
import { useAuthStore } from '@/stores/auth';
import UserLayout from '@/Layouts/UserLayout';

export default function AdventuresEdit() {
    const { uuid } = useParams<{ uuid: string }>();
    const navigate = useNavigate();
    const [adventure, setAdventure] = useState<any>(null);
    const [user, setUser] = useState<any>(null);
    const [loading, setLoading] = useState(true);

    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [isVisible, setIsVisible] = useState(true);
    const [allowComments, setAllowComments] = useState(true);
    const [allowDownload, setAllowDownload] = useState(false);
    const [artworkPreview, setArtworkPreview] = useState<string>('');
    const [artworkFile, setArtworkFile] = useState<File | null>(null);
    const [submitting, setSubmitting] = useState(false);
    const [activeTab, setActiveTab] = useState(0);

    const maxAllowed = 60;
    const titleLength = title.length;
    const inputAllowed = titleLength < maxAllowed;

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await apiClient.get(`/adventures/${uuid}/edit`);
                const apiData = response.data;
                const adventureData = apiData.adventure ?? null;
                setAdventure(adventureData);
                setUser(apiData.user ?? null);
                if (adventureData) {
                    setTitle(adventureData.title || '');
                    setDescription(adventureData.description || '');
                    setIsVisible(adventureData.is_visible ?? true);
                    setAllowComments(adventureData.allow_comments ?? true);
                    setAllowDownload(adventureData.allow_download ?? false);
                    setArtworkPreview(adventureData.artwork || '');
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
        } else if (adventure) {
            setArtworkPreview(adventure.artwork || '');
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
            if (artworkFile) formData.append('artwork', artworkFile);
            await apiClient.post(`/adventures/${uuid}`, formData);
        } catch (error) {
            console.error('Failed to update adventure:', error);
        } finally {
            setSubmitting(false);
        }
    };

    const handleDelete = async () => {
        if (window.confirm('Are you sure you want to delete this adventure?')) {
            try {
                await apiClient.delete(`/adventures/${uuid}`);
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

    if (!adventure) return null;

    return (
        <UserLayout user={user}>
            <div className="row gy-3">
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
                            <div className="col d-flex flex-row justify-content-between align-items-center">
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
                        <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                            <label className="text-start font-default fs-14 default-text-color">Description</label>
                            <textarea className="form-control" value={description} onChange={(e) => setDescription(e.target.value)} />
                        </div>
                    </div>
                </div>
                {adventure.children && adventure.children.length > 0 && (
                    <div className="col-12">
                        <ul className="nav nav-tabs tabs-header w-100">
                            {adventure.children.map((_: any, idx: number) => (
                                <li key={idx} className="nav-item">
                                    <button
                                        className={`nav-link tab-item default-text-color px-4 fs-5 font-merge ${activeTab === idx ? 'active tab-item-active' : ''}`}
                                        onClick={() => setActiveTab(idx)}
                                    >
                                        Root {idx + 1}
                                    </button>
                                </li>
                            ))}
                        </ul>
                        <div className="py-4">
                            {adventure.children.map((root: any, rootIndex: number) => (
                                activeTab === rootIndex && (
                                    <div key={rootIndex}>
                                        <div className="row">
                                            <div className="col-4">
                                                <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                                    <label className="text-center font-default fs-14 default-text-color">Artwork</label>
                                                    <img src={root.artwork} className="img-fluid rounded-4 border-pink" alt={`${rootIndex}_artwork`} />
                                                </div>
                                            </div>
                                            <div className="col-8">
                                                <div className="d-flex flex-column justify-content-start w-100 gap-4">
                                                    <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                                        <label className="text-start font-default fs-14 default-text-color">Title</label>
                                                        <input type="text" className="form-control" value={root.title || ''} disabled readOnly />
                                                    </div>
                                                    <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                                        <label className="text-start font-default fs-14 default-text-color">Description</label>
                                                        <textarea className="form-control" value={root.description || ''} disabled readOnly />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {root.children?.map((final_item: any, finalIndex: number) => (
                                            <div key={`${finalIndex}_${rootIndex}`} className="row mt-4">
                                                <div className="col-4">
                                                    <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                                        <label className="text-center font-default fs-14 default-text-color">Artwork</label>
                                                        <img src={final_item.artwork} className="img-fluid rounded-4 border-pink" alt={`${finalIndex}_${rootIndex}_artwork`} />
                                                    </div>
                                                </div>
                                                <div className="col-8">
                                                    <div className="d-flex flex-column justify-content-start w-100 gap-4">
                                                        <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                                            <label className="text-start font-default fs-14 default-text-color">Title</label>
                                                            <input type="text" className="form-control" value={final_item.title || ''} disabled readOnly />
                                                        </div>
                                                        <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                                            <label className="text-start font-default fs-14 default-text-color">Description</label>
                                                            <textarea className="form-control" value={final_item.description || ''} disabled readOnly />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                )
                            ))}
                        </div>
                    </div>
                )}
            </div>
            <div className="d-flex flex-row justify-content-center align-items-center gap-4 w-100 mt-5">
                <Link to={`/adventures/${adventure.uuid}`} className="btn btn-default btn-outline">Cancel</Link>
                <button className="btn btn-default btn-pink" onClick={handleSubmit} disabled={submitting}>Save</button>
                <button className="btn btn-default btn-outline" onClick={handleDelete}>
                    <i className="fas fa-trash" /> Delete
                </button>
            </div>
        </UserLayout>
    );
}
