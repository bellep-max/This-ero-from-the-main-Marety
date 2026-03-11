import React from 'react';
import { Modal } from 'react-bootstrap';
import DefaultButton from '@/Components/Buttons/DefaultButton';

interface ConfirmDeletionModalProps {
    show: boolean;
    onClose: () => void;
    onConfirm: () => void;
    title: string;
    type: string;
}

export default function ConfirmDeletionModal({ show, onClose, onConfirm, title, type }: ConfirmDeletionModalProps) {
    return (
        <Modal
            show={show}
            onHide={onClose}
            centered
            contentClassName="bg-default rounded-4"
        >
            <Modal.Body className="d-flex flex-column p-3 p-md-5">
                <div className="text-center font-default fs-5">
                    Delete {type}
                </div>
                <p className="font-default fs-14">
                    Are you sure you want to delete "{title}"?
                </p>
                <div className="d-flex flex-row justify-content-between align-items-center mt-5 w-100">
                    <DefaultButton classList="btn-outline" onClick={onClose}>Cancel</DefaultButton>
                    <DefaultButton classList="btn-pink" onClick={onConfirm}>Yes</DefaultButton>
                </div>
            </Modal.Body>
        </Modal>
    );
}
