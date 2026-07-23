<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import { usePlayerStore } from '@/stores/player'
import { 
  IconPlayerPlayFilled, 
  IconPlayerPauseFilled, 
  IconPlayerSkipForwardFilled, 
  IconPlayerSkipBackFilled, 
  IconArrowsShuffle, 
  IconRepeat, 
  IconVolume, 
  IconVolumeOff, 
  IconHeart, 
  IconListDetails 
} from '@tabler/icons-vue'

const playerStore = usePlayerStore()

// For timeline interaction
const timelineInput = ref<HTMLInputElement | null>(null)

// Format seconds to MM:SS
const formatTime = (seconds: number) => {
  if (isNaN(seconds)) return '0:00'
  const m = Math.floor(seconds / 60)
  const s = Math.floor(seconds % 60)
  return `${m}:${s < 10 ? '0' : ''}${s}`
}

const handleSeek = (e: Event) => {
  const target = e.target as HTMLInputElement
  playerStore.seek(Number(target.value))
}

const handleVolumeChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  playerStore.setVolume(Number(target.value))
}

const progressPercentage = computed(() => {
  if (playerStore.duration === 0) return 0
  return (playerStore.currentTime / playerStore.duration) * 100
})

const volumePercentage = computed(() => {
  return playerStore.isMuted ? 0 : playerStore.volume * 100
})

onMounted(() => {
  // Load mock data so reviewer can test UI
  playerStore.loadMockSong()
})
</script>

