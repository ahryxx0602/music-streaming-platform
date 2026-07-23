<script setup lang="ts">
import { ref, watch } from 'vue'
import api from '@/services/api'
import { usePlayerStore } from '@/stores/player'
import { IconSearch, IconPlayerPlayFilled } from '@tabler/icons-vue'

const playerStore = usePlayerStore()

// State
const searchQuery = ref('')
const isLoading = ref(false)
const hasSearched = ref(false)

// Results Data
const topResult = ref<any>(null)
const songsResult = ref<any[]>([])

// Debounce logic
let timeout: ReturnType<typeof setTimeout> | null = null

watch(searchQuery, (newVal) => {
  if (timeout) clearTimeout(timeout)
  
  const query = newVal.trim()
  
  if (!query) {
    topResult.value = null
    songsResult.value = []
    hasSearched.value = false
    isLoading.value = false
    return
  }
  
  isLoading.value = true
  hasSearched.value = true
  
  timeout = setTimeout(() => {
    fetchSearchResults(query)
  }, 500)
})

const fetchSearchResults = async (query: string) => {
  try {
    const { data } = await api.get(`/v1/listener/search?q=${encodeURIComponent(query)}`)
    if (data.success) {
      const { songs, artists, albums } = data.data
      
      songsResult.value = songs || []
      
      // Build top result logic: prefer artist, then song, then album
      if (artists && artists.length > 0) {
        topResult.value = {
          type: 'Nghệ sĩ',
          name: artists[0].stage_name,
          image: artists[0].avatar_url || 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?q=80&w=300&auto=format&fit=crop',
          description: 'Nghệ sĩ liên quan'
        }
      } else if (songs && songs.length > 0) {
        topResult.value = {
          type: 'Bài hát',
          name: songs[0].title,
          image: songs[0].cover_url,
          description: songs[0].artist?.stage_name || 'Unknown Artist'
        }
      } else if (albums && albums.length > 0) {
        topResult.value = {
          type: 'Album',
          name: albums[0].title,
          image: albums[0].cover_url,
          description: albums[0].artist?.stage_name || 'Unknown Artist'
        }
      } else {
        topResult.value = null
      }
    }
  } catch (error) {
    console.error("Search failed:", error)
  } finally {
    isLoading.value = false
  }
}

const handlePlay = (id: number) => {
  playerStore.fetchAndPlaySong(id)
}
</script>

