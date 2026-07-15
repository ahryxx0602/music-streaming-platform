<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { IconSearch, IconBell, IconMoon, IconChevronRight } from '@tabler/icons-vue';

const route = useRoute();
const pathSegments = computed(() => {
  const path = route.path.replace('/admin', '');
  if (!path || path === '/') return ['Dashboard', 'Overview'];
  const segments = path.split('/').filter(p => p);
  return ['Dashboard', ...segments.map(s => s.charAt(0).toUpperCase() + s.slice(1))];
});
</script>

<template>
  <!-- Header cao 72px -->
  <header class="h-[72px] bg-white border-b border-slate-200 flex items-center justify-between px-8 flex-shrink-0 z-10 shadow-sm transition-all duration-200">
    
    <!-- Left: Breadcrumbs hiện đại (Linear Style) -->
    <div class="flex items-center text-sm font-medium text-slate-500">
      <template v-for="(segment, index) in pathSegments" :key="index">
        <span 
          class="cursor-pointer transition-colors"
          :class="index === pathSegments.length - 1 ? 'text-slate-900 font-semibold' : 'hover:text-slate-800'"
        >
          {{ segment }}
        </span>
        <IconChevronRight v-if="index < pathSegments.length - 1" size="14" class="mx-2 text-slate-400" />
      </template>
    </div>

    <!-- Right: Command Search, Notifications, Theme, Profile -->
    <div class="flex items-center gap-5">
      <!-- Command Search -->
      <div class="relative hidden md:block group">
        <IconSearch size="14" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-admin-primary transition-colors" />
        <input 
          type="text" 
          placeholder="Command Search (Ctrl + K)" 
          class="h-9 pl-9 pr-12 text-sm border border-slate-200 rounded-lg bg-slate-50 focus:outline-none focus:ring-1 focus:ring-admin-primary focus:border-admin-primary focus:bg-white w-64 transition-all"
        />
        <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1">
          <kbd class="px-1.5 py-0.5 text-[10px] font-medium text-slate-500 bg-white border border-slate-200 rounded">⌘</kbd>
          <kbd class="px-1.5 py-0.5 text-[10px] font-medium text-slate-500 bg-white border border-slate-200 rounded">K</kbd>
        </div>
      </div>

      <div class="h-5 w-px bg-slate-200 hidden md:block"></div>

      <!-- Utilities -->
      <div class="flex items-center gap-2">
        <button class="text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors relative outline-none rounded-md p-1.5 focus:ring-2 focus:ring-admin-primary">
          <IconBell size="20" />
          <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-admin-primary rounded-full border-2 border-white"></span>
        </button>

        <button class="text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors outline-none rounded-md p-1.5 focus:ring-2 focus:ring-admin-primary">
          <IconMoon size="20" />
        </button>
      </div>

      <!-- Profile Dropdown Trigger -->
      <div class="flex items-center gap-2 pl-2 border-l border-slate-200 cursor-pointer group">
        <div class="w-8 h-8 rounded-full bg-admin-slate-800 overflow-hidden ring-2 ring-transparent group-hover:ring-admin-primary/20 transition-all">
          <img src="https://ui-avatars.com/api/?name=Thanh&background=0F172A&color=fff" alt="User" />
        </div>
      </div>
    </div>
  </header>
</template>
