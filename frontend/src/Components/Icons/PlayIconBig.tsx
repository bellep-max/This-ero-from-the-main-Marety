import React from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

export default function PlayIconBig() {
    return (
        <div className="play-icon-big d-flex justify-content-center align-items-center">
            <FontAwesomeIcon icon={['fas', 'play']} size="2x" />
        </div>
    );
}