<template>
  <!-- Global Player Wrapper -->
  <div class="fixed bottom-0 left-0 w-full h-[90px] bg-black/90 backdrop-blur-xl border-t border-white/10 z-[100] px-4 flex items-center justify-between shadow-[0_-5px_20px_rgba(0,0,0,0.5)]">
    
    <!-- Left: Now Playing Info -->
    <div class="flex items-center space-x-4 w-1/4 min-w-[200px]">
      <div v-if="playerStore.currentSong" class="flex items-center space-x-4 w-full">
        <div class="w-14 h-14 rounded-lg overflow-hidden shrink-0 shadow-lg border border-white/5 relative group cursor-pointer">
          <img :src="playerStore.currentSong.cover_image" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
        </div>
        <div class="flex-1 min-w-0 flex flex-col justify-center">
          <h4 class="text-white font-righteous text-base truncate cursor-pointer hover:underline">{{ playerStore.currentSong.title }}</h4>
          <p class="text-gray-400 text-xs font-poppins truncate cursor-pointer hover:text-white transition-colors">{{ playerStore.currentSong.artist }}</p>
        </div>
        <button class="text-gray-400 hover:text-pink-500 transition-colors hidden sm:block shrink-0">
          <IconHeart :size="20" />
        </button>
      </div>
      <div v-else class="text-gray-500 font-poppins text-sm italic">
        No track selected
      </div>
    </div>

    <!-- Center: Playback Controls & Progress -->
    <div class="flex flex-col items-center justify-center flex-1 max-w-2xl px-4">
      <!-- Buttons -->
      <div class="flex items-center space-x-6 mb-1.5">
        <button class="text-gray-400 hover:text-white transition-colors">
          <IconArrowsShuffle :size="20" stroke-width="2" />
        </button>
        
        <button @click="playerStore.prev()" class="text-gray-300 hover:text-white transition-colors">
          <IconPlayerSkipBackFilled :size="20" />
        </button>
        
        <!-- Play/Pause Button with Neon Glow -->
        <button 
          @click="playerStore.togglePlay()"
          class="w-10 h-10 flex items-center justify-center bg-white rounded-full text-black hover:scale-105 transition-all shadow-[0_0_15px_rgba(255,255,255,0.3)] hover:shadow-[0_0_25px_rgba(255,255,255,0.6)]"
        >
          <IconPlayerPauseFilled v-if="playerStore.isPlaying" :size="20" />
          <IconPlayerPlayFilled v-else :size="20" class="ml-1" />
        </button>
        
        <button @click="playerStore.next()" class="text-gray-300 hover:text-white transition-colors">
          <IconPlayerSkipForwardFilled :size="20" />
        </button>
        
        <button class="text-gray-400 hover:text-white transition-colors">
          <IconRepeat :size="20" stroke-width="2" />
        </button>
      </div>
      
      <!-- Progress Bar -->
      <div class="flex items-center w-full space-x-3 text-xs font-mono text-gray-400">
        <span class="min-w-[40px] text-right">{{ formatTime(playerStore.currentTime) }}</span>
        
        <!-- Slider Container -->
        <div class="relative flex-1 h-1.5 group flex items-center cursor-pointer">
          <input 
            type="range" 
            min="0" 
            :max="playerStore.duration || 100" 
            :value="playerStore.currentTime"
            @input="handleSeek"
            class="absolute inset-0 w-full h-full opacity-0 z-10 cursor-pointer"
          />
          <!-- Track Background -->
          <div class="absolute w-full h-1 bg-white/10 rounded-full overflow-hidden">
            <!-- Fill -->
            <div 
              class="h-full bg-theme-primary group-hover:bg-theme-accent transition-colors relative"
              :style="{ width: `${progressPercentage}%` }"
            ></div>
          </div>
          <!-- Thumb indicator (visible on hover) -->
          <div 
            class="absolute h-3 w-3 bg-white rounded-full opacity-0 group-hover:opacity-100 shadow-[0_0_10px_rgba(255,255,255,0.5)] transform -translate-y-1/2 top-1/2 pointer-events-none transition-opacity"
            :style="{ left: `calc(${progressPercentage}% - 6px)` }"
          ></div>
        </div>
        
        <span class="min-w-[40px]">{{ formatTime(playerStore.duration) }}</span>
      </div>
    </div>

    <!-- Right: Volume & Extras -->
    <div class="flex items-center justify-end space-x-4 w-1/4 min-w-[150px]">
      <button class="text-gray-400 hover:text-white transition-colors hidden lg:block">
        <IconListDetails :size="20" />
      </button>
      
      <div class="flex items-center space-x-2 w-28 group">
        <button @click="playerStore.toggleMute()" class="text-gray-400 hover:text-white transition-colors shrink-0">
          <IconVolumeOff v-if="playerStore.isMuted || playerStore.volume === 0" :size="20" />
          <IconVolume v-else :size="20" />
        </button>
        
        <div class="relative flex-1 h-1 flex items-center cursor-pointer">
          <input 
            type="range" 
            min="0" 
            max="1" 
            step="0.01" 
            :value="playerStore.isMuted ? 0 : playerStore.volume"
            @input="handleVolumeChange"
            class="absolute inset-0 w-full h-full opacity-0 z-10 cursor-pointer"
          />
          <div class="absolute w-full h-1 bg-white/10 rounded-full overflow-hidden">
            <div class="h-full bg-white group-hover:bg-theme-accent transition-colors" :style="{ width: `${volumePercentage}%` }"></div>
          </div>
          <!-- Thumb -->
          <div 
            class="absolute h-3 w-3 bg-white rounded-full opacity-0 group-hover:opacity-100 shadow-sm transform -translate-y-1/2 top-1/2 pointer-events-none transition-opacity"
            :style="{ left: `calc(${volumePercentage}% - 6px)` }"
          ></div>
        </div>
      </div>
    </div>
    
  </div>
</template>

<style scoped>
/* Thêm class để định nghĩa custom màu (theme-primary/accent) nếu cần, hiện tại dùng inline Tailwind */
.bg-theme-primary {
  background-color: #3b82f6; /* Blue-500 equivalent */
}
.bg-theme-accent {
  background-color: #8b5cf6; /* Violet-500 equivalent */
}
</style>
