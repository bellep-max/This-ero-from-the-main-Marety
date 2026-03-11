import React from 'react';
import Icon from '@/Components/Icons/Icon';
import DefaultButton from '@/Components/Buttons/DefaultButton';

interface CommentData {
    id?: number;
    content: string;
    user: string;
    artwork: string;
    created_at: string;
    is_reply?: boolean;
    replies?: CommentData[];
    [key: string]: any;
}

interface CommentProps {
    comment: CommentData;
    replied?: boolean;
    onReply?: (comment: CommentData) => void;
    onCancel?: () => void;
}

export default function Comment({ comment, replied = false, onReply, onCancel }: CommentProps) {
    return (
        <div className="d-flex flex-row gap-4 justify-content-start align-items-center w-100">
            {comment.is_reply && <Icon icon={['fas', 'arrow-turn-up']} classList="color-light" />}
            <div className="bg-default rounded-4 p-3 d-flex flex-row gap-4 flex-grow-1">
                <div className="d-flex flex-row gap-3">
                    <img className="comment-img" src={comment.artwork} alt={comment.user} />
                    <div className="d-flex flex-column text-start">
                        <span className="d-inline-block font-default fw-bolder">{comment.user}</span>
                        <span className="d-inline-block font-merge color-grey">{comment.created_at}</span>
                    </div>
                </div>
                <div className="d-block fs-5">{comment.content}</div>
            </div>
            {!comment.is_reply && (
                <div>
                    {replied ? (
                        <DefaultButton classList="btn-outline btn-rounded" onClick={() => onCancel?.()}>
                            <Icon icon={['fas', 'xmark']} />
                        </DefaultButton>
                    ) : (
                        <DefaultButton classList="btn-outline btn-rnd" onClick={() => onReply?.(comment)}>
                            <Icon icon={['fas', 'reply']} />
                        </DefaultButton>
                    )}
                </div>
            )}
        </div>
    );
}
