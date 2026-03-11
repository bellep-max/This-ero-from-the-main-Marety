import React, { useState, useMemo } from 'react';
import { $t } from '@/i18n';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import Icon from '@/Components/Icons/Icon';

interface BlogPageFiltersProps {
    categories?: { id: number; name: string }[];
    archives?: { created_at: string; count: number; date: string }[];
    tags?: string[];
    filters: {
        categories?: number[];
        dates?: string[];
        tags?: string[];
    };
    onUpdated?: (filters: any) => void;
}

export default function BlogPageFilters({ categories = [], archives = [], tags = [], filters: initialFilters, onUpdated }: BlogPageFiltersProps) {
    const [showFilters, setShowFilters] = useState(false);
    const [selectedCategories, setSelectedCategories] = useState<number[]>(initialFilters.categories ?? []);
    const [selectedDates, setSelectedDates] = useState<string[]>(initialFilters.dates ?? []);
    const [selectedTags, setSelectedTags] = useState<string[]>(initialFilters.tags ?? []);
    const [tagSearch, setTagSearch] = useState('');

    const hasFilters = selectedCategories.length > 0 || selectedDates.length > 0 || selectedTags.length > 0;
    const numberFilters = selectedCategories.length + selectedDates.length + selectedTags.length;

    const handleCategoryChange = (id: number, checked: boolean) => {
        const updated = checked ? [...selectedCategories, id] : selectedCategories.filter((c) => c !== id);
        setSelectedCategories(updated);
        onUpdated?.({ categories: updated, dates: selectedDates, tags: selectedTags });
    };

    const handleDateChange = (date: string, checked: boolean) => {
        const updated = checked ? [...selectedDates, date] : selectedDates.filter((d) => d !== date);
        setSelectedDates(updated);
        onUpdated?.({ categories: selectedCategories, dates: updated, tags: selectedTags });
    };

    const handleTagChange = (tag: string, checked: boolean) => {
        const updated = checked ? [...selectedTags, tag] : selectedTags.filter((t) => t !== tag);
        setSelectedTags(updated);
        onUpdated?.({ categories: selectedCategories, dates: selectedDates, tags: updated });
    };

    const resetFilters = () => {
        setSelectedCategories([]);
        setSelectedDates([]);
        setSelectedTags([]);
        onUpdated?.({ categories: [], dates: [], tags: [] });
    };

    return (
        <>
            <DefaultButton classList="btn-outline d-xl-none" onClick={() => setShowFilters(!showFilters)}>
                {$t('buttons.filters')}
            </DefaultButton>
            <div className={`d-flex flex-column w-100 bg-default rounded-5 px-3 py-4 gap-3 ${showFilters ? '' : 'd-none d-xl-flex'}`}>
                <div className="fs-4 font-default">{$t('buttons.filters')}</div>
                <div className="accordion">
                    <div className="accordion-item border-0">
                        <h2 className="accordion-header">
                            <button className="accordion-button gap-2" type="button" data-bs-toggle="collapse" data-bs-target="#filterCategories">
                                <Icon icon={['fas', 'layer-group']} classList="color-pink" />
                                <span className="font-default">{$t('pages.blog.filters.categories')}</span>
                            </button>
                        </h2>
                        <div id="filterCategories" className="accordion-collapse collapse show">
                            <div className="accordion-body">
                                {categories.map((cat) => (
                                    <div key={cat.id} className="form-check">
                                        <input
                                            className="form-check-input"
                                            type="checkbox"
                                            id={`cat-${cat.id}`}
                                            checked={selectedCategories.includes(cat.id)}
                                            onChange={(e) => handleCategoryChange(cat.id, e.target.checked)}
                                        />
                                        <label className="form-check-label" htmlFor={`cat-${cat.id}`}>{cat.name}</label>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                    <div className="accordion-item border-0">
                        <h2 className="accordion-header">
                            <button className="accordion-button gap-2" type="button" data-bs-toggle="collapse" data-bs-target="#filterArchives">
                                <Icon icon={['fas', 'folder-tree']} classList="color-pink" />
                                <span className="font-default">{$t('pages.blog.filters.archives')}</span>
                            </button>
                        </h2>
                        <div id="filterArchives" className="accordion-collapse collapse show">
                            <div className="accordion-body">
                                {archives.map((archive) => (
                                    <div key={archive.created_at} className="form-check">
                                        <input
                                            className="form-check-input"
                                            type="checkbox"
                                            id={`archive-${archive.created_at}`}
                                            checked={selectedDates.includes(archive.created_at)}
                                            onChange={(e) => handleDateChange(archive.created_at, e.target.checked)}
                                        />
                                        <label className="form-check-label" htmlFor={`archive-${archive.created_at}`}>
                                            ({archive.count}) {archive.date}
                                        </label>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                    <div className="accordion-item border-0">
                        <h2 className="accordion-header">
                            <button className="accordion-button gap-2" type="button" data-bs-toggle="collapse" data-bs-target="#filterTags">
                                <Icon icon={['fas', 'hashtag']} classList="color-pink" />
                                <span className="font-default">{$t('pages.discover.filters.tags.name')}</span>
                            </button>
                        </h2>
                        <div id="filterTags" className="accordion-collapse collapse show">
                            <div className="accordion-body d-flex flex-column gap-2">
                                <input
                                    type="search"
                                    className="form-control"
                                    placeholder={$t('pages.discover.filters.tags.placeholder')}
                                    value={tagSearch}
                                    onChange={(e) => setTagSearch(e.target.value)}
                                />
                                {tags.map((tag) => (
                                    <div key={tag} className="form-check">
                                        <input
                                            className="form-check-input"
                                            type="checkbox"
                                            id={`tag-${tag}`}
                                            checked={selectedTags.includes(tag)}
                                            onChange={(e) => handleTagChange(tag, e.target.checked)}
                                        />
                                        <label className="form-check-label" htmlFor={`tag-${tag}`}>{tag}</label>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
                {hasFilters && (
                    <div className="d-flex flex-column justify-content-between align-items-center gap-3">
                        <div className="store-filter-mobile-count">
                            <span className="total-filter">{numberFilters}</span> {$t('misc.filters_selected')}
                        </div>
                        <div className="d-flex flex-row gap-3 justify-content-center align-items-center">
                            <DefaultButton classList="btn-outline" onClick={resetFilters}>
                                {$t('buttons.cancel')}
                            </DefaultButton>
                        </div>
                    </div>
                )}
            </div>
        </>
    );
}
