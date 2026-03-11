import { slugify } from './FormService';

export const addTag = (tag: string, tags: any[], formTags: any[]) => {
    const newTag = {
        id: -Math.floor(Math.random() * 10000000),
        tag: slugify(tag),
    };

    tags.push(newTag);
    formTags.push(newTag);
};

export default { addTag };
