<script setup lang="ts">
const props = defineProps<{
  status: string;
}>();

const getBadgeClasses = () => {
  switch (props.status?.toLowerCase()) {
    case 'active': return 'bg-emerald-50 dark:bg-emerald-500/15 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20';
    case 'suspended': return 'bg-amber-50 dark:bg-amber-500/15 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400 dark:text-amber-400 border-amber-200 dark:border-amber-500/20';
    case 'banned': return 'bg-rose-50 dark:bg-rose-500/15 dark:bg-rose-500/15 text-rose-700 dark:text-rose-400 dark:text-rose-400 border-rose-200 dark:border-rose-500/20';
    case 'pending': return 'bg-blue-50 dark:bg-blue-500/15 dark:bg-blue-500/15 text-blue-700 dark:text-blue-400 dark:text-blue-400 border-blue-200 dark:border-blue-500/20';
    default: return 'bg-theme-bg text-theme-text border-theme-border';
  }
};
</script>

<template>
  <span :class="['inline-flex items-center justify-center px-2.5 py-1 text-xs font-bold rounded-lg border', getBadgeClasses()]">
    <slot>{{ status }}</slot>
  </span>
</template>
