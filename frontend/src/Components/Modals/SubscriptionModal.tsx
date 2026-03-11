import React from 'react';
import { Modal } from 'react-bootstrap';
import DefaultButton from '@/Components/Buttons/DefaultButton';

interface SubscriptionModalProps {
    show: boolean;
    onClose: () => void;
}

export default function SubscriptionModal({ show, onClose }: SubscriptionModalProps) {
    return (
        <Modal show={show} onHide={onClose} centered>
            <Modal.Body className="d-flex flex-column p-3 p-md-5">
                <div className="text-center font-default fs-5">Subscription</div>
                <p className="font-default fs-14">
                    This feature requires a subscription.
                </p>
                <p className="font-default fs-6">
                    Subscribe to unlock premium features.
                </p>
                <div className="d-flex flex-row justify-content-end align-items-center mt-5 w-100">
                    <a href="/settings/subscription" className="btn btn-default btn-pink">Subscribe</a>
                </div>
            </Modal.Body>
        </Modal>
    );
}
