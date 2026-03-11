import React, { useState } from 'react';
import { Modal, Form } from 'react-bootstrap';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import apiClient from '@/api/client';

interface ReportModalProps {
    show: boolean;
    onClose: () => void;
    item: any;
    type: string;
}

export default function ReportModal({ show, onClose, item, type }: ReportModalProps) {
    const [reportType, setReportType] = useState<string | null>(null);

    const submit = async () => {
        try {
            await apiClient.post('/report', {
                report_type: reportType,
                type,
                uuid: item?.uuid,
            });
            onClose();
        } catch (err) {
            console.error('Report failed:', err);
        }
    };

    return (
        <Modal show={show} onHide={onClose} centered>
            <Modal.Body className="d-flex flex-column p-3 p-md-5">
                <div className="text-center font-default fs-5">Report</div>
                <p className="font-default fs-14">Why are you reporting this content?</p>
                <div>
                    <Form.Check
                        type="radio"
                        name="reportType"
                        label="Wrong Author"
                        value="1"
                        checked={reportType === '1'}
                        onChange={(e) => setReportType(e.target.value)}
                    />
                    <Form.Check
                        type="radio"
                        name="reportType"
                        label="Audio Problem"
                        value="2"
                        checked={reportType === '2'}
                        onChange={(e) => setReportType(e.target.value)}
                    />
                    <Form.Check
                        type="radio"
                        name="reportType"
                        label="Undesirable Content"
                        value="3"
                        checked={reportType === '3'}
                        onChange={(e) => setReportType(e.target.value)}
                    />
                </div>
                <div className="d-flex flex-row justify-content-between align-items-center mt-5 w-100">
                    <DefaultButton classList="btn-outline" onClick={onClose}>Cancel</DefaultButton>
                    <DefaultButton classList="btn-pink" disabled={!reportType} onClick={submit}>Submit</DefaultButton>
                </div>
            </Modal.Body>
        </Modal>
    );
}
