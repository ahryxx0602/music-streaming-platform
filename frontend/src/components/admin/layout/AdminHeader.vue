<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { IconSearch, IconBell, IconChevronRight } from '@tabler/icons-vue';
import LanguageSwitcher from '@/components/base/LanguageSwitcher.vue';
import ThemeToggle from '@/components/base/ThemeToggle.vue';

const route = useRoute();
const pathSegments = computed(() => {
 const path = route.path.replace('/admin', '');
 if (!path || path === '/') return ['admin.menu.dashboard', 'admin.menu.overview'];
 const segments = path.split('/').filter(p => p);
 return ['admin.menu.dashboard', ...segments.map(s => 'admin.menu.' + s.toLowerCase())];
});
</script>

<template>
 <!-- Header cao 72px -->
 <header class="h-[72px] bg-theme-surface border-b border-theme-border flex items-center justify-between px-8 flex-shrink-0 z-10 shadow-sm transition-all duration-200">
 
 <!-- Left: Breadcrumbs hiện đại (Linear Style) -->
 <div class="flex items-center text-sm font-medium text-theme-text-sec">
 <template v-for="(segment, index) in pathSegments" :key="index">
 <span 
 class="cursor-pointer transition-colors"
 :class="index === pathSegments.length - 1 ? 'text-theme-text font-semibold' : 'hover:text-theme-text'"
 >
 {{ $t(segment) }}
 </span>
 <IconChevronRight v-if="index < pathSegments.length - 1" size="14" class="mx-2 text-theme-text-sec/50" />
 </template>
 </div>

 <!-- Right: Command Search, Notifications, Theme, Profile -->
 <div class="flex items-center gap-5">
 <!-- Command Search -->
 <div class="relative hidden md:block group">
 <IconSearch size="14" class="absolute left-3 top-1/2 -translate-y-1/2 text-theme-text-sec group-focus-within:text-theme-primary transition-colors" />
 <input 
 type="text" 
 :placeholder="$t('admin.header.search')" 
 class="h-9 pl-9 pr-12 text-sm border border-theme-border rounded-lg bg-theme-bg focus:outline-none focus:ring-1 focus:ring-theme-primary focus:border-theme-primary focus:bg-theme-surface w-64 transition-all text-theme-text"
 />
 <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1">
 <kbd class="px-1.5 py-0.5 text-[10px] font-medium text-theme-text-sec bg-theme-surface border border-theme-border rounded">⌘</kbd>
 <kbd class="px-1.5 py-0.5 text-[10px] font-medium text-theme-text-sec bg-theme-surface border border-theme-border rounded">K</kbd>
 </div>
 </div>

 <div class="h-5 w-px bg-theme-border hidden md:block"></div>

 <!-- Utilities -->
 <div class="flex items-center gap-2">
 <LanguageSwitcher />

 <button class="text-theme-text-sec hover:text-theme-text hover:bg-theme-surface-hover transition-colors relative outline-none rounded-md p-1.5 focus:ring-2 focus:ring-theme-primary cursor-pointer">
 <IconBell size="20" />
 <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-theme-danger rounded-full border-2 border-theme-surface"></span>
 </button>

 <ThemeToggle />
 </div>

 <!-- Profile Dropdown Trigger -->
 <div class="flex items-center gap-2 pl-2 border-l border-theme-border cursor-pointer group">
 <div class="w-8 h-8 rounded-full bg-theme-surface-hover overflow-hidden ring-2 ring-transparent group-hover:ring-theme-primary/20 transition-all">
 <img src="https://ui-avatars.com/api/?name=Thanh&background=6366F1&color=fff" alt="User" />
 </div>
 </div>
 </div>
 </header>
</template>
