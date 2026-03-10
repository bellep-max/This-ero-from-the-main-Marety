import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export const isLogged = computed(() => usePage().props.auth.user !== null);

export const isOwner = (uuid) => usePage().props.auth.user.uuid === uuid;

export default { isLogged, isOwner };
