import React from 'react';
import { Modal } from 'react-bootstrap';

interface AdventureMapModalProps {
    show: boolean;
    onClose: () => void;
    adventure: any;
}

export default function AdventureMapModal({ show, onClose, adventure }: AdventureMapModalProps) {
    return (
        <Modal show={show} onHide={onClose} centered size="lg">
            <Modal.Body>
                <div className="container-fluid d-flex flex-column w-100 h-100">
                    <div className="w-100 text-center font-default fs-5 mb-2">
                        Adventure Map
                    </div>
                    <div className="d-flex flex-column gap-2">
                        <div className="fw-bold">{adventure?.title} (Heading)</div>
                        {adventure?.children?.map((root: any, rootIndex: number) => (
                            <div key={root.uuid || rootIndex} className="ms-3">
                                <div className="fw-semibold">{root.title} (Root {rootIndex + 1})</div>
                                {root.children?.map((final: any, finalIndex: number) => (
                                    <div key={final.uuid || finalIndex} className="ms-4">
                                        {final.title} (Final {finalIndex + 1})
                                    </div>
                                ))}
                            </div>
                        ))}
                    </div>
                </div>
            </Modal.Body>
        </Modal>
    );
}
