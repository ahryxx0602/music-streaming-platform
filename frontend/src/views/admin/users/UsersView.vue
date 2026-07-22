<script setup lang="ts">
import { shallowRef, ref, computed, onMounted } from 'vue';
import { IconUsers, IconMicrophone2, IconShieldCheck } from '@tabler/icons-vue';
import StatsCard from '@/components/admin/ui/card/StatsCard.vue';
import ArtistTab from './components/tabs/ArtistTab.vue';
import ListenerTab from './components/tabs/ListenerTab.vue';
import StaffTab from './components/tabs/StaffTab.vue';
import api from '@/services/api';

// Dynamic tabs
const activeTab = shallowRef('artist');

const stats = ref({
 listener: 0,
 artist: 0,
 staff: 0,
 total: 0
});

const tabs = computed(() => [
 { id: 'listener', label: 'admin.listeners', component: ListenerTab, count: stats.value.listener },
 { id: 'artist', label: 'admin.artists', component: ArtistTab, count: stats.value.artist },
 { id: 'staff', label: 'admin.staff', component: StaffTab, count: stats.value.staff }
]);

const fetchStats = async () => {
 try {
 const res = await api.get('/admin/users/stats');
 stats.value = res.data.data;
 } catch (error) {
 console.error('Lỗi khi tải thống kê:', error);
 }
};

onMounted(() => {
 fetchStats();
});
</script>

<template>
 <div class="flex flex-col h-full space-y-[var(--spacing-admin-gap,24px)]">
 
 <!-- 1. Stats Cards Overview (Tầng 1) -->
 <div class="grid grid-cols-1 md:grid-cols-3 gap-6 shrink-0">
 <StatsCard 
 :title="$t('admin.listeners')" 
 :value="stats.listener" 
 trend="+12%" 
 :trend-up="true"
 :icon="IconUsers"
 icon-class="text-admin-primary"
 />
 <StatsCard 
 :title="$t('admin.dashboard_stats.total_artists')" 
 :value="stats.artist" 
 trend="+5%" 
 :trend-up="true"
 :icon="IconMicrophone2"
 icon-class="text-indigo-500"
 />
 <StatsCard 
 :title="$t('admin.users_stats.admin_staff')" 
 :value="stats.staff" 
 trend="-1%" 
 :trend-up="false"
 :icon="IconShieldCheck"
 icon-class="text-theme-text-sec"
 />
 </div>

 <!-- 2. The Main Data View (Tầng 2 & 3) -->
 <div class="flex-1 min-h-0 bg-theme-surface rounded-[var(--radius-admin-card,12px)] border border-theme-border shadow-sm flex flex-col overflow-hidden">
 <!-- Tabs Navigation -->
 <div class="flex items-center gap-6 px-6 border-b border-theme-border bg-theme-bg/50 pt-3 shrink-0">
 <button 
 v-for="tab in tabs" 
 :key="tab.id"
 @click="activeTab = tab.id"
 class="pb-3 pt-1 text-sm font-semibold transition-colors relative flex items-center gap-2 outline-none focus-visible:ring-2 focus-visible:ring-admin-primary rounded-t-md group"
 :class="activeTab === tab.id ? 'text-theme-text' : 'text-theme-text-sec hover:text-theme-text'"
 >
 {{ $t(tab.label) }}
 <span class="px-2 py-0.5 rounded-full text-[11px] font-bold transition-colors"
 :class="activeTab === tab.id ? 'bg-admin-primary text-white' : 'bg-theme-bg text-theme-text-sec group-hover:bg-theme-surface-hover border border-theme-border'">
 {{ tab.count }}
 </span>
 <!-- Active Indicator -->
 <div v-if="activeTab === tab.id" class="absolute bottom-0 left-0 w-full h-[2px] bg-admin-primary rounded-t-full shadow-[0_-1px_4px_rgba(59,130,246,0.3)]"></div>
 </button>
 </div>

 <!-- Tab Content Area (Table + Toolbar) -->
 <div class="flex-1 flex flex-col min-h-0 relative">
 <keep-alive>
 <component :is="tabs.find(t => t.id === activeTab)?.component" />
 </keep-alive>
 </div>
 </div>
 
 </div>
</template>
