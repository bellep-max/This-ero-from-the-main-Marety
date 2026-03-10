import '@/assets/sass/app.scss';
import './bootstrap';
import 'bootstrap-vue-next/dist/bootstrap-vue-next.css';
import 'vue-final-modal/style.css';
import 'vue-multiselect/dist/vue-multiselect.min.css';
import 'vue3-toastify/dist/index.css';
import 'v-network-graph/lib/style.css';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';
import { createBootstrap } from 'bootstrap-vue-next';
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import {
    faFile,
    faArrowRightFromFile,
    faXmark,
    faClock,
    faUserMinus,
    faUserPlus,
    faArrowTurnUp,
    faShuffle,
    faBackwardStep,
    faForwardStep,
    faRepeat,
    faStar,
    faPause,
    faPlay,
    faVolumeXmark,
    faVolumeHigh,
    faVolumeLow,
    faAnglesDown,
    faAnglesUp,
    faAnglesRight,
    faAnglesLeft,
    faFileAudio,
    faFileCirclePlus,
    faFolderPlus,
    faRightLeft,
    faCubesStacked,
    faEllipsisVertical,
    faTrash,
    faArrowLeft,
    faArrowRight,
    faCrown,
    faRss,
    faQuoteLeft,
    faQuoteRight,
    faMagnifyingGlass,
    faMoon,
    faCircle,
    faCircleNodes,
    faCirclePlus,
    faBell,
    faGear,
    faMicrophone,
    faHashtag,
    faCalendarDays,
    faCodeBranch,
    faLanguage,
    faFlag,
    faGlobe,
    faLayerGroup,
    faFolderTree,
    faReply,
    faFaceSmile,
    faFloppyDisk,
    faPlus,
    faUser,
    faWallet,
    faUserPen,
    faLock,
    faEnvelopeOpenText,
    faList,
    faMusic,
    faRoute,
    faPodcast,
    faUsers,
    faLink,
    faPersonWalkingArrowRight,
    faShareNodes,
    faUpRightFromSquare,
    faPersonCirclePlus,
    faPersonCircleExclamation,
    faPersonCircleXmark,
    faCheck,
    faCalendarCheck,
    faPen,
    faHeadphones,
    faQuestion,
    faHeart,
    faHeartCircleCheck,
    faSun,
} from '@fortawesome/free-solid-svg-icons';
import {
    faPatreon,
    faTwitter,
    faFacebook,
    faXTwitter,
    faLinkedin,
    faPinterest,
} from '@fortawesome/free-brands-svg-icons';
import { createPinia } from 'pinia';
import { createVfm } from 'vue-final-modal';
import i18n from './i18n';
import clickOutsideDirective from './Directives/ClickOutsideDirective';
import Vue3Toastify, { type ToastContainerOptions } from 'vue3-toastify';
import { vBToggle, vBTooltip } from 'bootstrap-vue-next';
import { createPersistedStatePlugin } from 'pinia-plugin-persistedstate-2';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useAudioPlayerStore } from '@/stores/track.js';
import VueMultiselect from 'vue-multiselect';
import VNetworkGraph from 'v-network-graph';