<template>
  <div class="h-full flex flex-col space-y-8 pb-32 max-w-7xl mx-auto px-4 pt-10">
    <!-- Thanh Tìm Kiếm To Bự -->
    <div class="w-full max-w-3xl mx-auto relative group">
      <div class="absolute inset-0 bg-theme-primary/20 blur-xl rounded-full group-hover:bg-theme-primary/30 transition-colors duration-500"></div>
      <div class="relative bg-black/60 backdrop-blur-xl border-2 border-white/10 group-hover:border-theme-primary/50 rounded-full flex items-center px-6 py-4 transition-colors duration-300 shadow-[0_10px_30px_rgba(0,0,0,0.5)]">
        <IconSearch class="text-gray-400 group-hover:text-theme-primary transition-colors" :size="32" />
        <input 
          v-model="searchQuery"
          type="text" 
          placeholder="Bạn muốn nghe gì hôm nay?" 
          class="flex-1 bg-transparent border-none outline-none text-white text-2xl px-6 font-righteous placeholder:text-gray-600 placeholder:font-poppins placeholder:text-xl"
        >
      </div>
    </div>

    <!-- Trạng Thái Trống -->
    <div v-if="!hasSearched" class="flex-1 flex flex-col items-center justify-center text-center mt-20 opacity-50">
      <IconSearch :size="80" class="text-gray-600 mb-6" />
      <h2 class="text-2xl font-righteous text-white mb-2">Tìm kiếm Giai điệu</h2>
      <p class="text-gray-400 font-poppins">Nhập tên bài hát, nghệ sĩ hoặc album để bắt đầu.</p>
    </div>

    <!-- Kết quả -->
    <div v-else class="flex-1 animate-fade-in">
      
      <!-- Skeleton Loading -->
      <div v-if="isLoading" class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        <!-- Top Result Skeleton -->
        <div class="lg:col-span-2 space-y-4">
          <h2 class="text-2xl font-righteous text-white tracking-wide mb-6 opacity-0">.</h2>
          <div class="bg-white/5 animate-pulse rounded-3xl p-6 h-[250px]"></div>
        </div>
        <!-- Songs List Skeleton -->
        <div class="lg:col-span-3 space-y-4">
          <h2 class="text-2xl font-righteous text-white tracking-wide mb-6 opacity-0">.</h2>
          <div v-for="i in 4" :key="i" class="h-[72px] bg-white/5 animate-pulse rounded-xl w-full"></div>
        </div>
      </div>

      <!-- Hiển thị Dữ liệu -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-5 gap-10">
        
        <!-- Cột Trái: Top Result -->
        <div class="lg:col-span-2" v-if="topResult">
          <h2 class="text-2xl font-righteous text-white tracking-wide mb-6">Kết quả Hàng đầu</h2>
          <div class="bg-[#181818] hover:bg-[#282828] p-6 rounded-3xl transition-colors duration-300 relative group cursor-pointer border border-white/5 shadow-xl">
            <img 
              :src="topResult.image" 
              class="w-24 h-24 rounded-full object-cover shadow-lg mb-6 group-hover:scale-105 transition-transform duration-300"
            />
            <h3 class="text-4xl font-righteous text-white mb-2 truncate">{{ topResult.name }}</h3>
            <div class="flex items-center space-x-3">
              <p class="text-gray-400 font-poppins text-sm">{{ topResult.description }}</p>
              <span class="px-3 py-1 bg-black/50 text-white text-xs font-bold uppercase rounded-full tracking-wider">{{ topResult.type }}</span>
            </div>

            <!-- Nút Play bay lơ lửng -->
            <button 
              class="absolute bottom-6 right-6 w-14 h-14 bg-theme-primary text-white rounded-full flex items-center justify-center shadow-[var(--shadow-glow)] opacity-0 group-hover:opacity-100 hover:scale-110 hover:bg-theme-accent transition-all duration-300 translate-y-4 group-hover:translate-y-0"
            >
              <IconPlayerPlayFilled :size="24" class="ml-1" />
            </button>
          </div>
        </div>

        <!-- Cột Phải: Danh sách Bài hát -->
        <div class="lg:col-span-3" v-if="songsResult.length">
          <h2 class="text-2xl font-righteous text-white tracking-wide mb-6">Bài Hát</h2>
          <div class="space-y-2">
            <div 
              v-for="song in songsResult" 
              :key="song.id"
              class="flex items-center p-3 rounded-xl hover:bg-white/10 transition-colors group cursor-pointer"
              @click="handlePlay(song.id)"
            >
              <div class="relative w-12 h-12 mr-4 rounded overflow-hidden shrink-0">
                <img :src="song.cover_url" class="w-full h-full object-cover" />
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                  <IconPlayerPlayFilled class="text-white" :size="20" />
                </div>
              </div>
              <div class="flex-1 min-w-0 flex flex-col justify-center">
                <h4 class="text-white font-medium font-poppins truncate group-hover:text-theme-primary transition-colors">{{ song.title }}</h4>
                <p class="text-gray-400 text-sm font-poppins truncate hover:underline hover:text-white">{{ song.artist?.stage_name || 'Unknown Artist' }}</p>
              </div>
              <div class="text-gray-500 text-sm font-mono opacity-0 group-hover:opacity-100 transition-opacity">
                <!-- Convert seconds to duration string if needed -->
                {{ song.duration ? `${Math.floor(song.duration/60)}:${('0'+song.duration%60).slice(-2)}` : '' }}
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.4s ease-out forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
