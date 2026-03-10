import { computed } from 'vue';
import { useAuthStore } from '@/stores/auth';

export const isLogged = computed(() => {
    const authStore = useAuthStore();
    return authStore.user !== null;
});

export const isOwner = (uuid: string): boolean => {
    const authStore = useAuthStore();
    return authStore.user?.uuid === uuid;
};

export default { isLogged, isOwner };
