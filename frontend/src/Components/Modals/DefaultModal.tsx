import React from 'react';
import { Modal } from 'react-bootstrap';

interface DefaultModalProps {
    show: boolean;
    onClose: () => void;
    title?: string;
    children?: React.ReactNode;
}

export default function DefaultModal({ show, onClose, title = '', children }: DefaultModalProps) {
    return (
        <Modal show={show} onHide={onClose} centered>
            {title && (
                <Modal.Header closeButton>
                    <Modal.Title>{title}</Modal.Title>
                </Modal.Header>
            )}
            <Modal.Body>{children}</Modal.Body>
        </Modal>
    );
}
