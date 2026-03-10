import { slugify } from './FormService';
import { ref, type Ref } from 'vue';

export const addTag = (tag: string, tags: Ref<any[]>, formTags: any[]) => {
    const newTag = {
        id: -Math.floor(Math.random() * 10000000),
        tag: slugify(tag),
    };

    tags.value.push(newTag);
    formTags.push(newTag);
};

export default { addTag };
