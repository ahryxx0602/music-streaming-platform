<script setup lang="ts">
defineProps<{
  type?: 'button' | 'submit' | 'reset';
  variant?: 'primary' | 'secondary' | 'outline';
  disabled?: boolean;
  loading?: boolean;
}>();
</script>

<template>
  <button 
    :type="type || 'button'" 
    :disabled="disabled || loading"
    :class="['base-button', variant || 'primary', { loading }]"
  >
    <span v-if="loading" class="spinner"></span>
    <slot v-else />
  </button>
</template>

<style scoped>
.base-button {
  width: 100%;
  padding: 0.875rem 1.5rem;
  border-radius: var(--radius-md);
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  border: none;
  outline: none;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.base-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.primary {
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
  color: white;
  box-shadow: 0 4px 14px 0 rgba(139, 92, 246, 0.39);
}

.primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(139, 92, 246, 0.5);
}

.primary:active:not(:disabled) {
  transform: translateY(1px);
}

.secondary {
  background: var(--color-glass-border);
  color: white;
  border: 1px solid var(--color-glass-border);
}

.secondary:hover:not(:disabled) {
  background: rgba(255, 255, 255, 0.15);
}

.outline {
  background: transparent;
  color: var(--color-primary);
  border: 1px solid var(--color-primary);
}

.outline:hover:not(:disabled) {
  background: rgba(139, 92, 246, 0.1);
}

.spinner {
  width: 1.25rem;
  height: 1.25rem;
  border: 2px solid rgba(255,255,255,0.3);
  border-radius: 50%;
  border-top-color: white;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
