<script setup lang="ts">
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import { usePlayerStore } from '@/stores/player'
import { IconPlayerPlayFilled } from '@tabler/icons-vue'

const playerStore = usePlayerStore()

// State
const isLoading = ref(true)

const newReleases = ref<any[]>([])
const trendingArtists = ref<any[]>([])
const heroBanner = ref<any>(null)

const fetchHomeExplore = async () => {
  isLoading.value = true
  
  try {
    const { data } = await api.get('/v1/listener/home-explore')
    if (data.success) {
      newReleases.value = data.data.new_releases || []
      trendingArtists.value = data.data.trending_artists || []
      const banners = data.data.banners || []
      if (banners.length > 0) {
        heroBanner.value = banners[0]
      }
    }
  } catch (error) {
    console.error("Failed to fetch home explore data", error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchHomeExplore()
})

const handlePlay = (id: number) => {
  playerStore.fetchAndPlaySong(id)
}
</script>

<template>
  <div class="h-full space-y-12 pb-32">
    <!-- Hero Banner -->
    <section v-if="isLoading" class="w-full h-[350px] bg-white/5 animate-pulse rounded-3xl border border-white/5"></section>
    
    <section v-else class="relative w-full h-[350px] rounded-3xl overflow-hidden group shadow-[0_10px_30px_rgba(0,0,0,0.5)]">
      <div 
        class="absolute inset-0 bg-cover bg-center transition-transform duration-1000 group-hover:scale-105"
        :style="{ backgroundImage: `url(${heroBanner?.image_url || 'https://images.unsplash.com/photo-1493225457124-a1a2a5370f08?q=80&w=1200&auto=format&fit=crop'})` }"
      ></div>
      <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/50 to-transparent"></div>
      <div class="absolute inset-0 p-10 flex flex-col justify-center">
        <span class="text-theme-primary font-bold tracking-widest text-xs uppercase mb-4 drop-shadow-md">Album Thịnh Hành</span>
        <h1 class="text-5xl md:text-6xl font-righteous text-white mb-4 drop-shadow-lg leading-tight">{{ heroBanner?.title || 'THE WEEKND' }} <br/><span class="text-theme-accent">{{ heroBanner?.subtitle || 'AFTER HOURS' }}</span></h1>
        <p class="text-gray-300 font-poppins max-w-md mb-8 text-sm leading-relaxed">{{ heroBanner?.description || 'Đắm chìm vào không gian âm nhạc Synthwave đặc trưng của The Weeknd với những track nhạc đứng đầu bảng xếp hạng toàn cầu.' }}</p>
        <button class="bg-gradient-to-r from-theme-secondary to-theme-accent hover:from-theme-primary hover:to-theme-secondary text-white w-fit px-8 py-3.5 rounded-full font-bold tracking-wide shadow-[var(--shadow-glow)] hover:shadow-[var(--shadow-glow-purple)] transition-all duration-300 flex items-center space-x-2">
          <IconPlayerPlayFilled :size="20" />
          <span>PHÁT NGAY</span>
        </button>
      </div>
    </section>

    <!-- Nhạc Mới Phát Hành -->
    <section>
      <div class="flex items-end justify-between mb-6">
        <h2 class="text-2xl font-righteous text-white tracking-wide">Nhạc Mới Phát Hành</h2>
        <a href="#" class="text-sm font-poppins text-gray-400 hover:text-white transition-colors">Xem tất cả</a>
      </div>

      <!-- Loading Skeleton -->
      <div v-if="isLoading" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
        <div v-for="i in 5" :key="i" class="space-y-3">
          <div class="w-full aspect-square bg-white/5 animate-pulse rounded-xl"></div>
          <div class="h-4 bg-white/5 animate-pulse rounded w-3/4"></div>
          <div class="h-3 bg-white/5 animate-pulse rounded w-1/2"></div>
        </div>
      </div>

      <!-- Real Data -->
      <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
        <div 
          v-for="song in newReleases" 
          :key="song.id"
          class="group bg-white/5 hover:bg-white/10 backdrop-blur-md p-4 rounded-2xl transition-all duration-300 border border-white/5 hover:border-white/20 cursor-pointer"
        >
          <div class="w-full aspect-square relative mb-4 rounded-xl overflow-hidden shadow-lg bg-black/40">
            <img :src="song.cover_url" :alt="song.title" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
            
            <!-- Hover Overlay -->
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
              <button 
                @click.stop="handlePlay(song.id)"
                class="w-14 h-14 bg-theme-primary text-white rounded-full flex items-center justify-center shadow-[var(--shadow-glow)] hover:scale-110 transition-transform duration-300 translate-y-4 group-hover:translate-y-0"
              >
                <IconPlayerPlayFilled :size="24" class="ml-1" />
              </button>
            </div>
          </div>
          <h3 class="text-white font-righteous text-lg truncate mb-1 group-hover:text-theme-primary transition-colors">{{ song.title }}</h3>
          <p class="text-gray-400 font-poppins text-sm truncate">{{ song.artist?.stage_name || 'Unknown Artist' }}</p>
        </div>
      </div>
    </section>

    <!-- Nghệ Sĩ Thịnh Hành -->
    <section>
      <div class="flex items-end justify-between mb-6">
        <h2 class="text-2xl font-righteous text-white tracking-wide">Nghệ Sĩ Thịnh Hành</h2>
        <a href="#" class="text-sm font-poppins text-gray-400 hover:text-white transition-colors">Xem tất cả</a>
      </div>

      <!-- Loading Skeleton -->
      <div v-if="isLoading" class="grid grid-cols-3 md:grid-cols-5 gap-6">
        <div v-for="i in 5" :key="i" class="flex flex-col items-center space-y-3">
          <div class="w-32 h-32 bg-white/5 animate-pulse rounded-full"></div>
          <div class="h-4 bg-white/5 animate-pulse rounded w-20"></div>
        </div>
      </div>

      <!-- Real Data -->
      <div v-else class="grid grid-cols-3 md:grid-cols-5 gap-6">
        <div 
          v-for="artist in trendingArtists" 
          :key="artist.id"
          class="flex flex-col items-center group cursor-pointer"
        >
          <div class="w-32 h-32 rounded-full overflow-hidden mb-4 border-2 border-transparent group-hover:border-theme-primary transition-colors duration-300 shadow-xl relative bg-black/40">
            <img :src="artist.avatar_url || 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?q=80&w=200&auto=format&fit=crop'" :alt="artist.stage_name" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
              <IconPlayerPlayFilled class="text-white" :size="32" />
            </div>
          </div>
          <h3 class="text-white font-poppins font-medium text-center hover:underline">{{ artist.stage_name }}</h3>
          <span class="text-gray-500 text-xs mt-1 font-poppins">Nghệ sĩ</span>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
/* Custom overrides if needed */
</style>
