<script setup lang="ts">
import { ref, computed } from 'vue';
import type { Component } from 'vue';
import { IconEye, IconEyeOff } from '@tabler/icons-vue';

const props = defineProps<{
  modelValue: string | number;
  label?: string;
  type?: string;
  placeholder?: string;
  required?: boolean;
  disabled?: boolean;
  error?: string;
  icon?: Component;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void;
}>();

const showPassword = ref(false);

const inputType = computed(() => {
  if (props.type === 'password') {
    return showPassword.value ? 'text' : 'password';
  }
  return props.type || 'text';
});
</script>

<template>
  <div class="flex flex-col mb-4">
    <label v-if="label" class="mb-1.5 text-sm font-medium text-theme-text">
      {{ label }} <span v-if="required" class="text-rose-500 font-bold">*</span>
    </label>
    
    <div class="relative">
      <div v-if="icon" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-theme-text-sec">
        <component :is="icon" size="18" />
      </div>
      
      <input
        :type="inputType"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
        class="w-full h-9 border border-slate-300 px-3 text-sm transition-all focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 disabled:bg-theme-bg disabled:text-theme-text-sec disabled:cursor-not-allowed text-theme-text placeholder-slate-400 shadow-sm"
        style="border-radius: var(--admin-radius-sm, 6px);"
        :class="[
          icon ? 'pl-9' : '',
          type === 'password' ? 'pr-9' : '',
          error 
            ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50/30' 
            : 'bg-theme-surface hover:border-slate-400'
        ]"
      />
      
      <button 
        v-if="type === 'password'" 
        type="button"
        @click="showPassword = !showPassword"
        class="absolute inset-y-0 right-0 pr-3 flex items-center text-theme-text-sec hover:text-theme-text-sec transition-colors focus:outline-none"
      >
        <IconEyeOff v-if="showPassword" size="18" />
        <IconEye v-else size="18" />
      </button>
    </div>
    
    <p v-if="error" class="mt-1.5 text-xs text-rose-500 font-medium">{{ error }}</p>
  </div>
</template>
