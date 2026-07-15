<script setup lang="ts">
import { ref, computed } from 'vue';
import type { Component } from 'vue';
import { IconEye, IconEyeOff } from '@tabler/icons-vue';

const props = defineProps<{
  modelValue: string;
  label?: string;
  type?: string;
  placeholder?: string;
  required?: boolean;
  icon?: Component;
  error?: string | false | any;
}>();

defineEmits<{
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
  <div class="base-input-wrapper">
    <label v-if="label" class="base-label">{{ label }} <span v-if="required" class="required">*</span></label>
    <div class="input-container">
      <div v-if="icon" class="icon-wrapper" :class="{ 'icon-error': error }">
        <component :is="icon" size="20" stroke-width="1.5" />
      </div>
      <input
        :type="inputType"
        :value="modelValue"
        :placeholder="placeholder"
        :required="required"
        @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
        class="base-input"
        :class="{ 'has-icon': icon, 'input-error': error, 'has-toggle': type === 'password' }"
      />
      <div v-if="type === 'password'" class="toggle-wrapper" @click="showPassword = !showPassword">
        <IconEyeOff v-if="showPassword" size="20" stroke-width="1.5" />
        <IconEye v-else size="20" stroke-width="1.5" />
      </div>
    </div>
    <span v-if="typeof error === 'string' && error" class="error-message">{{ error }}</span>
  </div>
</template>

<style scoped>
.base-input-wrapper {
  margin-bottom: 1.25rem;
  display: flex; flex-direction: column;
}

.base-label {
  font-size: 0.875rem; font-weight: 500;
  color: var(--color-label, var(--color-text-secondary)); margin-bottom: 0.75rem; letter-spacing: 0.025em;
}

.required { color: var(--color-primary); opacity: 0.8; margin-left: 0.125rem; }

.input-container { position: relative; display: flex; align-items: center; }

.icon-wrapper {
  position: absolute; left: 1rem;
  color: var(--color-input-placeholder);
  display: flex; align-items: center; justify-content: center;
  pointer-events: none; transition: var(--transition-smooth);
}

.base-input {
  width: 100%; height: 48px;
  background: var(--color-input-bg);
  border: 1px solid var(--color-input-border);
  border-radius: var(--radius-md, 12px);
  padding: 0 1rem;
  color: var(--color-input-text); font-size: 1rem;
  outline: none; transition: var(--transition-smooth); box-sizing: border-box;
  caret-color: var(--color-primary);
}

.base-input.has-icon { padding-left: 2.75rem; }
.base-input.has-toggle { padding-right: 2.75rem; }

.toggle-wrapper {
  position: absolute; right: 1rem;
  color: var(--color-input-placeholder);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: var(--transition-smooth);
}
.toggle-wrapper:hover { color: var(--color-input-border-focus); }

.base-input::placeholder { color: var(--color-input-placeholder); }

.base-input:hover:not(.input-error) { 
  background: var(--color-input-bg-hover); 
  border-color: var(--color-input-border-hover); 
}

.base-input:focus:not(.input-error) {
  border-color: var(--color-input-border-focus);
  background: var(--color-input-bg-focus);
  box-shadow: var(--shadow-input-focus);
}

.input-container:focus-within .icon-wrapper:not(.icon-error) { color: var(--color-input-border-focus); }

/* Trạng thái lỗi (Task 8) */
.input-error {
  border-color: #EF4444 !important;
  animation: shake 150ms 2;
}
.icon-error { color: #EF4444 !important; }

@keyframes shake {
  0% { transform: translateX(0); }
  25% { transform: translateX(-4px); }
  50% { transform: translateX(0); }
  75% { transform: translateX(4px); }
  100% { transform: translateX(0); }
}

.error-message {
  color: #EF4444;
  font-size: 0.8rem;
  margin-top: 0.375rem;
  margin-left: 0.25rem;
  display: block;
}
</style>
