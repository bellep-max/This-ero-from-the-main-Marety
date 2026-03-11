import React from 'react';
import { Modal } from 'react-bootstrap';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import apiClient from '@/api/client';

interface DownloadModalProps {
    show: boolean;
    onClose: () => void;
    item?: any;
    type: string;
}

export default function DownloadModal({ show, onClose, item, type }: DownloadModalProps) {
    const downloadStandard = async () => {
        try {
            const response = await apiClient.get(`/downloads/${item?.uuid}/${type}`);
            window.location.href = response.data.download_url;
            onClose();
        } catch (err) {
            console.error('Download failed:', err);
        }
    };

    const downloadHQ = async () => {
        try {
            const response = await apiClient.get(`/downloads/${item?.uuid}/${type}/hd`);
            window.location.href = response.data.download_url;
            onClose();
        } catch (err) {
            console.error('HD Download failed:', err);
        }
    };

    return (
        <Modal show={show} onHide={onClose} centered>
            <Modal.Body className="d-flex flex-column p-4 gap-4">
                <DefaultButton classList="btn-pink" onClick={downloadStandard}>
                    Standard
                </DefaultButton>
                <DefaultButton classList="btn-pink" disabled={!item?.hd} onClick={downloadHQ}>
                    High Quality
                </DefaultButton>
            </Modal.Body>
        </Modal>
    );
}
