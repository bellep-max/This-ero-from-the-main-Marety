import React, { useState, useEffect, useRef } from 'react';
import Icon from '@/Components/Icons/Icon';

interface TextareaEmojiPickerProps {
    value?: string;
    response?: { content?: string; [key: string]: any };
    onInput?: (value: string) => void;
    onCancel?: () => void;
    onChange?: (value: string) => void;
}

export default function TextareaEmojiPicker({ value = '', response = {}, onInput, onCancel, onChange }: TextareaEmojiPickerProps) {
    const [showPicker, setShowPicker] = useState(false);
    const textareaRef = useRef<HTMLTextAreaElement>(null);

    const hasResponse = Object.keys(response).length > 0;

    const handleChange = (e: React.ChangeEvent<HTMLTextAreaElement>) => {
        const val = e.target.value;
        onChange?.(val);
        onInput?.(val);
    };

    const togglePicker = () => {
        setShowPicker(!showPicker);
    };

    return (
        <div className="d-flex flex-column flex-grow-1">
            {hasResponse && (
                <div className="z-0 rounded-top-4 bg-secondary-subtle w-100 p-2 font-merge d-flex flex-row flex-grow-0 justify-content-between text-start">
                    <span>{response.content}</span>
                    <Icon icon={['fas', 'xmark']} onClick={() => onCancel?.()} />
                </div>
            )}
            <div className="textarea-emoji-picker w-100 z-1" style={{ position: 'relative', margin: '0 auto' }}>
                <Icon
                    icon={['fas', 'face-smile']}
                    classList="color-pink emoji-trigger"
                    onClick={togglePicker}
                    style={{ position: 'absolute', top: '0.5rem', right: '0.5rem', cursor: 'pointer', height: '20px', zIndex: 2 }}
                />
                <textarea
                    rows={3}
                    ref={textareaRef}
                    className="form-control"
                    style={{ resize: 'none' }}
                    value={value}
                    onChange={handleChange}
                />
            </div>
        </div>
    );
}
