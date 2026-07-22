<script setup lang="ts">
const props = defineProps<{
 status: string;
}>();

const getBadgeClasses = () => {
 switch (props.status?.toLowerCase()) {
 case 'active': return 'bg-theme-success/15 text-theme-success border-theme-success/20';
 case 'suspended': return 'bg-theme-warning/15 text-theme-warning border-theme-warning/20';
 case 'banned': return 'bg-theme-danger/15 text-theme-danger border-theme-danger/20';
 case 'pending': return 'bg-theme-info/15 text-theme-info border-theme-info/20';
 default: return 'bg-theme-bg text-theme-text border-theme-border';
 }
};
</script>

<template>
 <span :class="['inline-flex items-center justify-center px-2.5 py-1 text-xs font-bold rounded-lg border', getBadgeClasses()]">
 <slot>{{ status }}</slot>
 </span>
</template>
