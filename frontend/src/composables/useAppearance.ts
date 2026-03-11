import { useState, useEffect } from 'react';

type Appearance = 'light' | 'dark' | 'system';

export function updateTheme(value: Appearance) {
    if (typeof window === 'undefined') return;
    if (value === 'system') {
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        document.documentElement.classList.toggle('dark', systemTheme === 'dark');
    } else {
        document.documentElement.classList.toggle('dark', value === 'dark');
    }
}

const setCookie = (name: string, value: string, days = 365) => {
    if (typeof document === 'undefined') return;
    const maxAge = days * 24 * 60 * 60;
    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const getStoredAppearance = (): Appearance | null => {
    if (typeof window === 'undefined') return null;
    return localStorage.getItem('appearance') as Appearance | null;
};

const handleSystemThemeChange = () => {
    updateTheme(getStoredAppearance() || 'system');
};

export function initializeTheme() {
    if (typeof window === 'undefined') return;
    updateTheme(getStoredAppearance() || 'system');
    window.matchMedia('(prefers-color-scheme: dark)')?.addEventListener('change', handleSystemThemeChange);
}

export function useAppearance() {
    const [appearance, setAppearance] = useState<Appearance>('system');

    useEffect(() => {
        initializeTheme();
        const saved = localStorage.getItem('appearance') as Appearance | null;
        if (saved) setAppearance(saved);
    }, []);

    function updateAppearance(value: Appearance) {
        setAppearance(value);
        localStorage.setItem('appearance', value);
        setCookie('appearance', value);
        updateTheme(value);
    }

    return { appearance, updateAppearance };
}
