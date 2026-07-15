<script setup lang="ts">
import { IconTrendingUp, IconTrendingDown } from '@tabler/icons-vue';
import type { Component } from 'vue';

const props = defineProps<{
  title: string;
  value: string | number;
  trend?: string; // e.g. "+12%" or "-5%"
  trendUp?: boolean;
  icon: Component;
  iconClass?: string; // Custom gradient or color for icon
}>();
</script>

<template>
  <div class="bg-white rounded-[var(--radius-admin-card)] p-[var(--spacing-admin-card-padding,20px)] border border-slate-200 admin-card-hover cursor-pointer relative overflow-hidden group">
    
    <div class="flex items-start justify-between">
      <div>
        <h3 class="text-sm font-medium text-slate-500 mb-1">{{ title }}</h3>
        <p class="text-[32px] font-bold text-slate-900 tracking-tight leading-none">{{ value }}</p>
      </div>
      
      <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-slate-50 group-hover:bg-slate-100 transition-colors" :class="iconClass">
        <component :is="icon" size="24" />
      </div>
    </div>

    <!-- Trend Indicator -->
    <div v-if="trend" class="mt-4 flex items-center gap-1.5 text-sm font-medium" :class="trendUp ? 'text-emerald-600' : 'text-rose-600'">
      <IconTrendingUp v-if="trendUp" size="16" />
      <IconTrendingDown v-else size="16" />
      <span>{{ trend }}</span>
      <span class="text-slate-400 font-normal ml-1">vs last month</span>
    </div>
    
  </div>
</template>
