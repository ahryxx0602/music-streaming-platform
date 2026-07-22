<script setup lang="ts">
import { useRoute } from 'vue-router';
import { 
  IconLayoutDashboard, 
  IconUpload, 
  IconDisc, 
  IconUser,
  IconChartBar
} from '@tabler/icons-vue';

const route = useRoute();

const menuItems = [
  { name: 'artist.menu.dashboard', path: '/artist/dashboard', icon: IconLayoutDashboard },
  { name: 'artist.menu.upload', path: '/artist/upload', icon: IconUpload },
  { name: 'artist.menu.releases', path: '/artist/releases', icon: IconDisc },
  { name: 'artist.menu.stats', path: '/artist/stats', icon: IconChartBar },
  { name: 'artist.menu.profile', path: '/artist/profile', icon: IconUser },
];

const isActive = (path: string) => {
  return route.path.startsWith(path);
};
</script>

<template>
  <aside class="w-[280px] flex-shrink-0 flex flex-col h-screen bg-theme-surface/60 backdrop-blur-xl border-r border-theme-border/50 z-20 transition-all duration-300">
    
    <!-- Logo -->
    <div class="h-[80px] px-8 flex items-center shrink-0 border-b border-theme-border/50">
      <div class="flex items-center gap-3 text-theme-text font-heading text-2xl tracking-wider uppercase">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-theme-primary to-theme-accent flex items-center justify-center shadow-[var(--shadow-glow-purple)]">
          <IconDisc class="text-white" size="24" stroke-width="2" />
        </div>
        ahryxx<span class="text-theme-accent font-light">ARTIST</span>
      </div>
    </div>

    <!-- Navigation -->
    <div class="flex-1 overflow-y-auto py-6 px-4 custom-scrollbar space-y-2">
      <router-link
        v-for="item in menuItems"
        :key="item.path"
        :to="item.path"
        class="group relative flex items-center gap-4 px-4 min-h-[52px] rounded-xl text-[15px] font-medium transition-all duration-300 outline-none overflow-hidden"
        :class="isActive(item.path) ? 'text-white bg-white/5 border border-white/10 shadow-[var(--shadow-glow-purple)]' : 'text-theme-text-sec hover:text-white hover:bg-white/5'"
      >
        <!-- Active indicator -->
        <div v-if="isActive(item.path)" class="absolute left-0 top-1/2 -translate-y-1/2 w-[4px] h-[60%] bg-theme-accent rounded-r-full shadow-[var(--shadow-glow)]"></div>
        
        <component :is="item.icon" size="22" class="transition-colors duration-300" :class="isActive(item.path) ? 'text-theme-accent' : 'text-theme-text-sec group-hover:text-theme-accent/70'" />
        <span class="tracking-wide">{{ $t(item.name) }}</span>
      </router-link>
    </div>
    
    <!-- Bottom Upload CTA -->
    <div class="p-6 border-t border-theme-border/50">
      <router-link to="/artist/upload" class="w-full flex items-center justify-center gap-2 py-3.5 bg-gradient-to-r from-theme-secondary to-theme-accent rounded-xl text-white font-semibold tracking-wide hover:shadow-[var(--shadow-glow)] transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]">
        <IconUpload size="20" stroke-width="2.5" />
        TẢI NHẠC LÊN
      </router-link>
    </div>
  </aside>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.05); border-radius: 4px; }
.custom-scrollbar:hover::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); }
</style>
