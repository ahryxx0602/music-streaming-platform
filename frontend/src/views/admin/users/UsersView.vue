<script setup lang="ts">
import { shallowRef } from 'vue';
import { IconUsers, IconMicrophone2, IconShieldCheck } from '@tabler/icons-vue';
import StatsCard from '@/components/admin/ui/card/StatsCard.vue';
import ArtistTab from './components/tabs/ArtistTab.vue';
import ListenerTab from './components/tabs/ListenerTab.vue';
import StaffTab from './components/tabs/StaffTab.vue';

// Dynamic tabs
const activeTab = shallowRef('artist');

const tabs = [
  { id: 'listener', label: 'Listeners', component: ListenerTab, count: 2418 },
  { id: 'artist', label: 'Artists', component: ArtistTab, count: 53 },
  { id: 'staff', label: 'Staff', component: StaffTab, count: 6 }
];
</script>

<template>
  <div class="flex flex-col h-full space-y-[var(--spacing-admin-gap,24px)]">
    
    <!-- 1. Stats Cards Overview (Tầng 1) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 shrink-0">
      <StatsCard 
        title="Total Users" 
        value="2,418" 
        trend="+12%" 
        :trend-up="true"
        :icon="IconUsers"
        icon-class="text-admin-primary"
      />
      <StatsCard 
        title="Total Artists" 
        value="53" 
        trend="+5%" 
        :trend-up="true"
        :icon="IconMicrophone2"
        icon-class="text-indigo-500"
      />
      <StatsCard 
        title="Admin Staff" 
        value="6" 
        trend="-1%" 
        :trend-up="false"
        :icon="IconShieldCheck"
        icon-class="text-slate-500"
      />
    </div>

    <!-- 2. The Main Data View (Tầng 2 & 3) -->
    <div class="flex-1 min-h-0 bg-white rounded-[var(--radius-admin-card,12px)] border border-slate-200 shadow-sm flex flex-col overflow-hidden">
      <!-- Tabs Navigation -->
      <div class="flex items-center gap-6 px-6 border-b border-slate-200 bg-slate-50/50 pt-3 shrink-0">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          @click="activeTab = tab.id"
          class="pb-3 pt-1 text-sm font-semibold transition-colors relative flex items-center gap-2 outline-none focus-visible:ring-2 focus-visible:ring-admin-primary rounded-t-md group"
          :class="activeTab === tab.id ? 'text-slate-900' : 'text-slate-500 hover:text-slate-700'"
        >
          {{ tab.label }}
          <span class="px-2 py-0.5 rounded-full text-[11px] font-bold transition-colors"
            :class="activeTab === tab.id ? 'bg-admin-primary text-white' : 'bg-slate-200 text-slate-600 group-hover:bg-slate-300'">
            {{ tab.count }}
          </span>
          <!-- Active Indicator -->
          <div v-if="activeTab === tab.id" class="absolute bottom-0 left-0 w-full h-[2px] bg-slate-900 rounded-t-full shadow-[0_-1px_4px_rgba(15,23,42,0.3)]"></div>
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
