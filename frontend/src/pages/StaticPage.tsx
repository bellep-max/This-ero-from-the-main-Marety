import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import apiClient from '@/api/client';

export default function StaticPage() {
  const { slug } = useParams<{ slug: string }>();
  const [content, setContent] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await apiClient.get(`/pages/${slug}`);
        const apiData = response.data;
        setContent(apiData.content ?? null);
      } catch (error) {
        console.error('Failed to load page data:', error);
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, [slug]);

  if (loading) {
    return (
      <div className="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
        <div className="spinner-border text-light" role="status">
          <span className="visually-hidden">Loading...</span>
        </div>
      </div>
    );
  }

  return (
    <div className="container">
      {content && <span dangerouslySetInnerHTML={{ __html: content.content }} />}
    </div>
  );
}
