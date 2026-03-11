import React, { useState, useEffect } from 'react';
import apiClient from '@/api/client';
import { $t } from '@/i18n';
import BlogPageFilters from '@/Components/Sections/BlogPageFilters';
import PostCard from '@/Components/Cards/PostCard';
import route from '@/helpers/route';

export default function BlogIndexPage() {
  const [posts, setPosts] = useState<any[]>([]);
  const [categories, setCategories] = useState<any[]>([]);
  const [archives, setArchives] = useState<any[]>([]);
  const [tags, setTags] = useState<any[]>([]);
  const [filters, setFilters] = useState<any>({});
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await apiClient.get('/blog');
        const apiData = response.data;
        setPosts(apiData.posts ?? []);
        setCategories(apiData.categories ?? []);
        setArchives(apiData.archives ?? []);
        setTags(apiData.tags ?? []);
        setFilters(apiData.filters ?? {});
      } catch (error) {
        console.error('Failed to load page data:', error);
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, []);

  const applyFilters = async (formData: any) => {
    try {
      const response = await apiClient.get('/blog', { params: formData });
      const apiData = response.data;
      setPosts(apiData.posts ?? []);
    } catch (error) {
      console.error('Failed to apply filters:', error);
    }
  };

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
    <div className="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
      <div className="container">
        <div className="row">
          <div className="col text-start">
            <div className="d-block">
              <div className="block-title color-light text-truncate">
                {$t('pages.blog.title')}
              </div>
              <div className="block-description color-light">
                {$t('pages.blog.description')}
              </div>
            </div>
          </div>
        </div>
        <div className="row mt-3">
          <div className="col-12 col-xl-3 pb-3 pb-xl-0">
            <BlogPageFilters
              categories={categories}
              archives={archives}
              tags={tags}
              filters={filters}
              onUpdated={applyFilters}
            />
          </div>
          <div className="col col-xl-9 d-flex flex-column gap-4">
            {posts.map((post: any) => (
              <PostCard key={post.uuid || post.id} post={post} />
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}
