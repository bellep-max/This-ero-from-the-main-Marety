import { $t } from '@/i18n.js';
import ObjectTypes from '@/Enums/ObjectTypes.js';

export const removeEmptyObjectsKeys = (object) => {
    if (!isObject(object)) return;

    let newObject = {};

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

export const slugify = (text) => {
    return text
        .toString()
        .toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/--+/g, '-')
        .trim();
};

export const validateEmail = (email) => {
    const regExp = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    return regExp.test(email);
};

export const setItemLink = (item) => {
    const type = ObjectTypes.getObjectType(item.type);

    return route(`${type}s.show`, item.uuid);
};

export const setItemText = (item) => {
    const type = ObjectTypes.getObjectType(item.type);

    if (type === ObjectTypes.Song) {
        return $t('modals.share.texts.song', {
            name: item.title,
            author: item.user.name,
            link: setItemLink(item),
        });
    } else if (type === ObjectTypes.User) {
        return $t('modals.share.texts.user', { link: setItemLink(item) });
    } else if (type === ObjectTypes.Playlist) {
        return $t('modals.share.texts.playlist', {
            name: item.title,
            author: item.user.name,
            link: setItemLink(item),
        });
    } else if (type === ObjectTypes.Podcast) {
        return $t('modals.share.texts.podcast', {
            name: item.title,
            author: item.user.name,
            link: setItemLink(item),
        });
    } else if (type === ObjectTypes.PodcastEpisode) {
        return $t('modals.share.texts.episode', {
            name: item.title,
            author: item.user.name,
            link: setItemLink(item),
        });
    } else if (type === ObjectTypes.Adventure) {
        return $t('modals.share.texts.adventure', {
            name: item.title,
            author: item.user.name,
            link: setItemLink(item),
        });
    }
};

const isObject = (item) => {
    return typeof item === 'object' && item !== null && !Array.isArray(item);
};

export default {
    removeEmptyObjectsKeys,
    slugify,
    validateEmail,
    setItemLink,
    setItemText,
};
