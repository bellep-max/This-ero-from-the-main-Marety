import { createI18n } from 'vue-i18n';

import en from '@/assets/lang/en.json';

let messages = {
    en: {
        ...en,
    },
};

const i18n = createI18n({
    legacy: false,
    locale: 'en',
    fallbackLocale: 'en',
    messages: messages,
});

function $td(text, defaultValue = null, values = null) {
    if (i18n.global.te(text) || i18n.global.te(text, i18n.global.fallbackLocale.value)) {
        return i18n.global.t(text, values);
    }

    if (defaultValue === null) {
        return defaultValue;
    }

    return defaultValue ?? text;
}

function $t(key, values = null) {
    return i18n.global.t(...arguments);
}

function $te(key, values = null) {
    return i18n.global.te(...arguments);
}

export default i18n;

export { $td, $t, $te };
