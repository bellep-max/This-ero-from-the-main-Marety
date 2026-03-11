import '@/assets/sass/app.scss';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'react-toastify/dist/ReactToastify.css';
import 'rc-slider/assets/index.css';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

import React from 'react';
import { createRoot } from 'react-dom/client';
import { library } from '@fortawesome/fontawesome-svg-core';
import {
    faFile, faArrowRightFromFile, faXmark, faClock, faUserMinus, faUserPlus,
    faArrowTurnUp, faShuffle, faBackwardStep, faForwardStep, faRepeat, faStar,
    faPause, faPlay, faVolumeXmark, faVolumeHigh, faVolumeLow, faAnglesDown,
    faAnglesUp, faAnglesRight, faAnglesLeft, faFileAudio, faFileCirclePlus,
    faFolderPlus, faRightLeft, faCubesStacked, faEllipsisVertical, faTrash,
    faArrowLeft, faArrowRight, faCrown, faRss, faQuoteLeft, faQuoteRight,
    faMagnifyingGlass, faMoon, faCircle, faCircleNodes, faCirclePlus, faBell,
    faGear, faMicrophone, faHashtag, faCalendarDays, faCodeBranch, faLanguage,
    faFlag, faGlobe, faLayerGroup, faFolderTree, faReply, faFaceSmile,
    faFloppyDisk, faPlus, faUser, faWallet, faUserPen, faLock,
    faEnvelopeOpenText, faList, faMusic, faRoute, faPodcast, faUsers, faLink,
    faPersonWalkingArrowRight, faShareNodes, faUpRightFromSquare,
    faPersonCirclePlus, faPersonCircleExclamation, faPersonCircleXmark,
    faCheck, faCalendarCheck, faPen, faHeadphones, faQuestion, faHeart,
    faHeartCircleCheck, faSun, faChevronDown, faChevronUp, faChevronLeft, faChevronRight,
} from '@fortawesome/free-solid-svg-icons';
import {
    faPatreon, faTwitter, faFacebook, faXTwitter, faLinkedin, faPinterest,
} from '@fortawesome/free-brands-svg-icons';

import App from '@/App';
import '@/i18n';
import { initializeTheme } from '@/composables/useAppearance';

library.add(
    faFile, faArrowRightFromFile, faXmark, faClock, faUserMinus, faUserPlus,
    faArrowTurnUp, faShuffle, faBackwardStep, faForwardStep, faRepeat, faStar,
    faPause, faPlay, faVolumeXmark, faVolumeHigh, faVolumeLow, faAnglesDown,
    faAnglesUp, faAnglesRight, faAnglesLeft, faFileAudio, faFileCirclePlus,
    faFolderPlus, faRightLeft, faCubesStacked, faEllipsisVertical, faTrash,
    faArrowLeft, faArrowRight, faCrown, faRss, faQuoteLeft, faQuoteRight,
    faMagnifyingGlass, faMoon, faCircle, faCircleNodes, faCirclePlus, faBell,
    faGear, faMicrophone, faHashtag, faCalendarDays, faCodeBranch, faLanguage,
    faFlag, faGlobe, faLayerGroup, faFolderTree, faReply, faFaceSmile,
    faFloppyDisk, faPlus, faUser, faWallet, faUserPen, faLock,
    faEnvelopeOpenText, faList, faMusic, faRoute, faPodcast, faUsers, faLink,
    faPersonWalkingArrowRight, faShareNodes, faUpRightFromSquare,
    faPersonCirclePlus, faPersonCircleExclamation, faPersonCircleXmark,
    faCheck, faCalendarCheck, faPen, faHeadphones, faQuestion, faHeart,
    faHeartCircleCheck, faSun, faChevronDown, faChevronUp, faChevronLeft, faChevronRight,
    faPatreon, faTwitter, faFacebook, faXTwitter, faLinkedin, faPinterest,
);

initializeTheme();

const container = document.getElementById('app');
if (container) {
    const root = createRoot(container);
    root.render(
        <React.StrictMode>
            <App />
        </React.StrictMode>
    );
}
