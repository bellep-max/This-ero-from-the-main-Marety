import { defineStore } from 'pinia';
import Activities from '@/Enums/Activities.js';
import ObjectTypes from '@/Enums/ObjectTypes.js';
import { isNotEmpty } from '@/Services/MiscService';
import apiClient from '@/api/client';
import { ACTIONS } from '@/api/endpoints';

export const useAudioPlayerStore = defineStore('audioPlayer', {
    state: () => ({
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
        savedPlaybackState: {
            time: 0,
            wasPlaying: false,
        },
    }),

    getters: {
        currentProgress: (state) => {
            return state.duration ? (state.currentTime / state.duration) * 100 : 0;
        },
        isFirstTrack: (state) => state.currentTrackIndex === 0,
        isLastTrack: (state) => state.currentTrackIndex === state.queue.length - 1,
        hasQueue: (state) => state.queue.length > 0,
        currentQueue: (state) => {
            return state.isShuffle ? state.shuffledQueue : state.queue;
        },
    },

    actions: {
        savePlaybackState() {
            if (this.audio) {
                this.savedPlaybackState = {
                    time: this.audio.currentTime,
                    wasPlaying: this.isPlaying,
                };
            }
        },

        restorePlaybackState() {
            if (!this.audio) {
                this.initializeAudio();
            }

            if (this.audio && this.currentTrack) {
                const { time, wasPlaying } = this.savedPlaybackState;
                this.audio.currentTime = time;

                if (wasPlaying) {
                    this.play();
                }
            }
        },

        initializeAudio() {
            try {
                if (this.audio?.src) {
                    return;
                }

                const newAudio = new Audio();
                this.setupAudioEventListeners(newAudio);
                this.audio = newAudio;
                this.audio.volume = this.volume;

                if (this.currentTrack?.path) {
                    this.audio.src = this.currentTrack.path;
                    this.audio.currentTime = this.currentTime;
                    if (this.isPlaying) {
                        this.audio.play().catch(() => {
                            this.isPlaying = false;
                        });
                    }
                }

                if (this.currentTrack && this.savedPlaybackState.wasPlaying) {
                    this.restorePlaybackState();
                }
            } catch (error) {
                console.error('Audio initialization error:', error);
                this.error = 'Failed to initialize audio player';
            }
        },

        setupAudioEventListeners(audio) {
            const events = {
                timeupdate: () => {
                    this.currentTime = audio.currentTime;
                },
                loadedmetadata: () => {
                    this.duration = audio.duration;
                },
                play: () => {
                    this.isPlaying = true;
                },
                pause: () => {
                    this.isPlaying = false;
                },
                ended: () => {
                    const trackType = ObjectTypes.getObjectType(this.currentTrack.type);

                    if (trackType === ObjectTypes.Adventure) {
                        if (isNotEmpty(this.currentTrack.children)) {
                            this.showAdventureModal = true;
                        }
                    } else {
                        this.showAdventureModal = false;

                        if (this.isRepeat && this.isLastTrack) {
                            this.loadTrack(0);
                        } else if (!this.isLastTrack) {
                            this.playNext();
                        } else {
                            this.isPlaying = false;
                            this.currentTime = 0;
                        }
                    }
                },
                error: (e) => {
                    this.error = `Audio playback error: ${e.message}`;
                    console.error('Audio error:', e);
                },
            };

            Object.entries(events).forEach(([event, handler]) => {
                audio.addEventListener(event, handler);
            });
        },

        unloadAudio() {
            if (!this.audio) return;

            try {
                this.currentTime = this.audio.currentTime;
                this.audio.pause();
                this.audio.src = '';
                this.removeAudioEventListeners();
                this.audio = null;
            } catch (error) {
                console.error('Error cleaning up audio:', error);
            }
        },

        removeAudioEventListeners() {
            if (!this.audio) return;

            const events = ['timeupdate', 'ended', 'loadedmetadata', 'play', 'pause', 'error'];
            events.forEach((event) => {
                this.audio.removeEventListener(event, () => {});
            });
        },

        loadTrack(index) {
            if (!this.audio) {
                this.initializeAudio();
            }

            if (!this.queue[index]) {
                this.error = 'Invalid track index';
                return;
            }

            try {
                this.currentTrack = this.queue[index];
                this.currentTrackIndex = index;
                this.currentTime = 0;
                this.error = null;

                if (this.audio) {
                    this.audio.src = this.queue[index].path;
                    this.audio.load();
                    this.play();
                    this.logPlayActivity(this.currentTrack.uuid, this.currentTrack.type);
                }
            } catch (error) {
                this.error = 'Failed to load track';
                console.error('Track loading error:', error);
            }
        },

        async logPlayActivity(trackUuid, type) {
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

        setTracks(newTracks) {
            this.queue = Array.isArray(newTracks) ? newTracks : [newTracks];
            if (this.queue.length > 0) {
                this.loadTrack(0);
            }
        },

        togglePlayPause() {
            if (!this.audio) {
                this.initializeAudio();
            }

            try {
                if (!this.audio.src && this.queue.length > 0) {
                    this.loadTrack(this.currentTrackIndex);
                } else if (this.isPlaying) {
                    this.pause();
                } else {
                    this.play();
                }
            } catch (error) {
                console.error('Toggle play/pause error:', error);
                this.error = 'Failed to toggle playback';
            }
        },

        play() {
            if (!this.audio) {
                this.initializeAudio();
            }

            if (this.audio) {
                this.audio.play().catch((error) => {
                    console.error('Play error:', error);
                    this.error = 'Failed to play track';
                    this.isPlaying = false;
                });
            }
        },

        pause() {
            if (this.audio) {
                this.audio.pause();
            }
            this.isPlaying = false;
        },

        setVolume(level) {
            if (level < 0 || level > 1) return;
            this.volume = level;
            if (this.audio) {
                this.audio.volume = level;
            }
        },

        toggleShuffle() {
            this.isShuffle = !this.isShuffle;
            if (this.isShuffle) {
                this.shuffleQueue();
            } else {
                const currentTrack = this.currentTrack;
                this.currentTrackIndex = this.queue.findIndex((track) => track.uuid === currentTrack.uuid);
            }
        },

        shuffleQueue() {
            if (!this.currentTrack) return;

            const currentTrack = this.currentTrack;
            const remainingTracks = this.queue.filter((track) => track.uuid !== currentTrack.uuid);

            for (let i = remainingTracks.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [remainingTracks[i], remainingTracks[j]] = [remainingTracks[j], remainingTracks[i]];
            }

            this.shuffledQueue = [currentTrack, ...remainingTracks];
            this.currentTrackIndex = 0;
        },

        playNext() {
            if (this.queue.length === 0) return;

            const queue = this.currentQueue;
            let nextIndex = this.currentTrackIndex + 1;
            if (nextIndex >= queue.length) {
                nextIndex = 0;
            }

            this.loadTrack(nextIndex);
        },

        playPrevious() {
            if (this.queue.length === 0) return;

            const queue = this.currentQueue;
            let prevIndex = this.currentTrackIndex - 1;
            if (prevIndex < 0) {
                prevIndex = queue.length - 1;
            }

            this.loadTrack(prevIndex);
        },

        seek(value) {
            if (!this.audio || !this.duration) return;

            const numValue = parseFloat(value);
            if (isNaN(numValue)) return;

            if (numValue <= 100) {
                this.audio.currentTime = (numValue / 100) * this.duration;
            } else {
                this.audio.currentTime = Math.min(numValue, this.duration);
            }
        },

        addToQueueNext(track) {
            this.queue.splice(this.currentTrackIndex + 1, 0, track);
        },

        removeFromQueue(index) {
            if (index === this.currentTrackIndex) {
                this.playNext();
            }
            this.queue.splice(index, 1);
        },

        deleteFromQueue(uuid) {
            const index = this.queue.findIndex((track) => track.uuid === uuid);
            if (index === -1) return;

            if (this.currentTrack && this.currentTrack.uuid === uuid) {
                const wasPlaying = this.isPlaying;
                if (this.queue.length === 1) {
                    this.queue.splice(index, 1);
                    this.currentTrack = null;
                    this.currentTrackIndex = 0;
                    this.pause();
                    return;
                }
                this.queue.splice(index, 1);
                this.currentTrackIndex = index >= this.queue.length ? 0 : index;
                this.loadTrack(this.currentTrackIndex);
                if (!wasPlaying) {
                    this.pause();
                }
            } else {
                this.queue.splice(index, 1);
                if (index < this.currentTrackIndex) {
                    this.currentTrackIndex--;
                }
            }
        },
    },

    persist: {
        paths: [
            'currentTrack',
            'queue',
            'currentTrackIndex',
            'isPlaying',
            'isRepeat',
            'isShuffle',
            'volume',
            'currentTime',
            'savedPlaybackState',
        ],
    },
});
