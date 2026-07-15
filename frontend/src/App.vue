<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import AuthLayout from './layouts/AuthLayout.vue';
import MainLayout from './layouts/MainLayout.vue';

const route = useRoute();

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
</template>

<style>
/* App global overrides */
</style>
