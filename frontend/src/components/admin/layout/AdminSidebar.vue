<script setup lang="ts">
import { useRoute } from 'vue-router';
import { 
 IconLayoutDashboard, 
 IconUsers, 
 IconMusic, 
 IconMicrophone2,
 IconDisc,
 IconPlaylist,
 IconCategory,
 IconReportAnalytics,
 IconMessageReport,
 IconSettings,
 IconShieldLock,
 IconPhoto,
 IconMailFast
} from '@tabler/icons-vue';

const route = useRoute();

const menuGroups = [
 {
 title: 'admin.menu.general',
 items: [
 { name: 'admin.menu.dashboard', path: '/admin/dashboard', icon: IconLayoutDashboard },
 { name: 'admin.menu.users', path: '/admin/users', icon: IconUsers },
 { name: 'admin.menu.invites', path: '/admin/invites', icon: IconMailFast },
 ]
 },
 {
 title: 'admin.menu.music',
 items: [
 { name: 'admin.menu.genres', path: '/admin/genres', icon: IconCategory },
 { name: 'admin.menu.inventory', path: '/admin/inventory', icon: IconMusic },
 { name: 'admin.menu.artists', path: '/admin/artists', icon: IconMicrophone2 },
 { name: 'admin.menu.albums', path: '/admin/albums', icon: IconDisc },
 { name: 'admin.menu.playlists', path: '/admin/playlists', icon: IconPlaylist },
 ]
 },
 {
 title: 'admin.menu.moderation',
 items: [
 { name: 'admin.menu.song_moderation', path: '/admin/moderation/songs', icon: IconMusic },
 { name: 'admin.menu.reports', path: '/admin/reports', icon: IconReportAnalytics },
 { name: 'admin.menu.reviews', path: '/admin/reviews', icon: IconMessageReport },
 ]
 },
 {
 title: 'admin.menu.ui',
 items: [
 { name: 'admin.menu.banners', path: '/admin/banners', icon: IconPhoto },
 ]
 },
 {
 title: 'admin.menu.system',
 items: [
 { name: 'admin.menu.settings', path: '/admin/settings', icon: IconSettings },
 { name: 'admin.menu.permissions', path: '/admin/permissions', icon: IconShieldLock },
 ]
 }
];

const isActive = (path: string) => {
 return route.path.startsWith(path);
};
</script>

<template>
 <aside class="w-[260px] flex-shrink-0 flex flex-col h-screen bg-theme-surface border-r border-theme-border z-20 transition-all duration-300">
 
 <!-- Logo -->
 <div class="h-[72px] px-6 flex items-center shrink-0 border-b border-theme-border">
 <div class="flex items-center gap-3 text-theme-text font-bold text-xl tracking-tight">
 <div class="w-8 h-8 rounded-lg bg-theme-accent flex items-center justify-center shadow-lg shadow-theme-accent/20">
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
 <h3 class="px-3 text-[11px] font-bold text-theme-text-sec uppercase tracking-wider mb-2">
 {{ $t(group.title) }}
 </h3>
 <nav class="space-y-0.5 relative">
 <router-link
 v-for="item in group.items"
 :key="item.path"
 :to="item.path"
 class="group relative flex items-center gap-3 px-3 min-h-[44px] rounded-lg text-sm font-medium transition-all duration-200 outline-none focus-visible:ring-2 focus-visible:ring-theme-primary overflow-hidden"
 :class="isActive(item.path) ? 'text-theme-primary bg-theme-primary/10' : 'text-theme-text-sec hover:text-theme-text hover:bg-theme-surface-hover'"
 >
 <!-- Left Indicator for Active state -->
 <div v-if="isActive(item.path)" class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 bg-theme-primary rounded-r-full shadow-[var(--shadow-glow)]"></div>
 
 <component :is="item.icon" size="20" class="transition-colors duration-200" :class="isActive(item.path) ? 'text-theme-primary' : 'text-theme-text-sec group-hover:text-theme-primary/70'" />
 {{ $t(item.name) }}
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
