import { defineStore } from 'pinia';
import api from '../services/api';

export const usePlayerStore = defineStore('player', {
    state: () => ({
        queue: [],
        currentSong: null,
        isPlaying: false,
        progress: 0,
        volume: 1,
    }),
    
    actions: {
        play(song) {
            this.currentSong = song;
            this.isPlaying = true;
        },
        pause() {
            this.isPlaying = false;
            // TODO: Triggers API to save progress /sync history
        },
        togglePlay() {
            this.isPlaying = !this.isPlaying;
        },
        addToQueue(song) {
            this.queue.push(song);
        },
        next() {
            // Logic to play next song in queue
        },
        prev() {
            // Logic to play previous song
        }
    },
    
    persist: true // Save queue & currentSong across reloads
});
