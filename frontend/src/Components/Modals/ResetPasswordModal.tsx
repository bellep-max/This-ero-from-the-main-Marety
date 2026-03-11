import React, { useState } from 'react';
import { Modal, Form } from 'react-bootstrap';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import apiClient from '@/api/client';

interface ResetPasswordModalProps {
    show: boolean;
    onClose: () => void;
}

export default function ResetPasswordModal({ show, onClose }: ResetPasswordModalProps) {
    const [email, setEmail] = useState('');

    const submit = () => {
        onClose();
    };

    return (
        <Modal show={show} onHide={onClose} centered>
            <Modal.Body className="d-flex flex-column p-3 p-md-5">
                <div className="text-center font-default fs-5">Reset Password</div>
                <p className="font-default fs-14">
                    Enter your email address and we will send you a link to reset your password.
                </p>
                <div className="row gy-3 gy-lg-4">
                    <div className="col-12">
                        <div className="d-flex flex-column justify-content-start align-items-start gap-1">
                            <label htmlFor="forget-email" className="font-default fs-14 default-text-color">Email</label>
                            <Form.Control type="email" value={email} onChange={(e) => setEmail(e.target.value)} />
                        </div>
                    </div>
                </div>
                <div className="d-flex flex-row justify-content-between align-items-center mt-5 w-100">
                    <DefaultButton classList="btn-outline" onClick={onClose}>Cancel</DefaultButton>
                    <DefaultButton classList="btn-pink" onClick={submit}>Submit</DefaultButton>
                </div>
            </Modal.Body>
        </Modal>
    );
}
