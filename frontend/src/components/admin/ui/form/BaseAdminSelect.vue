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
 <label v-if="label" class="mb-1.5 text-sm font-medium text-[var(--color-label)]">
 {{ label }} <span v-if="required" class="text-theme-danger font-bold">*</span>
 </label>
 
 <div class="relative">
 <select
 :value="modelValue"
 :required="required"
 :disabled="disabled"
 @change="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
 class="w-full min-h-[44px] border px-4 text-base transition-all duration-200 ease-in-out focus:outline-none focus:ring-1 disabled:opacity-50 disabled:cursor-not-allowed appearance-none shadow-sm cursor-pointer"
 style="border-radius: var(--admin-radius-sm, 8px); background-color: var(--color-input-bg); color: var(--color-input-text);"
 :class="[
 error 
 ? 'border-theme-danger focus:border-theme-danger focus:ring-theme-danger bg-theme-danger/10' 
 : 'border-[var(--color-input-border)] hover:border-[var(--color-input-border-hover)] focus:border-[var(--color-input-border-focus)] focus:ring-[var(--color-input-border-focus)]'
 ]"
 >
 <option value="" disabled selected hidden>Chọn một mục...</option>
 <option v-for="opt in options" :key="opt.value" :value="opt.value">
 {{ opt.label }}
 </option>
 </select>
 

 
 <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-theme-text-sec">
 <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
 </div>
 </div>
 
 <p v-if="error" class="mt-1.5 text-xs text-theme-danger font-medium">{{ error }}</p>
 </div>
</template>
