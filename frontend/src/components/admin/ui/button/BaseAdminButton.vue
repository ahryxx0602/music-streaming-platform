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
 class="inline-flex items-center justify-center font-bold tracking-wide rounded-full transition-all duration-300 ease-[cubic-bezier(0.25,1,0.5,1)] focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer hover:-translate-y-[2px] active:translate-y-[1px] min-h-[44px]"
 :class="[
 // Size definitions
 size === 'sm' ? 'px-5 text-xs' :
 size === 'lg' ? 'px-8 text-base' :
 'px-6 text-sm',
 
 // Variant definitions
 variant === 'secondary' ? 'bg-transparent border-2 border-theme-border text-theme-text hover:bg-theme-surface focus:ring-theme-border hover:shadow-md' :
 variant === 'danger' ? 'bg-theme-danger text-white hover:opacity-90 hover:shadow-[0_4px_20px_rgba(239,68,68,0.4)] focus:ring-theme-danger/30 border border-transparent' :
 variant === 'ghost' ? 'bg-transparent text-theme-text-sec hover:bg-theme-surface hover:text-theme-text focus:ring-theme-border shadow-none hover:shadow-sm' :
 'bg-theme-accent text-white hover:opacity-90 hover:shadow-[0_4px_20px_rgba(34,197,94,0.4)] dark:hover:shadow-[var(--shadow-glow)] focus:ring-theme-accent/30 border border-transparent' // primary (CTA Green/Cyan)
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
