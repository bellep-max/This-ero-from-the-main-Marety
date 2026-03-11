import i18next from 'i18next';
import { initReactI18next } from 'react-i18next';
import en from '@/assets/lang/en.json';

i18next.use(initReactI18next).init({
    resources: {
        en: { translation: en },
    },
    lng: 'en',
    fallbackLng: 'en',
    interpolation: {
        escapeValue: false,
    },
});

export function $t(key: string, values?: any): string {
    return i18next.t(key, values) as string;
}

export function $te(key: string): boolean {
    return i18next.exists(key);
}

export function $td(text: string, defaultValue: string | null = null, values: any = null): string {
    if (i18next.exists(text)) {
        return i18next.t(text, values) as string;
    }
    return defaultValue ?? text;
}

export default i18next;
