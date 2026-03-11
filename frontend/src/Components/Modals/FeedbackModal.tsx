import React, { useState, useEffect } from 'react';
import { Modal, Form } from 'react-bootstrap';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import apiClient from '@/api/client';
import { useAuthStore } from '@/stores/auth';

interface FeedbackModalProps {
    show: boolean;
    onClose: () => void;
}

export default function FeedbackModal({ show, onClose }: FeedbackModalProps) {
    const user = useAuthStore((state) => state.user);
    const [email, setEmail] = useState('');
    const [emotion, setEmotion] = useState('');
    const [about, setAbout] = useState('');
    const [message, setMessage] = useState('');

    const emotions = ['Angry', 'Confused', 'Happy', 'Amused', 'Impressed', 'Surprised', 'Disappointed', 'Frustrated', 'Curious', 'Indifferent'];
    const aboutOptions = [
        { value: 'Website', label: 'Website' },
        { value: 'General', label: 'General Questions' },
        { value: 'iPhone', label: 'iPhone' },
        { value: 'Android', label: 'Android' },
    ];

    useEffect(() => {
        if (user?.email) {
            setEmail(user.email);
        }
    }, [user]);

    const submit = () => {
        onClose();
    };

    const cancel = () => {
        setEmail(user?.email || '');
        setEmotion('');
        setAbout('');
        setMessage('');
        onClose();
    };

    return (
        <Modal show={show} onHide={onClose} centered>
            <Modal.Body className="d-flex flex-column p-3 p-md-5">
                <div className="text-center font-default fs-5">Feedback</div>
                <p className="font-default fs-14">We would love to hear your thoughts!</p>
                <div className="container-fluid">
                    <div className="row gy-3 gy-lg-4">
                        <div className="col-12">
                            <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                <label className="font-default fs-14 default-text-color">Email</label>
                                <Form.Control type="email" value={email} onChange={(e) => setEmail(e.target.value)} required />
                            </div>
                        </div>
                        <div className="col-12 col-lg-6">
                            <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                <label className="font-default fs-14 default-text-color">I feel:</label>
                                <Form.Select value={emotion} onChange={(e) => setEmotion(e.target.value)}>
                                    <option value="">Select...</option>
                                    {emotions.map((em) => (
                                        <option key={em} value={em}>{em}</option>
                                    ))}
                                </Form.Select>
                            </div>
                        </div>
                        <div className="col-12 col-lg-6">
                            <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                <label className="font-default fs-14 default-text-color">Feedback about:</label>
                                <Form.Select value={about} onChange={(e) => setAbout(e.target.value)}>
                                    <option value="">Select...</option>
                                    {aboutOptions.map((opt) => (
                                        <option key={opt.value} value={opt.value}>{opt.label}</option>
                                    ))}
                                </Form.Select>
                            </div>
                        </div>
                        <div className="col-12">
                            <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                <label className="font-default fs-14 default-text-color">Share your thoughts</label>
                                <Form.Control
                                    as="textarea"
                                    rows={3}
                                    value={message}
                                    onChange={(e) => setMessage(e.target.value)}
                                    placeholder="Share your thoughts"
                                />
                            </div>
                        </div>
                    </div>
                    <div className="d-flex flex-row justify-content-between align-items-center mt-5 w-100">
                        <DefaultButton classList="btn-outline" onClick={cancel}>Cancel</DefaultButton>
                        <DefaultButton classList="btn-pink" onClick={submit}>Submit</DefaultButton>
                    </div>
                </div>
            </Modal.Body>
        </Modal>
    );
}
