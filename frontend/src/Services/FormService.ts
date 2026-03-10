import { $t } from '@/i18n';
import ObjectTypes from '@/Enums/ObjectTypes.js';

export const removeEmptyObjectsKeys = (object: Record<string, any>): Record<string, any> => {
    if (!isObject(object)) return {};

    let newObject: Record<string, any> = {};

    Object.keys(object).forEach((key) => {
        const value = object[key];

        if (isObject(value)) {
            if (value instanceof File) {
                newObject[key] = value;
            } else {
                const cleanedNestedObject = removeEmptyObjectsKeys(value);

                if (Object.keys(cleanedNestedObject).length > 0) {
                    newObject[key] = cleanedNestedObject;
                }
            }
        } else if (Array.isArray(value)) {
            if (value.length > 0) {
                newObject[key] = value;
            }
        } else if (value !== null && value !== undefined && value !== '') {
            newObject[key] = value;
        }
    });

    return newObject;
};

export const slugify = (text: string): string => {
    return text
        .toString()
        .toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/--+/g, '-')
        .trim();
};

export const validateEmail = (email: string): boolean => {
    const regExp = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    return regExp.test(email);
};

export const setItemLink = (item: any): string => {
    const type = ObjectTypes.getObjectType(item.type);

    const typeRouteMap: Record<string, string> = {
        [ObjectTypes.Song]: `/track/${item.uuid}`,
        [ObjectTypes.User]: `/user/${item.username || item.uuid}`,
        [ObjectTypes.Playlist]: `/playlist/${item.uuid}`,
        [ObjectTypes.Podcast]: `/podcast/${item.uuid}`,
        [ObjectTypes.PodcastEpisode]: `/podcast/${item.podcast_uuid}/episode/${item.uuid}`,
        [ObjectTypes.Adventure]: `/adventures/${item.uuid}`,
    };

    return typeRouteMap[type] || `/track/${item.uuid}`;
};

export const setItemText = (item: any): string => {
    const type = ObjectTypes.getObjectType(item.type);

    if (type === ObjectTypes.Song) {
        return ($t as any)('modals.share.texts.song', {
            name: item.title,
            author: item.user.name,
            link: setItemLink(item),
        });
    } else if (type === ObjectTypes.User) {
        return ($t as any)('modals.share.texts.user', { link: setItemLink(item) });
    } else if (type === ObjectTypes.Playlist) {
        return ($t as any)('modals.share.texts.playlist', {
            name: item.title,
            author: item.user.name,
            link: setItemLink(item),
        });
    } else if (type === ObjectTypes.Podcast) {
        return ($t as any)('modals.share.texts.podcast', {
            name: item.title,
            author: item.user.name,
            link: setItemLink(item),
        });
    } else if (type === ObjectTypes.PodcastEpisode) {
        return ($t as any)('modals.share.texts.episode', {
            name: item.title,
            author: item.user.name,
            link: setItemLink(item),
        });
    } else if (type === ObjectTypes.Adventure) {
        return ($t as any)('modals.share.texts.adventure', {
            name: item.title,
            author: item.user.name,
            link: setItemLink(item),
        });
    }

    return '';
};

const isObject = (item: any): boolean => {
    return typeof item === 'object' && item !== null && !Array.isArray(item);
};

export default {
    removeEmptyObjectsKeys,
    slugify,
    validateEmail,
    setItemLink,
    setItemText,
};
