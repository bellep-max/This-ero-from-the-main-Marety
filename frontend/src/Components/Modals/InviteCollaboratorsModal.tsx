import React from 'react';
import { Modal } from 'react-bootstrap';
import apiClient from '@/api/client';

interface InviteCollaboratorsModalProps {
    show: boolean;
    onClose: () => void;
    playlist_uuid: string;
    following?: any[];
}

export default function InviteCollaboratorsModal({ show, onClose, playlist_uuid, following = [] }: InviteCollaboratorsModalProps) {
    const submit = (user: any) => {
        apiClient.post(`/playlists/${playlist_uuid}/collaborators`, {
            collaborator_uuid: user.uuid,
        });
    };

    return (
        <Modal show={show} onHide={onClose} centered>
            <Modal.Body className="d-flex flex-column p-4">
                <div className="text-center font-default fs-5 mb-3">
                    Invite Collaborators
                </div>
                <div className="row w-100 playlist-container overflow-y-auto">
                    {following.map((user: any) => (
                        <div key={user.uuid} className="col-12 d-flex justify-content-between align-items-center p-2 border-bottom">
                            <span>{user.name || user.username}</span>
                            <button className="btn btn-sm btn-default btn-pink" onClick={() => submit(user)}>
                                Invite
                            </button>
                        </div>
                    ))}
                </div>
            </Modal.Body>
        </Modal>
    );
}
