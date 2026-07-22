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
 <label v-if="label" class="mb-1.5 text-sm font-medium text-[var(--color-label)]">
 {{ label }} <span v-if="required" class="text-theme-danger font-bold">*</span>
 </label>
 
 <div class="relative">
 <div v-if="icon" class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-theme-text-sec">
 <component :is="icon" size="18" />
 </div>
 
 <input
 :type="inputType"
 :value="modelValue"
 :placeholder="placeholder"
 :disabled="disabled"
 @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
 class="w-full min-h-[44px] border px-4 text-base transition-all duration-200 ease-in-out focus:outline-none focus:ring-1 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm"
 style="border-radius: var(--admin-radius-sm, 8px); background-color: var(--color-input-bg); color: var(--color-input-text);"
 :class="[
 icon ? 'pl-11' : '',
 type === 'password' ? 'pr-11' : '',
 error 
 ? 'border-theme-danger focus:border-theme-danger focus:ring-theme-danger bg-theme-danger/10' 
 : 'border-[var(--color-input-border)] hover:border-[var(--color-input-border-hover)] focus:border-[var(--color-input-border-focus)] focus:ring-[var(--color-input-border-focus)]'
 ]"
 />
 
 <button 
 v-if="type === 'password'" 
 type="button"
 @click="showPassword = !showPassword"
 class="absolute inset-y-0 right-0 pr-4 flex items-center text-theme-text-sec hover:text-white transition-colors focus:outline-none cursor-pointer"
 >
 <IconEyeOff v-if="showPassword" size="18" />
 <IconEye v-else size="18" />
 </button>
 </div>
 
 <p v-if="error" class="mt-1.5 text-xs text-theme-danger font-medium">{{ error }}</p>
 </div>
</template>
