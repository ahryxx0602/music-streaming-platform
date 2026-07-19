<script setup lang="ts">
import type { Component } from 'vue';

const props = defineProps<{
  type?: 'button' | 'submit' | 'reset';
  variant?: 'primary' | 'secondary' | 'danger' | 'ghost';
  size?: 'sm' | 'md' | 'lg';
  loading?: boolean;
  disabled?: boolean;
  icon?: Component;
  iconPos?: 'left' | 'right';
}>();

const emit = defineEmits(['click']);
</script>

<template>
  <button
    :type="type || 'button'"
    :disabled="disabled || loading"
    @click="$emit('click', $event)"
    class="inline-flex items-center justify-center font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm active:scale-95"
    style="border-radius: var(--radius-admin-btn);"
    :class="[
      // Size definitions
      size === 'sm' ? 'px-3 py-1.5 text-xs' :
      size === 'lg' ? 'px-5 py-2.5 text-base' :
      'px-4 py-2 text-sm',
      
      // Variant definitions
      variant === 'secondary' ? 'bg-theme-surface border border-slate-300 text-theme-text hover:bg-theme-bg focus:ring-slate-200' :
      variant === 'danger' ? 'bg-rose-600 text-white hover:bg-rose-700 focus:ring-rose-500/30 border border-transparent' :
      variant === 'ghost' ? 'bg-transparent text-theme-text-sec hover:bg-theme-surface-hover hover:text-theme-text focus:ring-slate-200 shadow-none' :
      'bg-admin-primary text-white hover:bg-admin-primary-hover focus:ring-admin-primary/30 border border-transparent' // primary (Aurora Blue)
    ]"
  >
    <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    <component v-else-if="icon && iconPos !== 'right'" :is="icon" size="16" class="mr-2 -ml-1" />
    
    <slot />
    
    <component v-if="icon && iconPos === 'right' && !loading" :is="icon" size="16" class="ml-2 -mr-1" />
  </button>
</template>
