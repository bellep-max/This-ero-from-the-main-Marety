import { useAuthStore } from '@/stores/auth';

export const isLogged = (): boolean => {
    return useAuthStore.getState().user !== null;
};

export const isOwner = (uuid: string): boolean => {
    return useAuthStore.getState().user?.uuid === uuid;
};

export default { isLogged, isOwner };