library.add(
    faFile,
    faArrowRightFromFile,
    faXmark,
    faClock,
    faUserMinus,
    faUserPlus,
    faArrowTurnUp,
    faShuffle,
    faBackwardStep,
    faForwardStep,
    faRepeat,
    faStar,
    faPause,
    faPlay,
    faVolumeXmark,
    faVolumeHigh,
    faVolumeLow,
    faAnglesDown,
    faAnglesUp,
    faAnglesRight,
    faAnglesLeft,
    faFileAudio,
    faFileCirclePlus,
    faFolderPlus,
    faRightLeft,
    faCubesStacked,
    faEllipsisVertical,
    faTrash,
    faArrowLeft,
    faArrowRight,
    faCrown,
    faRss,
    faQuoteLeft,
    faQuoteRight,
    faMagnifyingGlass,
    faMoon,
    faCircle,
    faCircleNodes,
    faCirclePlus,
    faBell,
    faGear,
    faMicrophone,
    faHashtag,
    faCalendarDays,
    faCodeBranch,
    faLanguage,
    faFlag,
    faGlobe,
    faLayerGroup,
    faFolderTree,
    faReply,
    faFaceSmile,
    faFloppyDisk,
    faPlus,
    faUser,
    faWallet,
    faUserPen,
    faLock,
    faEnvelopeOpenText,
    faList,
    faMusic,
    faRoute,
    faPodcast,
    faUsers,
    faLink,
    faPersonWalkingArrowRight,
    faShareNodes,
    faUpRightFromSquare,
    faPersonCirclePlus,
    faPersonCircleExclamation,
    faPersonCircleXmark,
    faCheck,
    faCalendarCheck,
    faPen,
    faHeadphones,
    faQuestion,
    faHeart,
    faHeartCircleCheck,
    faSun,
    faPatreon,
    faTwitter,
    faFacebook,
    faXTwitter,
    faLinkedin,
    faPinterest,
);

const persistedStatePlugin = createPersistedStatePlugin({
    key: (prefix) => `erocast-${prefix}`,
    storage: localStorage,
});

const pinia = createPinia();
pinia.use(persistedStatePlugin);

// Set up page transition hooks
router.on('before', (event) => {
    const audioPlayer = useAudioPlayerStore();
    if (audioPlayer.audio && audioPlayer.isPlaying) {
        audioPlayer.currentTime = audioPlayer.audio.currentTime;
    }

    if (audioPlayer.isPlaying) {
        // Preserve the current playback state
        audioPlayer.savePlaybackState();
    }
});

router.on('after', (event) => {
    const audioPlayer = useAudioPlayerStore();
    if (audioPlayer.currentTrack && (!audioPlayer.audio || !audioPlayer.audio.src)) {
        audioPlayer.initializeAudio();
    }
});

router.on('finish', () => {
    const audioPlayer = useAudioPlayerStore();
    if (audioPlayer.currentTrack) {
        // Restore playback after page load
        audioPlayer.restorePlaybackState();
    }
});

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const requiredVersion = document.body.dataset.appVersion;
const storedVersion = localStorage.getItem('app-version');

if (storedVersion && storedVersion !== requiredVersion) {
    // Clear the data you need to refresh
    localStorage.removeItem('some-component-data');

    // You might also want to do a full-page refresh to ensure everything is reset
    window.location.reload();

    console.log('App version changed, local data cleared.');
}

// Update the stored version to the current version
if (requiredVersion != null) {
    localStorage.setItem('app-version', requiredVersion);
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: async (name) => {
        const page = await resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        );
        page.default.layout = page.default.layout || AppLayout;
        return page;
    },
    setup({ el, App, props, plugin }) {
        const serverVersion = props.initialPage.version;

        // Get the last stored version from local storage
        const storedVersion = localStorage.getItem('app_version');

        // Check if the versions are different
        if (serverVersion && storedVersion !== serverVersion) {
            // It's a new version, so clean local storage
            localStorage.clear();

            // Store the new version to prevent repeated cleaning
            localStorage.setItem('app_version', serverVersion);

            // You can add a console log for debugging purposes
            console.log(`New app version detected: ${serverVersion}. Local storage has been cleared.`);
        }

        const app = createApp({ render: () => h(App, props) });
        const vfm = createVfm();

        // Initialize Pinia first
        app.use(pinia);

        app.use(plugin)
            .use(Vue3Toastify, {
                autoClose: 3000,
                // ...existing options...
            } as ToastContainerOptions)
            .use(createBootstrap())
            .use(vfm)
            .use(ZiggyVue)
            .use(i18n)
            .use(VNetworkGraph)
            .directive('click-outside', clickOutsideDirective)
            .directive('b-toggle', vBToggle)
            .directive('b-tooltip', vBTooltip)
            .component('vue-multiselect', VueMultiselect)
            .component('font-awesome-icon', FontAwesomeIcon);

        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
