import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

export default function SearchComponent() {
    const [query, setQuery] = useState('');
    const [show, setShow] = useState(false);
    const navigate = useNavigate();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (query.trim()) {
            navigate(`/search?q=${encodeURIComponent(query.trim())}`);
            setShow(false);
            setQuery('');
        }
    };

    return (
        <div className="d-flex align-items-center">
            {show ? (
                <form onSubmit={handleSubmit} className="d-flex gap-2">
                    <input
                        type="text"
                        className="form-control form-control-sm"
                        placeholder="Search..."
                        value={query}
                        onChange={(e) => setQuery(e.target.value)}
                        autoFocus
                    />
                    <button type="button" className="btn btn-sm btn-default" onClick={() => setShow(false)}>
                        <FontAwesomeIcon icon={['fas', 'xmark']} />
                    </button>
                </form>
            ) : (
                <button className="btn btn-default p-2 btn-icon" onClick={() => setShow(true)} type="button">
                    <FontAwesomeIcon icon={['fas', 'magnifying-glass']} />
                </button>
            )}
        </div>
    );
}
