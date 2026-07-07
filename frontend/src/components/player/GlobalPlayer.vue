<template>
  <div class="h-24 bg-zinc-950 border-t border-zinc-900 flex items-center justify-between px-6">
    <!-- Song Info -->
    <div class="flex items-center w-1/4">
      <div v-if="playerStore.currentSong" class="flex items-center space-x-4">
        <img :src="playerStore.currentSong.cover" class="w-14 h-14 rounded-md shadow-lg" alt="Cover" />
        <div>
          <h4 class="text-sm font-semibold text-white">{{ playerStore.currentSong.title }}</h4>
          <p class="text-xs text-zinc-400">{{ playerStore.currentSong.artist }}</p>
        </div>
      </div>
    </div>
    
    <!-- Controls -->
    <div class="flex flex-col items-center justify-center w-2/4 max-w-xl">
      <div class="flex items-center space-x-6">
        <button @click="playerStore.prev()" class="text-zinc-400 hover:text-white transition">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 6h2v12H6zm3.5 6l8.5 6V6z"/></svg>
        </button>
        <button @click="playerStore.togglePlay()" class="w-10 h-10 flex items-center justify-center bg-white text-black rounded-full hover:scale-105 transition">
          <svg v-if="!playerStore.isPlaying" class="w-5 h-5 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
          <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
        </button>
        <button @click="playerStore.next()" class="text-zinc-400 hover:text-white transition">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/></svg>
        </button>
      </div>
      <div class="w-full flex items-center mt-2 space-x-2 text-xs text-zinc-400">
        <span>0:00</span>
        <div class="flex-1 h-1.5 bg-zinc-800 rounded-full overflow-hidden cursor-pointer group">
          <div class="h-full bg-white group-hover:bg-emerald-500 w-1/3 rounded-full relative"></div>
        </div>
        <span>3:45</span>
      </div>
    </div>
    
    <!-- Volume -->
    <div class="w-1/4 flex justify-end">
      <!-- Volume placeholder -->
    </div>
  </div>
</template>

<script setup>
import { usePlayerStore } from '../../stores/playerStore';
const playerStore = usePlayerStore();
</script>
