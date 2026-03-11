import React from 'react';
import { Modal } from 'react-bootstrap';
import DefaultButton from '@/Components/Buttons/DefaultButton';

interface AdventureBranchSelectModalProps {
    show: boolean;
    onClose: () => void;
    adventure?: any;
    onSelected?: (track: any) => void;
}

export default function AdventureBranchSelectModal({ show, onClose, adventure, onSelected }: AdventureBranchSelectModalProps) {
    return (
        <Modal show={show} onHide={onClose} centered backdrop="static" keyboard={false}>
            <Modal.Body>
                <div className="container-fluid overflow-y-auto d-flex flex-column">
                    <div className="w-100 text-center font-default fs-5 mb-2">
                        Choose Your Path
                    </div>
                    <div className="d-flex flex-row justify-content-center align-items-center gap-3">
                        {adventure?.children?.map((track: any) => (
                            <DefaultButton
                                key={track.uuid}
                                classList="btn-pink"
                                onClick={() => onSelected?.(track)}
                            >
                                {track.title}
                            </DefaultButton>
                        ))}
                    </div>
                </div>
            </Modal.Body>
        </Modal>
    );
}
