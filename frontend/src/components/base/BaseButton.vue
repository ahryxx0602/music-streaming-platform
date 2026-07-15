<script setup lang="ts">
import type { Component } from 'vue';

defineProps<{
  type?: 'button' | 'submit' | 'reset';
  variant?: 'primary' | 'secondary' | 'outline';
  disabled?: boolean;
  loading?: boolean;
  icon?: Component;
  iconPosition?: 'left' | 'right';
}>();
</script>

<template>
  <button 
    :type="type || 'button'" 
    :disabled="disabled || loading"
    :class="['base-button', variant || 'primary', { loading }]"
  >
    <span v-if="loading" class="spinner"></span>
    <template v-else>
      <component v-if="icon && (!iconPosition || iconPosition === 'left')" :is="icon" class="btn-icon left" size="20" stroke-width="2" />
      <slot />
      <component v-if="icon && iconPosition === 'right'" :is="icon" class="btn-icon right" size="20" stroke-width="2" />
    </template>
  </button>
</template>

<style scoped>
.base-button {
  width: 100%;
  padding: 0.875rem 1.5rem;
  border-radius: var(--radius-md, 12px);
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  border: none;
  outline: none;
  transition: var(--transition-smooth);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

.base-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-icon.left { margin-right: 0.5rem; }
.btn-icon.right { margin-left: 0.5rem; }

.primary {
  /* Using Deep Blue (#2563EB), Primary (#3B82F6), Accent (#38BDF8) */
  background: linear-gradient(135deg, #2563EB 0%, var(--color-primary) 50%, var(--color-accent) 100%);
  color: var(--color-text-primary);
  box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.2);
  z-index: 1;
}

.primary::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: linear-gradient(135deg, var(--color-accent) 0%, var(--color-primary) 50%, #2563EB 100%);
  opacity: 0;
  transition: var(--transition-smooth);
  z-index: -1;
}

.primary:hover:not(:disabled)::before {
  opacity: 1;
}

.primary:hover:not(:disabled) {
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 0 24px rgba(59, 130, 246, 0.25);
}

.primary:active:not(:disabled) {
  transform: translateY(1px);
}

.secondary {
  background: transparent;
  color: var(--color-text-primary);
  border: 1px solid var(--color-input-border);
}

.secondary:hover:not(:disabled) {
  background: rgba(255, 255, 255, 0.05);
}

.spinner {
  width: 1.25rem; height: 1.25rem;
  border: 2px solid rgba(255,255,255,0.3);
  border-radius: 50%;
  border-top-color: var(--color-text-primary);
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
