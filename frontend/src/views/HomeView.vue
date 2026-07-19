<script setup lang="ts">
import { ref } from 'vue';
import { IconPlayerPlayFilled } from '@tabler/icons-vue';

const featuredPlaylists = ref([
  { id: 1, title: 'Chill Vibes', desc: 'Nhạc nhẹ nhàng thư giãn', image: 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?q=80&w=400&auto=format&fit=crop' },
  { id: 2, title: 'Top Hits VietNam', desc: 'Những ca khúc hot nhất hiện nay', image: 'https://images.unsplash.com/photo-1493225457124-a1a2a5f5f922?q=80&w=400&auto=format&fit=crop' },
  { id: 3, title: 'Focus & Study', desc: 'Tập trung học tập và làm việc', image: 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?q=80&w=400&auto=format&fit=crop' },
  { id: 4, title: 'Workout Motivation', desc: 'Động lực tập luyện', image: 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=400&auto=format&fit=crop' },
]);

const newReleases = ref([
  { id: 1, title: 'Midnight', artist: 'The Weekend', image: 'https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?q=80&w=200&auto=format&fit=crop' },
  { id: 2, title: 'Losing Control', artist: 'Alan Walker', image: 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?q=80&w=200&auto=format&fit=crop' },
  { id: 3, title: 'Ocean Eyes', artist: 'Billie Eilish', image: 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?q=80&w=200&auto=format&fit=crop' },
  { id: 4, title: 'Starboy', artist: 'The Weekend', image: 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?q=80&w=200&auto=format&fit=crop' },
  { id: 5, title: 'Faded', artist: 'Alan Walker', image: 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?q=80&w=200&auto=format&fit=crop' },
]);
</script>

<template>
  <div class="home-view pb-24">
    
    <!-- Hero Banner -->
    <div class="relative w-full h-72 rounded-3xl overflow-hidden mb-12 shadow-2xl">
      <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?q=80&w=1200&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover opacity-60" />
      <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
      <div class="absolute bottom-0 left-0 p-8 w-full">
        <span class="px-3 py-1 bg-blue-500 text-white text-xs font-bold uppercase rounded-full tracking-wider mb-3 inline-block">Trending Now</span>
        <h2 class="text-4xl md:text-5xl font-black text-theme-text mb-2">Aurora EDM Festival</h2>
        <p class="text-theme-text-sec max-w-2xl text-sm md:text-base">Hòa mình vào không gian âm nhạc điện tử sôi động nhất hành tinh với hàng ngàn track nhạc bản quyền chất lượng cao.</p>
        <div class="mt-6 flex gap-4">
          <button class="bg-blue-500 hover:bg-blue-400 text-white font-bold py-3 px-8 rounded-full shadow-lg shadow-blue-500/30 transition transform hover:scale-105 flex items-center gap-2">
            <IconPlayerPlayFilled size="20" />
            Phát ngay
          </button>
          <button class="bg-theme-surface hover:bg-theme-surface-hover backdrop-blur-md border border-theme-border text-theme-text font-bold py-3 px-8 rounded-full transition">Theo dõi</button>
        </div>
      </div>
    </div>

    <!-- Featured Playlists -->
    <section class="mb-12">
      <div class="flex items-end justify-between mb-6">
        <h3 class="text-2xl font-bold text-theme-text tracking-tight">Dành cho bạn</h3>
        <a href="#" class="text-sm font-semibold text-theme-text-sec hover:text-theme-text uppercase tracking-wider">Xem tất cả</a>
      </div>
      
      <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <div v-for="item in featuredPlaylists" :key="item.id" class="group bg-theme-surface-hover hover:bg-theme-border border border-transparent hover:border-theme-primary p-4 rounded-2xl transition duration-300 cursor-pointer relative">
          <div class="relative w-full aspect-square rounded-xl overflow-hidden mb-4 shadow-lg">
            <img :src="item.image" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
            <!-- Play Button Overlay -->
            <div class="absolute bottom-2 right-2 w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 shadow-xl shadow-blue-500/40">
              <IconPlayerPlayFilled class="text-theme-text ml-1" size="24" />
            </div>
          </div>
          <h4 class="text-theme-text font-bold truncate mb-1">{{ item.title }}</h4>
          <p class="text-theme-text-sec text-sm line-clamp-2">{{ item.desc }}</p>
        </div>
      </div>
    </section>

    <!-- New Releases -->
    <section>
      <div class="flex items-end justify-between mb-6">
        <h3 class="text-2xl font-bold text-theme-text tracking-tight">Mới phát hành</h3>
      </div>
      
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-6">
        <div v-for="item in newReleases" :key="item.id" class="group cursor-pointer">
          <div class="relative w-full aspect-square rounded-full md:rounded-2xl overflow-hidden mb-3 shadow-lg">
            <img :src="item.image" class="w-full h-full object-cover group-hover:scale-110 transition duration-500" />
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center">
              <IconPlayerPlayFilled class="text-theme-text w-10 h-10 drop-shadow-lg" />
            </div>
          </div>
          <div class="text-center md:text-left px-1">
            <h4 class="text-theme-text font-bold truncate text-sm">{{ item.title }}</h4>
            <p class="text-theme-text-sec text-xs truncate mt-0.5">{{ item.artist }}</p>
          </div>
        </div>
      </div>
    </section>

  </div>
</template>

<style scoped>
/* Optional styling */
</style>
