import React, { useState, useEffect, useMemo } from 'react';
import { Modal, Form, Tab, Tabs, InputGroup, Button } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import DefaultButton from '@/Components/Buttons/DefaultButton';

interface ShareModalProps {
    show: boolean;
    onClose: () => void;
    item?: any;
}

export default function ShareModal({ show, onClose, item }: ShareModalProps) {
    const [twitterPost, setTwitterPost] = useState('');
    const [itemLink, setItemLink] = useState('');
    const [embedTheme, setEmbedTheme] = useState(false);
    const [embedSize, setEmbedSize] = useState('large');
    const [embedCode, setEmbedCode] = useState('');
    const twitterMaxAllowed = 280;

    useEffect(() => {
        if (item && show) {
            const link = `${window.location.origin}/${item.type}/${item.uuid}`;
            setItemLink(link);
            setTwitterPost(item.title || item.name || '');
        }
    }, [item, show]);

    const copyToClipboard = (text: string) => {
        if (!text) return;
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text);
        } else {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.top = '-9999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try { document.execCommand('copy'); } catch {}
            document.body.removeChild(textArea);
        }
    };

    const facebookLink = useMemo(
        () => `https://www.facebook.com/share.php?u=${encodeURIComponent(itemLink)}&ref=songShare`,
        [itemLink]
    );
    const twitterLink = useMemo(
        () => `https://twitter.com/intent/tweet?url=${encodeURIComponent(itemLink)}&text=${encodeURIComponent(twitterPost)}`,
        [itemLink, twitterPost]
    );
    const pinterestLink = useMemo(
        () => `https://pinterest.com/pin/create/button/?url=${encodeURIComponent(itemLink)}&media=${encodeURIComponent(item?.artwork || '')}&description=${encodeURIComponent(item?.title || item?.name || '')}`,
        [itemLink, item]
    );
    const linkedinLink = useMemo(
        () => `https://www.linkedin.com/feed/?shareActive=true&text=${encodeURIComponent(item?.title || item?.name || '')}&shareUrl=${encodeURIComponent(itemLink)}`,
        [itemLink, item]
    );
    const emailLink = useMemo(
        () => `mailto:?subject=${encodeURIComponent(item?.title || item?.name || '')}&body=${encodeURIComponent(itemLink)}`,
        [itemLink, item]
    );

    return (
        <Modal show={show} onHide={onClose} centered size="lg">
            <Modal.Body className="d-flex flex-column p-4 gap-4">
                <Tabs defaultActiveKey="facebook" className="tabs-header w-100" fill>
                    {item && (
                        <Tab eventKey="embed" title="Embed">
                            <div className="d-flex flex-column w-100 justify-content-center align-items-center gap-3 pt-3">
                                <div className="d-flex flex-row justify-content-center align-items-center gap-4">
                                    <DefaultButton
                                        classList={embedSize === 'large' ? 'btn-pink btn-narrow' : 'btn-outline btn-narrow'}
                                        onClick={() => setEmbedSize('large')}
                                    >
                                        Picture
                                    </DefaultButton>
                                    <DefaultButton
                                        classList={embedSize === 'medium' ? 'btn-pink btn-narrow' : 'btn-outline btn-narrow'}
                                        onClick={() => setEmbedSize('medium')}
                                    >
                                        Classic
                                    </DefaultButton>
                                    <DefaultButton
                                        classList={embedSize === 'small' ? 'btn-pink btn-narrow' : 'btn-outline btn-narrow'}
                                        onClick={() => setEmbedSize('small')}
                                    >
                                        Mini
                                    </DefaultButton>
                                </div>
                                <Form.Check
                                    type="switch"
                                    label={embedTheme ? 'Dark' : 'Light'}
                                    checked={embedTheme}
                                    onChange={(e) => setEmbedTheme(e.target.checked)}
                                />
                                <Form.Control value={embedCode} readOnly onClick={() => copyToClipboard(embedCode)} />
                            </div>
                        </Tab>
                    )}
                    <Tab eventKey="facebook" title="Facebook">
                        <div className="d-flex flex-column w-100 justify-content-start align-items-center gap-1 pt-3">
                            <label className="font-default fs-14 default-text-color mx-auto">
                                Share on Facebook
                            </label>
                            <a href={facebookLink} target="_blank" rel="noopener noreferrer" className="btn btn-default btn-pink">
                                Share
                            </a>
                        </div>
                    </Tab>
                    <Tab eventKey="twitter" title="Twitter">
                        <div className="d-flex flex-column w-100 justify-content-start align-items-start gap-3 pt-3">
                            <label className="font-default fs-14 default-text-color mx-auto">Share on Twitter</label>
                            <Form.Control
                                as="textarea"
                                rows={3}
                                value={twitterPost}
                                onChange={(e) => setTwitterPost(e.target.value)}
                                isValid={twitterPost.length <= twitterMaxAllowed}
                                isInvalid={twitterPost.length > twitterMaxAllowed}
                            />
                            <div className="d-flex flex-row w-100 justify-content-between align-items-center">
                                <span>{twitterPost.length}</span>
                                <a href={twitterLink} target="_blank" rel="noopener noreferrer" className="btn btn-default btn-pink">
                                    Share
                                </a>
                            </div>
                        </div>
                    </Tab>
                    <Tab eventKey="more" title="More">
                        <div className="d-flex flex-column w-100 justify-content-center align-items-center gap-3 pt-3">
                            <label className="font-default fs-14 default-text-color">Share via other platforms</label>
                            <a href={pinterestLink} target="_blank" rel="noopener noreferrer" className="btn btn-default btn-pink">
                                <FontAwesomeIcon icon={['fab', 'pinterest']} /> Pinterest
                            </a>
                            <a href={linkedinLink} target="_blank" rel="noopener noreferrer" className="btn btn-default btn-pink">
                                <FontAwesomeIcon icon={['fab', 'linkedin']} /> LinkedIn
                            </a>
                            <a href={emailLink} className="btn btn-default btn-pink">
                                <FontAwesomeIcon icon={['fas', 'envelope-open-text']} /> Email
                            </a>
                        </div>
                    </Tab>
                </Tabs>
                <div className="d-block">
                    <InputGroup>
                        <Form.Control value={itemLink} readOnly />
                        <Button variant="outline-primary" onClick={() => copyToClipboard(itemLink)}>Copy</Button>
                    </InputGroup>
                </div>
            </Modal.Body>
        </Modal>
    );
}
