import React from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { useAppearance } from '@/composables/useAppearance';

export default function DarkModeButton() {
    const { appearance, updateAppearance } = useAppearance();

    const toggleTheme = () => {
        updateAppearance(appearance === 'dark' ? 'light' : 'dark');
    };

    return (
        <button className="btn btn-default p-2 btn-icon" onClick={toggleTheme} type="button">
            <FontAwesomeIcon icon={['fas', appearance === 'dark' ? 'sun' : 'moon']} />
        </button>
    );
}
