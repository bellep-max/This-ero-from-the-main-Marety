import React from 'react';

interface PersistentProps {
    children: React.ReactNode;
}

export default function Persistent({ children }: PersistentProps) {
    return <div className="persistent-component">{children}</div>;
}
