<script setup lang="ts">
import { useRoute } from 'vue-router';
import { 
  IconLayoutDashboard, 
  IconUsers, 
  IconMusic, 
  IconMicrophone2,
  IconDisc,
  IconPlaylist,
  IconReportAnalytics,
  IconMessageReport,
  IconSettings,
  IconShieldLock
} from '@tabler/icons-vue';

const route = useRoute();

const menuGroups = [
  {
    title: 'General',
    items: [
      { name: 'Dashboard', path: '/admin/dashboard', icon: IconLayoutDashboard },
      { name: 'User Management', path: '/admin/users', icon: IconUsers },
    ]
  },
  {
    title: 'Music',
    items: [
      { name: 'Music', path: '/admin/music', icon: IconMusic },
      { name: 'Artist', path: '/admin/artists', icon: IconMicrophone2 },
      { name: 'Albums', path: '/admin/albums', icon: IconDisc },
      { name: 'Playlist', path: '/admin/playlists', icon: IconPlaylist },
    ]
  },
  {
    title: 'Moderation',
    items: [
      { name: 'Reports', path: '/admin/reports', icon: IconReportAnalytics },
      { name: 'Reviews', path: '/admin/reviews', icon: IconMessageReport },
    ]
  },
  {
    title: 'System',
    items: [
      { name: 'Settings', path: '/admin/settings', icon: IconSettings },
      { name: 'Permissions', path: '/admin/permissions', icon: IconShieldLock },
    ]
  }
];

const isActive = (path: string) => {
  return route.path.startsWith(path);
};
</script>

<template>
  <aside class="w-[260px] flex-shrink-0 flex flex-col h-screen text-slate-300 z-20 transition-all duration-300" 
         style="background: linear-gradient(180deg, #111827, #0F172A);">
    
    <!-- Logo -->
    <div class="h-[72px] px-6 flex items-center shrink-0">
      <div class="flex items-center gap-3 text-white font-bold text-xl tracking-tight">
        <div class="w-8 h-8 rounded-lg bg-admin-primary flex items-center justify-center shadow-lg shadow-admin-primary/20">
          <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
          </svg>
        </div>
        ahryxx
      </div>
    </div>

    <!-- Navigation -->
    <div class="flex-1 overflow-y-auto py-2 custom-scrollbar">
      <div v-for="(group, idx) in menuGroups" :key="idx" class="mb-6 px-4">
        <h3 class="px-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">
          {{ group.title }}
        </h3>
        <nav class="space-y-0.5 relative">
          <router-link
            v-for="item in group.items"
            :key="item.path"
            :to="item.path"
            class="group relative flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-200 outline-none focus-visible:ring-2 focus-visible:ring-admin-primary overflow-hidden"
            :class="isActive(item.path) ? 'text-white bg-slate-800/50' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/30'"
          >
            <!-- Left Indicator for Active state -->
            <div v-if="isActive(item.path)" class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-4 bg-admin-primary rounded-r-full shadow-[0_0_8px_rgba(59,130,246,0.8)]"></div>
            
            <component :is="item.icon" size="18" class="transition-colors duration-200" :class="isActive(item.path) ? 'text-admin-primary' : 'text-slate-500 group-hover:text-slate-400'" />
            {{ item.name }}
          </router-link>
        </nav>
      </div>
    </div>
  </aside>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.05); border-radius: 4px; }
.custom-scrollbar:hover::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.15); }
</style>
