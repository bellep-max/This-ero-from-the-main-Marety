import React, { useState, useMemo } from 'react';
import { $t } from '@/i18n';
import { useAuthStore } from '@/stores/auth';
import TextareaEmojiPicker from '@/Components/FormItems/TextareaEmojiPicker';
import Comment from '@/Components/Comment';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import apiClient from '@/api/client';
import route from '@/helpers/route';

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

interface CommentsSectionProps {
    comments?: CommentData[];
    type: string;
    uuid: string;
    onCommented?: () => void;
}

export default function CommentsSection({ comments = [], type, uuid, onCommented }: CommentsSectionProps) {
    const isLogged = useAuthStore((s) => s.isLogged);

    const [content, setContent] = useState('');
    const [parentId, setParentId] = useState<number | null>(null);
    const [responseComment, setResponseComment] = useState<any>({});
    const [submitting, setSubmitting] = useState(false);

    const hasComments = comments.length > 0;
    const emptyInput = content.length === 0;

    const setReply = (comment: CommentData) => {
        setParentId(comment.id || null);
        setResponseComment(comment);
    };

    const resetReply = () => {
        setParentId(null);
        setResponseComment({});
    };

    const checkIsReplied = (comment: CommentData) => {
        return !!parentId && responseComment === comment;
    };

    const submit = async () => {
        setSubmitting(true);
        try {
            await apiClient.post(route('comments.store'), {
                parent_id: parentId,
                content,
                commentable_type: type,
                uuid,
            });
            onCommented?.();
            setContent('');
            setParentId(null);
        } catch (error) {
            console.error('Failed to submit comment:', error);
        } finally {
            setSubmitting(false);
        }
    };

    if (!isLogged) {
        return (
            <a href="javascript:void(0)" className="w-100 text-center color-light font-default fs-5">
                {$t('misc.login_to_comment')}
            </a>
        );
    }

    return (
        <div className="col d-flex flex-column align-items-start gap-4">
            <div className="d-flex flex-row align-items-center gap-4 w-100">
                <TextareaEmojiPicker
                    value={content}
                    response={responseComment}
                    onInput={setContent}
                    onChange={setContent}
                    onCancel={resetReply}
                />
                <DefaultButton classList="btn-rounded btn-pink" disabled={emptyInput || submitting} onClick={submit}>
                    <svg xmlns="http://www.w3.org/2000/svg" width="39" height="40" viewBox="13 21 38 22">
                        <path d="M48.3105 17.915C48.1514 17.6903 47.9317 17.5154 47.6771 17.4108C47.4224 17.3061 47.1432 17.276 46.8721 17.3239L14.3178 23.1488L13.614 25.7752L24.4582 33.888L29.0373 47.9616L31.6643 48.6655L48.3838 19.4686C48.52 19.2293 48.5855 18.9562 48.5725 18.6812C48.5595 18.4061 48.4687 18.1404 48.3105 17.915ZM30.7136 45.7259L26.7776 33.6288L40.1067 25.1034L38.8751 23.1778L25.4418 31.7696L16.5008 25.0805L45.5078 19.89L30.7136 45.7259Z" />
                    </svg>
                </DefaultButton>
            </div>
            {hasComments ? (
                comments.map((comment: CommentData) => (
                    <React.Fragment key={comment.id || comment.content}>
                        <Comment comment={comment} replied={checkIsReplied(comment)} onReply={setReply} onCancel={resetReply} />
                        {comment.replies?.map((reply: CommentData) => (
                            <Comment key={reply.id || reply.content} comment={reply} replied={checkIsReplied(reply)} onReply={setReply} onCancel={resetReply} />
                        ))}
                    </React.Fragment>
                ))
            ) : (
                <span className="w-100 text-center color-light font-default fs-5">
                    {$t('misc.no_comments')}
                </span>
            )}
        </div>
    );
}
