<script setup lang="ts">
import type { Component } from 'vue';

defineProps<{
  title: string;
  description: string;
  icon: Component;
}>();
</script>

<template>
  <div class="flex flex-col items-center justify-center py-24 px-6 text-center h-full">
    <div class="w-16 h-16 rounded-2xl bg-theme-bg flex items-center justify-center text-slate-300 border border-slate-100 shadow-sm mb-5">
      <component :is="icon" size="32" stroke="1.5" />
    </div>
    <h3 class="text-base font-semibold text-theme-text mb-1.5">{{ title }}</h3>
    <p class="text-sm text-theme-text-sec max-w-sm mb-6">{{ description }}</p>
    <slot name="action"></slot>
  </div>
</template>
