import React, { useState } from 'react';
import { Modal, Form } from 'react-bootstrap';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import apiClient from '@/api/client';

interface LoginModalProps {
    show: boolean;
    onClose: () => void;
    onSignup?: () => void;
    onResetPassword?: () => void;
}

export default function LoginModal({ show, onClose, onSignup, onResetPassword }: LoginModalProps) {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [errors, setErrors] = useState<Record<string, string>>({});

    const loginAttempt = async () => {
        try {
            await apiClient.post('/login', { username, password });
            setUsername('');
            setPassword('');
            setErrors({});
            onClose();
        } catch (err: any) {
            if (err.response?.data?.errors) {
                setErrors(err.response.data.errors);
            }
        }
    };

    const handleKeyUp = (e: React.KeyboardEvent) => {
        if (e.key === 'Enter') {
            loginAttempt();
        }
    };

    return (
        <Modal show={show} onHide={onClose} centered>
            <Modal.Body>
                <div className="d-flex flex-column flex-md-row">
                    <div className="col col-md-5 d-flex flex-column justify-content-center align-items-center gap-3 ps-md-5">
                        <div className="text-center font-default fs-5">Sign In</div>
                        <div className="col-12">
                            <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                <label htmlFor="username" className="font-default fs-14 default-text-color">Username</label>
                                <Form.Control
                                    type="text"
                                    value={username}
                                    onChange={(e) => setUsername(e.target.value)}
                                    onKeyUp={handleKeyUp}
                                />
                            </div>
                        </div>
                        <div className="col-12">
                            <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                                <label htmlFor="password" className="font-default fs-14 default-text-color">Password</label>
                                <Form.Control
                                    type="password"
                                    value={password}
                                    onChange={(e) => setPassword(e.target.value)}
                                    onKeyUp={handleKeyUp}
                                />
                                <a className="navigation-link ms-auto" onClick={onResetPassword} style={{ cursor: 'pointer' }}>
                                    Reset Password
                                </a>
                            </div>
                        </div>
                        <DefaultButton classList="btn-pink" onClick={loginAttempt}>Login</DefaultButton>
                        <div className="d-flex flex-row justify-content-center align-items-center">
                            <a className="navigation-link" onClick={onSignup} style={{ cursor: 'pointer' }}>
                                Don't have an account?
                            </a>
                        </div>
                    </div>
                    <div className="col d-none d-md-flex">
                        <img src="/assets/images/login.webp" className="object-fit-scale" alt="Login Icon" />
                    </div>
                </div>
            </Modal.Body>
        </Modal>
    );
}
