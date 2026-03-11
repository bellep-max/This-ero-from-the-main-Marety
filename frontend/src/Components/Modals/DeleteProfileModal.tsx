import React from 'react';
import { Modal } from 'react-bootstrap';
import DefaultButton from '@/Components/Buttons/DefaultButton';

interface DeleteProfileModalProps {
    show: boolean;
    onClose: () => void;
    onAccept?: () => void;
}

export default function DeleteProfileModal({ show, onClose, onAccept }: DeleteProfileModalProps) {
    return (
        <Modal show={show} onHide={onClose} centered>
            <Modal.Body className="d-flex flex-column p-4">
                <span className="w-100 text-center font-default fs-5">
                    Delete Profile
                </span>
                <span className="w-100 text-center font-merge fs-6">
                    Are you sure you want to delete your profile? This action cannot be undone.
                </span>
                <div className="col-12 d-flex flex-row justify-content-center align-items-center gap-4 mt-5">
                    <DefaultButton classList="btn-pink" onClick={onAccept}>
                        Confirm Delete Account
                    </DefaultButton>
                    <DefaultButton classList="btn-outline" onClick={onClose}>
                        Cancel
                    </DefaultButton>
                </div>
            </Modal.Body>
        </Modal>
    );
}
