<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import AuthLayout from './layouts/AuthLayout.vue';

const route = useRoute();

// Xử lý Dynamic Layout
const layoutComponent = computed(() => {
  if (route.meta.layout === 'AuthLayout') {
    return AuthLayout;
  }
  // Mặc định bọc qua thẻ div nếu không có layout (chờ các module sau setup MainLayout)
  return 'div'; 
});
</script>

<template>
  <component :is="layoutComponent">
    <RouterView />
  </component>
</template>

<style>
/* App global overrides */
</style>
