import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import Activities from '@/Enums/Activities.js';
import ObjectTypes from '@/Enums/ObjectTypes.js';
import { isNotEmpty } from '@/Services/MiscService';
import apiClient from '@/api/client';
import { ACTIONS } from '@/api/endpoints';

interface Track {
    uuid: string;
    path: string;
    title?: string;
    artwork?: string;
    type?: string;
    children?: any[];
    [key: string]: any;
}

interface AudioPlayerState {
    isPlaying: boolean;
    isRepeat: boolean;
    isShuffle: boolean;
    audio: HTMLAudioElement | null;
    currentTrack: Track | null;
    currentTrackIndex: number;
    currentTime: number;
    duration: number;
    volume: number;
    queue: Track[];
    shuffledQueue: Track[];
    error: string | null;
    showAdventureModal: boolean;
    savedPlaybackState: { time: number; wasPlaying: boolean };

    currentProgress: () => number;
    isFirstTrack: () => boolean;
    isLastTrack: () => boolean;
    hasQueue: () => boolean;
    currentQueue: () => Track[];

    savePlaybackState: () => void;
    restorePlaybackState: () => void;
    initializeAudio: () => void;
    loadTrack: (index: number) => void;
    setTracks: (newTracks: Track | Track[]) => void;
    togglePlayPause: () => void;
    play: () => void;
    pause: () => void;
    setVolume: (level: number) => void;
    toggleShuffle: () => void;
    shuffleQueue: () => void;
    playNext: () => void;
    playPrevious: () => void;
    seek: (value: number | string) => void;
    addToQueueNext: (track: Track) => void;
    removeFromQueue: (index: number) => void;
    deleteFromQueue: (uuid: string) => void;
    logPlayActivity: (trackUuid: string, type: string) => void;
    setShowAdventureModal: (show: boolean) => void;
}

