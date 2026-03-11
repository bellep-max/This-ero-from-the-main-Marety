import React, { useState } from 'react';
import { Modal, Spinner } from 'react-bootstrap';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import apiClient from '@/api/client';

interface AdultOnlyModalProps {
    show: boolean;
    onClose: () => void;
    onAccepted?: () => void;
}

export default function AdultOnlyModal({ show, onClose, onAccepted }: AdultOnlyModalProps) {
    const [processing, setProcessing] = useState(false);

    const accept = async () => {
        setProcessing(true);
        try {
            await apiClient.post('/terms/accept', { accept: true });
            setProcessing(false);
            onAccepted?.();
        } catch {
            setProcessing(false);
        }
    };

    return (
        <Modal
            show={show}
            onHide={onClose}
            centered
            backdrop="static"
            keyboard={false}
            contentClassName="lightbox-warning-18 rounded-4 d-flex justify-content-center p-3 p-md-0"
        >
            <Modal.Body>
                <div className="d-flex flex-column justify-content-center align-items-center gap-4">
                    <h2 className="fs-3 font-default fw-bold text-center color-light">
                        Are you over 18?
                    </h2>
                    <p className="fs-5 font-merge color-light">
                        This content is for adults only.
                    </p>
                    <div className="d-flex flex-column flex-md-row justify-content-center align-items-center gap-3 gap-md-5 w-100">
                        <a href="https://google.com/" className="btn btn-default btn-pink w-100">
                            I am not 18
                        </a>
                        <div className="w-100 position-relative">
                            {processing && (
                                <div className="position-absolute top-50 start-50 translate-middle">
                                    <Spinner animation="border" variant="danger" />
                                </div>
                            )}
                            <DefaultButton classList="w-100 btn-outline" disabled={processing} onClick={accept}>
                                I am 18 or older
                            </DefaultButton>
                        </div>
                    </div>
                </div>
            </Modal.Body>
        </Modal>
    );
}
