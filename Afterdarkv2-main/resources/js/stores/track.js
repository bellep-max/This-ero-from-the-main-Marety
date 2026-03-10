import { defineStore } from 'pinia';
import Activities from '@/Enums/Activities.js';
import ObjectTypes from '@/Enums/ObjectTypes.js';
import { isNotEmpty } from '@/Services/MiscService.js';

/**
 * @typedef {Object} Track
 * @property {string} uuid
 * @property {string} path
 * @property {string} title
 * @property {string} artwork
 */

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
        /**
         * Get the current queue based on shuffle state
         * @returns {Track[]}
         */
        currentQueue: (state) => {
            return state.isShuffle ? state.shuffledQueue : state.queue;
        },
    },

    actions: {
        /**
         * Save the current playback state before page transition
         */
        savePlaybackState() {
            if (this.audio) {
                this.savedPlaybackState = {
                    time: this.audio.currentTime,
                    wasPlaying: this.isPlaying,
                };
            }
        },

        /**
         * Restore playback state after page transition
         */
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

        /**
         * Initialize audio instance and set up event listeners
         */
        initializeAudio() {
            try {
                // Don't recreate audio if it exists and is valid
                if (this.audio?.src) {
                    return;
                }

                // Create new audio instance
                const newAudio = new Audio();

                // Set up event listeners before assigning to state
                this.setupAudioEventListeners(newAudio);

                // Only after successful setup, assign to state
                this.audio = newAudio;
                this.audio.volume = this.volume;

                // Restore current track if exists
                if (this.currentTrack?.path) {
                    this.audio.src = this.currentTrack.path;
                    this.audio.currentTime = this.currentTime;
                    if (this.isPlaying) {
                        this.audio.play().catch(() => {
                            this.isPlaying = false;
                        });
                    }
                }

                // After successful initialization, restore state if needed
                if (this.currentTrack && this.savedPlaybackState.wasPlaying) {
                    this.restorePlaybackState();
                }
            } catch (error) {
                console.error('Audio initialization error:', error);
                this.error = 'Failed to initialize audio player';
            }
        },

        /**
         * Set up all audio event listeners
         * @param {HTMLAudioElement} audio
         */
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

        /**
         * Clean up audio instance
         */
        unloadAudio() {
            if (!this.audio) return;

            try {
                // Save current time before cleanup
                this.currentTime = this.audio.currentTime;

                // Cleanup
                this.audio.pause();
                this.audio.src = '';
                this.removeAudioEventListeners();
                this.audio = null;
            } catch (error) {
                console.error('Error cleaning up audio:', error);
            }
        },

        /**
         * Remove all audio event listeners
         */
        removeAudioEventListeners() {
            if (!this.audio) return;

            const events = ['timeupdate', 'ended', 'loadedmetadata', 'play', 'pause', 'error'];
            events.forEach((event) => {
                this.audio.removeEventListener(event, () => {});
            });
        },

        /**
         * Load a track by index
         * @param {number} index
         */
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

        /**
         * Log play activity to the server
         * @param {string} trackUuid
         * @param {string} type
         */
        async logPlayActivity(trackUuid, type) {
            try {
                await axios.post(route('actions.store'), {
                    uuid: trackUuid,
                    action: Activities.PlaySong,
                    type: type,
                });
            } catch (error) {
                console.error('Failed to log play activity:', error);
            }
        },

        /**
         * Set new tracks in the queue
         * @param {Track|Track[]} newTracks
         */
        setTracks(newTracks) {
            this.queue = Array.isArray(newTracks) ? newTracks : [newTracks];
            if (this.queue.length > 0) {
                this.loadTrack(0);
            }
        },

        /**
         * Toggle play/pause state
         */
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

        /**
         * Play the current track
         */
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

        /**
         * Pause the current track
         */
        pause() {
            if (this.audio) {
                this.audio.pause();
            }
            this.isPlaying = false;
        },

        /**
         * Set the volume level
         * @param {number} level - Volume level between 0 and 1
         */
        setVolume(level) {
            if (level < 0 || level > 1) return;
            this.volume = level;
            if (this.audio) {
                this.audio.volume = level;
            }
        },

        /**
         * Toggle shuffle mode
         */
        toggleShuffle() {
            this.isShuffle = !this.isShuffle;
            if (this.isShuffle) {
                this.shuffleQueue();
            } else {
                // When turning shuffle off, find the current track in the original queue
                const currentTrack = this.currentTrack;
                this.currentTrackIndex = this.queue.findIndex((track) => track.uuid === currentTrack.uuid);
            }
        },

        /**
         * Shuffle the queue while keeping current track as first item
         */
        shuffleQueue() {
            if (!this.currentTrack) return;

            const currentTrack = this.currentTrack;
            const remainingTracks = this.queue.filter((track) => track.uuid !== currentTrack.uuid);

            // Fisher-Yates shuffle algorithm
            for (let i = remainingTracks.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [remainingTracks[i], remainingTracks[j]] = [remainingTracks[j], remainingTracks[i]];
            }

            this.shuffledQueue = [currentTrack, ...remainingTracks];
            this.currentTrackIndex = 0;
        },

        /**
         * Play the next track in the queue
         */
        playNext() {
            if (this.queue.length === 0) return;

            const queue = this.currentQueue;
            let nextIndex = this.currentTrackIndex + 1;
            if (nextIndex >= queue.length) {
                nextIndex = 0; // Loop back to the start
            }

            this.loadTrack(nextIndex);
        },

        /**
         * Play the previous track in the queue
         */
        playPrevious() {
            if (this.queue.length === 0) return;

            const queue = this.currentQueue;
            let prevIndex = this.currentTrackIndex - 1;
            if (prevIndex < 0) {
                prevIndex = queue.length - 1; // Loop to the end
            }

            this.loadTrack(prevIndex);
        },

        /**
         * Seek to a specific position in the track
         * @param {number|string} value - Can be either percentage (0-100) or time in seconds
         */
        seek(value) {
            if (!this.audio || !this.duration) return;

            const numValue = parseFloat(value);
            if (isNaN(numValue)) return;

            if (numValue <= 100) {
                // Treat as percentage
                this.audio.currentTime = (numValue / 100) * this.duration;
            } else {
                // Treat as direct time in seconds
                this.audio.currentTime = Math.min(numValue, this.duration);
            }
        },

        /**
         * Add a track to the queue next to the current track
         * @param {Track} track
         */
        addToQueueNext(track) {
            this.queue.splice(this.currentTrackIndex + 1, 0, track);
        },

        /**
         * Remove a track from the queue
         * @param {number} index
         */
        removeFromQueue(index) {
            if (index === this.currentTrackIndex) {
                this.playNext();
            }
            this.queue.splice(index, 1);
        },

        /**
         * Delete a track from the queue by its UUID
         * @param {string} uuid - The UUID of the track to delete
         */
        deleteFromQueue(uuid) {
            const index = this.queue.findIndex((track) => track.uuid === uuid);
            if (index === -1) return;

            // If deleting currently playing track
            if (this.currentTrack && this.currentTrack.uuid === uuid) {
                const wasPlaying = this.isPlaying;
                // If it's the last track, go to the first one
                if (this.queue.length === 1) {
                    this.queue.splice(index, 1);
                    this.currentTrack = null;
                    this.currentTrackIndex = 0;
                    this.pause();
                    return;
                }
                // Otherwise, play the next track
                this.queue.splice(index, 1);
                this.currentTrackIndex = index >= this.queue.length ? 0 : index;
                this.loadTrack(this.currentTrackIndex);
                if (!wasPlaying) {
                    this.pause();
                }
            } else {
                // If deleting a track that's not currently playing
                this.queue.splice(index, 1);
                // Adjust currentTrackIndex if the deleted track was before it
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
            'savedPlaybackState', // Add this to persist playback state
        ],
    },
});
