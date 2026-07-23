<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useThemeStore } from '@/stores/themeStore';
import AuthLayout from './layouts/AuthLayout.vue';
import MainLayout from './layouts/MainLayout.vue';
import GlobalPlayer from './components/player/GlobalPlayer.vue';

const route = useRoute();
const themeStore = useThemeStore();

onMounted(() => {
  themeStore.initTheme();
});

// Xử lý Dynamic Layout
const layoutComponent = computed(() => {
  if (route.meta.layout === 'AuthLayout') {
    return AuthLayout;
  }
  // Admin tự xử lý layout độc lập qua Nested Routes (Vue Router)
  if (route.path.startsWith('/admin')) {
    return null;
  }
  return MainLayout; 
});
</script>

<template>
  <component v-if="layoutComponent" :is="layoutComponent">
    <RouterView />
  </component>
  <RouterView v-else />
  
  <!-- Render Global Player for Listeners & Artists -->
  <GlobalPlayer v-if="!route.path.startsWith('/admin') && route.meta.layout !== 'AuthLayout'" />
</template>

<style>
/* App global overrides */
</style>
