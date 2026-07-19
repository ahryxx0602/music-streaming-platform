<script setup lang="ts">
defineProps<{
  modelValue: string;
  label?: string;
  options: { label: string; value: string }[];
  required?: boolean;
  disabled?: boolean;
  error?: string;
}>();

defineEmits<{
  (e: 'update:modelValue', value: string): void;
}>();
</script>

<template>
  <div class="flex flex-col gap-1.5 mb-4">
    <label v-if="label" class="text-sm font-semibold text-theme-text">
      {{ label }} <span v-if="required" class="text-rose-500">*</span>
    </label>
    
    <div class="relative">
      <select
        :value="modelValue"
        :required="required"
        :disabled="disabled"
        @change="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
        class="w-full h-10 pl-3 pr-10 bg-theme-surface border border-slate-300 rounded-lg text-sm text-theme-text focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all appearance-none cursor-pointer disabled:bg-theme-bg disabled:cursor-not-allowed"
        :class="{
          'border-rose-500 focus:border-rose-500 focus:ring-rose-500/20': error
        }"
      >
        <option value="" disabled selected hidden>Chọn một mục...</option>
        <option v-for="opt in options" :key="opt.value" :value="opt.value">
          {{ opt.label }}
        </option>
      </select>
      
      <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-theme-text-sec">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
      </div>
    </div>
    
    <span v-if="error" class="text-xs font-medium text-rose-500">{{ error }}</span>
  </div>
</template>
