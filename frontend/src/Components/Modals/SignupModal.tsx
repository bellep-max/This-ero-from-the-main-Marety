import React, { useState, useEffect } from 'react';
import { Modal, Form } from 'react-bootstrap';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import apiClient from '@/api/client';

interface SignupModalProps {
    show: boolean;
    onClose: () => void;
}

export default function SignupModal({ show, onClose }: SignupModalProps) {
    const [email, setEmail] = useState('');
    const [name, setName] = useState('');
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [passwordConfirmation, setPasswordConfirmation] = useState('');
    const [over18, setOver18] = useState(false);
    const [role, setRole] = useState('listener');
    const [isCustomUsername, setIsCustomUsername] = useState(false);
    const [currentLocation, setCurrentLocation] = useState('');
    const [customLocation, setCustomLocation] = useState('');
    const [isLoading, setIsLoading] = useState(true);
    const [errors, setErrors] = useState<Record<string, string>>({});

    const slugify = (text: string) => {
        return text
            .toString()
            .toLowerCase()
            .replace(/\s+/g, '-')
            .replace(/[^\w-]+/g, '')
            .replace(/--+/g, '-')
            .trim();
    };

    useEffect(() => {
        const loc = `${window.location.origin}/user/`;
        setCurrentLocation(loc);
        setCustomLocation(loc);
    }, []);

    useEffect(() => {
        if (name) {
            const slugged = slugify(name);
            setUsername(slugged);
            setCustomLocation(currentLocation + slugged);
        }
    }, [name, currentLocation]);

    useEffect(() => {
        setCustomLocation(currentLocation + username);
    }, [username, currentLocation]);

    const signup = async () => {
        const formUsername = isCustomUsername ? username : slugify(name);
        try {
            await apiClient.post('/register', {
                email,
                name,
                username: formUsername,
                password,
                password_confirmation: passwordConfirmation,
                over_18: over18,
                role,
            });
            setEmail('');
            setName('');
            setUsername('');
            setPassword('');
            setPasswordConfirmation('');
            setOver18(false);
            setIsCustomUsername(false);
            setErrors({});
            onClose();
        } catch (err: any) {
            if (err.response?.data?.errors) {
                setErrors(err.response.data.errors);
            }
        }
    };

    return (
        <Modal show={show} onHide={onClose} centered size="lg">
            <Modal.Body>
                <div className="d-flex flex-column flex-md-row">
                    <div className="col col-md-5 d-flex flex-column justify-content-center align-items-center gap-3 ps-md-5">
                        <div className="text-center font-default fs-5">Sign Up</div>
                        <div className="col-12">
                            <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                <label className="font-default fs-14 default-text-color">Email</label>
                                <Form.Control type="email" value={email} onChange={(e) => setEmail(e.target.value)} />
                                {errors.email && <div className="text-danger">{errors.email}</div>}
                            </div>
                        </div>
                        <div className="col-12">
                            <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                <label className="font-default fs-14 default-text-color">Name</label>
                                <Form.Control type="text" value={name} onChange={(e) => setName(e.target.value)} />
                                {errors.name && <div className="text-danger">{errors.name}</div>}
                            </div>
                        </div>
                        <div className="col-12">
                            <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                <div className="d-flex flex-row justify-content-between align-items-center w-100">
                                    <label className="font-default fs-14 default-text-color">Username</label>
                                    <Form.Check
                                        type="checkbox"
                                        label="Custom username"
                                        checked={isCustomUsername}
                                        onChange={(e) => setIsCustomUsername(e.target.checked)}
                                    />
                                </div>
                                {isCustomUsername ? (
                                    <div className="input-group">
                                        <span className="input-group-text">{currentLocation}</span>
                                        <Form.Control type="text" value={username} onChange={(e) => setUsername(e.target.value)} />
                                    </div>
                                ) : (
                                    <Form.Control type="text" readOnly plaintext placeholder={customLocation} />
                                )}
                                {errors.username && <div className="text-danger">{errors.username}</div>}
                            </div>
                        </div>
                        <div className="col-12">
                            <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                <label className="font-default fs-14 default-text-color">Password</label>
                                <Form.Control type="password" value={password} onChange={(e) => setPassword(e.target.value)} />
                                {errors.password && <div className="text-danger">{errors.password}</div>}
                            </div>
                        </div>
                        <div className="col-12">
                            <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                <label className="font-default fs-14 default-text-color">Retype Password</label>
                                <Form.Control type="password" value={passwordConfirmation} onChange={(e) => setPasswordConfirmation(e.target.value)} />
                            </div>
                        </div>
                        <div className="col-12">
                            <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                <label className="font-default fs-14 default-text-color">Choose Role</label>
                                <Form.Check type="radio" name="role" label="Listener" value="listener" checked={role === 'listener'} onChange={(e) => setRole(e.target.value)} />
                                <Form.Check type="radio" name="role" label="Creator" value="creator" checked={role === 'creator'} onChange={(e) => setRole(e.target.value)} />
                            </div>
                        </div>
                        <Form.Check
                            type="checkbox"
                            label="I agree to the terms and am over 18"
                            checked={over18}
                            onChange={(e) => setOver18(e.target.checked)}
                        />
                        <DefaultButton classList="btn-pink" onClick={signup}>Sign Up</DefaultButton>
                    </div>
                    <div className="col d-none d-md-flex image-container">
                        {isLoading && <div className="loading-state">Loading...</div>}
                        <img
                            src="/assets/images/signup.webp"
                            className="object-fit-scale"
                            alt="Signup image"
                            style={isLoading ? { display: 'none' } : {}}
                            onLoad={() => setIsLoading(false)}
                        />
                    </div>
                </div>
            </Modal.Body>
        </Modal>
    );
}
