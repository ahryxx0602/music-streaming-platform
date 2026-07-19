<script setup lang="ts">
import { useThemeStore } from '@/stores/themeStore';
import { IconSun, IconMoon } from '@tabler/icons-vue';

const themeStore = useThemeStore();
</script>

<template>
  <button 
    @click="themeStore.toggleTheme()" 
    class="p-2 rounded-full transition-colors duration-200"
    :class="themeStore.isDark ? 'hover:bg-slate-800 text-yellow-400' : 'hover:bg-slate-200 text-slate-700'"
    title="Toggle Theme"
  >
    <IconSun v-if="!themeStore.isDark" size="20" stroke="2" />
    <IconMoon v-else size="20" stroke="2" />
  </button>
</template>
