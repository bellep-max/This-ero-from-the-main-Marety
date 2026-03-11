import React, { useEffect, useState, lazy, Suspense } from 'react';
import { useLocation } from 'react-router-dom';
import { useAudioPlayerStore } from '@/stores/track';
import { useAuthStore } from '@/stores/auth';

const Header = lazy(() => import('@/Layouts/Components/Header'));
const Footer = lazy(() => import('@/Layouts/Components/Footer'));
const Player = lazy(() => import('@/Components/Player/Player'));
const AdultOnlyModal = lazy(() => import('@/Components/Modals/AdultOnlyModal'));
const AdventureBranchSelectModal = lazy(() => import('@/Components/Modals/AdventureBranchSelectModal'));

interface AppLayoutProps {
    children: React.ReactNode;
}

export default function AppLayout({ children }: AppLayoutProps) {
    const location = useLocation();
    const audioPlayer = useAudioPlayerStore();
    const isAdult = useAuthStore((s) => s.isAdult);
    const authLoaded = useAuthStore((s) => s.authLoaded);
    const [adultDismissed, setAdultDismissed] = useState(false);

    const showAdultModal = !adultDismissed && authLoaded && !isAdult;

    useEffect(() => {
        if (audioPlayer.currentTrack && !audioPlayer.audio) {
            audioPlayer.initializeAudio();
        }
    }, []);

    useEffect(() => {
        if (audioPlayer.isPlaying && !audioPlayer.audio) {
            audioPlayer.initializeAudio();
        }
    }, [location.pathname]);

    const setTrack = (track: any) => {
        audioPlayer.setTracks(track);
        audioPlayer.setShowAdventureModal(false);
    };

    return (
        <div className="d-flex flex-column min-vh-100">
            <Suspense fallback={null}>
                <Header />
            </Suspense>
            <div className="app-main flex-column">
                <div className="d-flex flex-column">
                    {children}
                </div>
            </div>
            <Suspense fallback={null}>
                <Player />
            </Suspense>
            <Suspense fallback={null}>
                <Footer />
            </Suspense>
            <Suspense fallback={null}>
                {showAdultModal && (
                    <AdultOnlyModal
                        show={showAdultModal}
                        onClose={() => setAdultDismissed(true)}
                        onAccepted={() => setAdultDismissed(true)}
                    />
                )}
                {audioPlayer.showAdventureModal && (
                    <AdventureBranchSelectModal
                        show={audioPlayer.showAdventureModal}
                        adventure={audioPlayer.currentTrack}
                        onClose={() => audioPlayer.setShowAdventureModal(false)}
                        onSelected={setTrack}
                    />
                )}
            </Suspense>
        </div>
    );
}