export const useAudioPlayerStore = create<AudioPlayerState>()(
    persist(
        (set, get) => ({
            isPlaying: false,
            isRepeat: false,
            isShuffle: false,
            audio: null,
            currentTrack: null,
            currentTrackIndex: 0,
            currentTime: 0,
            duration: 0,
            volume: 0.8,
            queue: [],
            shuffledQueue: [],
            error: null,
            showAdventureModal: false,
            savedPlaybackState: { time: 0, wasPlaying: false },

            currentProgress: () => {
                const { currentTime, duration } = get();
                return duration ? (currentTime / duration) * 100 : 0;
            },
            isFirstTrack: () => get().currentTrackIndex === 0,
            isLastTrack: () => get().currentTrackIndex === get().queue.length - 1,
            hasQueue: () => get().queue.length > 0,
            currentQueue: () => {
                const { isShuffle, shuffledQueue, queue } = get();
                return isShuffle ? shuffledQueue : queue;
            },

            setShowAdventureModal: (show) => set({ showAdventureModal: show }),

            savePlaybackState: () => {
                const { audio, isPlaying } = get();
                if (audio) {
                    set({ savedPlaybackState: { time: audio.currentTime, wasPlaying: isPlaying } });
                }
            },

            restorePlaybackState: () => {
                const state = get();
                if (!state.audio) state.initializeAudio();
                const { audio, currentTrack, savedPlaybackState } = get();
                if (audio && currentTrack) {
                    audio.currentTime = savedPlaybackState.time;
                    if (savedPlaybackState.wasPlaying) get().play();
                }
            },

            initializeAudio: () => {
                const state = get();
                try {
                    if (state.audio?.src) return;
                    const newAudio = new Audio();
                    newAudio.volume = state.volume;

                    newAudio.addEventListener('timeupdate', () => set({ currentTime: newAudio.currentTime }));
                    newAudio.addEventListener('loadedmetadata', () => set({ duration: newAudio.duration }));
                    newAudio.addEventListener('play', () => set({ isPlaying: true }));
                    newAudio.addEventListener('pause', () => set({ isPlaying: false }));
                    newAudio.addEventListener('ended', () => {
                        const s = get();
                        const trackType = ObjectTypes.getObjectType(s.currentTrack?.type);
                        if (trackType === ObjectTypes.Adventure) {
                            if (isNotEmpty(s.currentTrack?.children)) {
                                set({ showAdventureModal: true });
                            }
                        } else {
                            set({ showAdventureModal: false });
                            if (s.isRepeat && s.isLastTrack()) {
                                s.loadTrack(0);
                            } else if (!s.isLastTrack()) {
                                s.playNext();
                            } else {
                                set({ isPlaying: false, currentTime: 0 });
                            }
                        }
                    });
                    newAudio.addEventListener('error', (e: any) => {
                        set({ error: `Audio playback error: ${e.message}` });
                    });

                    set({ audio: newAudio });

                    if (state.currentTrack?.path) {
                        newAudio.src = state.currentTrack.path;
                        newAudio.currentTime = state.currentTime;
                        if (state.isPlaying) {
                            newAudio.play().catch(() => set({ isPlaying: false }));
                        }
                    }

                    if (state.currentTrack && state.savedPlaybackState.wasPlaying) {
                        get().restorePlaybackState();
                    }
                } catch (error) {
                    console.error('Audio initialization error:', error);
                    set({ error: 'Failed to initialize audio player' });
                }
            },

            loadTrack: (index) => {
                const state = get();
                if (!state.audio) state.initializeAudio();
                const activeQueue = state.currentQueue();
                if (!activeQueue[index]) {
                    set({ error: 'Invalid track index' });
                    return;
                }
                try {
                    const track = activeQueue[index];
                    set({
                        currentTrack: track,
                        currentTrackIndex: index,
                        currentTime: 0,
                        error: null,
                    });
                    const audio = get().audio;
                    if (audio) {
                        audio.src = track.path;
                        audio.load();
                        get().play();
                        get().logPlayActivity(track.uuid, track.type || '');
                    }
                } catch (error) {
                    set({ error: 'Failed to load track' });
                }
            },

            logPlayActivity: async (trackUuid, type) => {
                try {
                    await apiClient.post(ACTIONS.STORE, {
                        uuid: trackUuid,
                        action: Activities.PlaySong,
                        type: type,
                    });
                } catch (error) {
                    console.error('Failed to log play activity:', error);
                }
            },

            setTracks: (newTracks) => {
                const queue = Array.isArray(newTracks) ? newTracks : [newTracks];
                set({ queue });
                if (queue.length > 0) get().loadTrack(0);
            },

            togglePlayPause: () => {
                const state = get();
                if (!state.audio) state.initializeAudio();
                try {
                    const audio = get().audio;
                    if (audio && !audio.src && state.queue.length > 0) {
                        get().loadTrack(state.currentTrackIndex);
                    } else if (state.isPlaying) {
                        get().pause();
                    } else {
                        get().play();
                    }
                } catch (error) {
                    set({ error: 'Failed to toggle playback' });
                }
            },

            play: () => {
                const state = get();
                if (!state.audio) state.initializeAudio();
                const audio = get().audio;
                if (audio) {
                    audio.play().catch((error) => {
                        console.error('Play error:', error);
                        set({ error: 'Failed to play track', isPlaying: false });
                    });
                }
            },

            pause: () => {
                const { audio } = get();
                if (audio) audio.pause();
                set({ isPlaying: false });
            },

            setVolume: (level) => {
                if (level < 0 || level > 1) return;
                set({ volume: level });
                const { audio } = get();
                if (audio) audio.volume = level;
            },

            toggleShuffle: () => {
                const state = get();
                set({ isShuffle: !state.isShuffle });
                if (!state.isShuffle) {
                    get().shuffleQueue();
                } else {
                    const currentTrack = state.currentTrack;
                    const idx = state.queue.findIndex((t) => t.uuid === currentTrack?.uuid);
                    set({ currentTrackIndex: idx >= 0 ? idx : 0 });
                }
            },

            shuffleQueue: () => {
                const { currentTrack, queue } = get();
                if (!currentTrack) return;
                const remaining = queue.filter((t) => t.uuid !== currentTrack.uuid);
                for (let i = remaining.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [remaining[i], remaining[j]] = [remaining[j], remaining[i]];
                }
                set({ shuffledQueue: [currentTrack, ...remaining], currentTrackIndex: 0 });
            },

            playNext: () => {
                const state = get();
                if (state.queue.length === 0) return;
                const queue = state.currentQueue();
                let next = state.currentTrackIndex + 1;
                if (next >= queue.length) next = 0;
                get().loadTrack(next);
            },

            playPrevious: () => {
                const state = get();
                if (state.queue.length === 0) return;
                const queue = state.currentQueue();
                let prev = state.currentTrackIndex - 1;
                if (prev < 0) prev = queue.length - 1;
                get().loadTrack(prev);
            },

            seek: (value) => {
                const { audio, duration } = get();
                if (!audio || !duration) return;
                const numValue = parseFloat(String(value));
                if (isNaN(numValue)) return;
                audio.currentTime = numValue <= 100 ? (numValue / 100) * duration : Math.min(numValue, duration);
            },

            addToQueueNext: (track) => {
                const { queue, currentTrackIndex } = get();
                const newQueue = [...queue];
                newQueue.splice(currentTrackIndex + 1, 0, track);
                set({ queue: newQueue });
            },

            removeFromQueue: (index) => {
                const state = get();
                if (index === state.currentTrackIndex) state.playNext();
                const newQueue = [...state.queue];
                newQueue.splice(index, 1);
                set({ queue: newQueue });
            },

            deleteFromQueue: (uuid) => {
                const state = get();
                const index = state.queue.findIndex((t) => t.uuid === uuid);
                if (index === -1) return;

                if (state.currentTrack && state.currentTrack.uuid === uuid) {
                    const wasPlaying = state.isPlaying;
                    if (state.queue.length === 1) {
                        set({ queue: [], currentTrack: null, currentTrackIndex: 0 });
                        state.pause();
                        return;
                    }
                    const newQueue = [...state.queue];
                    newQueue.splice(index, 1);
                    const newIndex = index >= newQueue.length ? 0 : index;
                    set({ queue: newQueue, currentTrackIndex: newIndex });
                    get().loadTrack(newIndex);
                    if (!wasPlaying) get().pause();
                } else {
                    const newQueue = [...state.queue];
                    newQueue.splice(index, 1);
                    set({
                        queue: newQueue,
                        currentTrackIndex: index < state.currentTrackIndex ? state.currentTrackIndex - 1 : state.currentTrackIndex,
                    });
                }
            },
        }),
        {
            name: 'erocast-audioPlayer',
            partialize: (state) => ({
                currentTrack: state.currentTrack,
                queue: state.queue,
                currentTrackIndex: state.currentTrackIndex,
                isPlaying: state.isPlaying,
                isRepeat: state.isRepeat,
                isShuffle: state.isShuffle,
                volume: state.volume,
                currentTime: state.currentTime,
                savedPlaybackState: state.savedPlaybackState,
            }),
        }
    )
);
