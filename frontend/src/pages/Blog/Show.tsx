import React, { useState, useEffect, useMemo } from 'react';
import { useParams, Link } from 'react-router-dom';
import apiClient from '@/api/client';
import { $t } from '@/i18n';
import CommentsSection from '@/Components/Sections/CommentsSection';
import route from '@/helpers/route';

export default function BlogShowPage() {
  const { slug } = useParams<{ slug: string }>();
  const [post, setPost] = useState<any>(null);
  const [comments, setComments] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await apiClient.get(`/blog/${slug}`);
        const apiData = response.data;
        setPost(apiData.post ?? null);
        setComments(apiData.comments ?? null);
      } catch (error) {
        console.error('Failed to load page data:', error);
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, [slug]);

  const updateComments = async () => {
    try {
      const response = await apiClient.get(`/blog/${slug}`);
      const apiData = response.data;
      if (apiData.comments) setComments(apiData.comments);
    } catch (error) {
      console.error('Failed to refresh comments:', error);
    }
  };

  const content = useMemo(
    () => (post?.full_content ? post.full_content : post?.short_content),
    [post]
  );

  if (loading) {
    return (
      <div className="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
        <div className="spinner-border text-light" role="status">
          <span className="visually-hidden">Loading...</span>
        </div>
      </div>
    );
  }

  if (!post) return null;

  return (
    <div className="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
      <div className="container d-flex flex-column gap-4">
        <div className="row">
          <div className="col text-start">
            <div className="d-block">
              <div className="block-title color-light text-truncate">
                {post.title}
              </div>
              <a href={route('posts.index')} className="block-description color-light">
                {$t('misc.back')}
              </a>
            </div>
          </div>
        </div>
        <div className="row">
          <div className="col">
            <div className="d-flex flex-column justify-content-center align-items-center gap-4 bg-default rounded-5 p-5 h-100">
              {post.artwork && (
                <img className="img-fluid" alt={post.title} src={post.artwork} />
              )}
              {content && (
                <div className="fs-5" dangerouslySetInnerHTML={{ __html: content }} />
              )}
            </div>
          </div>
        </div>
        {post.allow_comments ? (
          <div className="row">
            <CommentsSection
              comments={comments}
              type={post.type}
              uuid={post.uuid}
              onCommented={updateComments}
            />
          </div>
        ) : (
          <div className="row text-center color-light font-default fs-5">
            <span>{$t('misc.comments_disabled')}</span>
          </div>
        )}
      </div>
    </div>
  );
}
