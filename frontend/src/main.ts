import '@/assets/sass/app.scss';
import './bootstrap';
import 'bootstrap-vue-next/dist/bootstrap-vue-next.css';
import 'vue-final-modal/style.css';
import 'vue-multiselect/dist/vue-multiselect.min.css';
import 'vue3-toastify/dist/index.css';
import 'v-network-graph/lib/style.css';

import type { DefineComponent } from 'vue';
import { createApp } from 'vue';
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
import { createPersistedStatePlugin } from 'pinia-plugin-persistedstate-2';
import { vBToggle, vBTooltip } from 'bootstrap-vue-next';
import Vue3Toastify, { type ToastContainerOptions } from 'vue3-toastify';
import VueMultiselect from 'vue-multiselect';
import VNetworkGraph from 'v-network-graph';

import App from '@/App.vue';
import router from '@/router';
import i18n from './i18n';
import clickOutsideDirective from './Directives/ClickOutsideDirective';
import { initializeTheme } from './composables/useAppearance';
import { route } from './helpers/route';
import { useForm } from './helpers/useForm';

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

const app = createApp(App);
const vfm = createVfm();

app.use(pinia);
app.use(router);
app.use(Vue3Toastify, {
    autoClose: 3000,
} as ToastContainerOptions);
app.use(createBootstrap());
app.use(vfm);
app.use(i18n);
app.use(VNetworkGraph);

app.directive('click-outside', clickOutsideDirective);
app.directive('b-toggle', vBToggle);
app.directive('b-tooltip', vBTooltip);
app.component('vue-multiselect', VueMultiselect);
app.component('font-awesome-icon', FontAwesomeIcon);

app.config.globalProperties.$route_helper = route;
(window as any).route = route;
(window as any).useForm = useForm;

app.mount('#app');

initializeTheme();
