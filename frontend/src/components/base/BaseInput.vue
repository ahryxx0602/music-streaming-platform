<script setup lang="ts">
defineProps<{
  modelValue: string;
  label?: string;
  type?: string;
  placeholder?: string;
  required?: boolean;
}>();

defineEmits<{
  (e: 'update:modelValue', value: string): void;
}>();
</script>

<template>
  <div class="base-input-wrapper">
    <label v-if="label" class="base-label">{{ label }} <span v-if="required" class="required">*</span></label>
    <input
      :type="type || 'text'"
      :value="modelValue"
      :placeholder="placeholder"
      :required="required"
      @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
      class="base-input"
    />
  </div>
</template>

<style scoped>
.base-input-wrapper {
  margin-bottom: 1.25rem;
  display: flex;
  flex-direction: column;
}

.base-label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #cbd5e1;
  margin-bottom: 0.5rem;
}

.required {
  color: #f43f5e;
}

.base-input {
  width: 100%;
  background: var(--color-glass-input);
  border: 1px solid var(--color-glass-border);
  border-radius: var(--radius-md);
  padding: 0.875rem 1rem;
  color: #f8fafc;
  font-size: 1rem;
  outline: none;
  transition: all 0.2s ease;
  box-sizing: border-box;
}

.base-input::placeholder {
  color: #64748b;
}

.base-input:focus {
  border-color: var(--color-primary);
  background: var(--color-glass-input-focus);
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2);
}
</style>
