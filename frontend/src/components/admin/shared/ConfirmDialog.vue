<script setup lang="ts">
import { IconAlertTriangle, IconInfoCircle } from '@tabler/icons-vue';

const props = defineProps<{
  modelValue: boolean;
  title: string;
  message: string;
  confirmText?: string;
  cancelText?: string;
  type?: 'danger' | 'warning' | 'info';
  loading?: boolean;
}>();

defineEmits<{
  (e: 'update:modelValue', value: boolean): void;
  (e: 'confirm'): void;
}>();
</script>

<template>
  <Teleport to="body">
    <transition name="dialog-fade">
      <div v-if="modelValue" class="fixed inset-0 z-[110] flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="!loading && $emit('update:modelValue', false)"></div>
        
        <!-- Dialog -->
        <transition name="dialog-zoom" appear>
          <div v-if="modelValue" class="relative bg-theme-surface rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden z-10 border border-theme-border">
            <!-- Header Icon -->
            <div class="p-6 text-center">
              <div 
                class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4"
                :class="{
                  'bg-rose-100 dark:bg-rose-500/15 text-rose-600 dark:text-rose-400': type === 'danger',
                  'bg-amber-100 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400': type === 'warning',
                  'bg-blue-100 dark:bg-blue-500/15 text-blue-600 dark:text-blue-400': type === 'info' || !type
                }"
              >
                <IconAlertTriangle v-if="type === 'danger' || type === 'warning'" size="32" stroke-width="1.5" />
                <IconInfoCircle v-else size="32" stroke-width="1.5" />
              </div>
              <h3 class="text-xl font-bold text-theme-text mb-2">{{ title }}</h3>
              <p class="text-theme-text-sec text-sm leading-relaxed">{{ message }}</p>
            </div>
            
            <!-- Actions -->
            <div class="p-6 pt-0 flex gap-3">
              <button 
                type="button" 
                @click="$emit('update:modelValue', false)"
                :disabled="loading"
                class="flex-1 h-10 rounded-lg border border-slate-300 font-semibold text-theme-text-sec hover:bg-theme-bg transition-colors disabled:opacity-50"
              >
                {{ cancelText || $t('common.cancel') }}
              </button>
              <button 
                type="button" 
                @click="$emit('confirm')"
                :disabled="loading"
                class="flex-1 h-10 rounded-lg font-semibold text-white transition-colors disabled:opacity-70 flex justify-center items-center gap-2"
                :class="{
                  'bg-rose-600 hover:bg-rose-700': type === 'danger',
                  'bg-amber-600 hover:bg-amber-700': type === 'warning',
                  'bg-blue-600 hover:bg-blue-700': type === 'info' || !type
                }"
              >
                <svg v-if="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ confirmText || $t('common.confirm') }}
              </button>
            </div>
          </div>
        </transition>
      </div>
    </transition>
  </Teleport>
</template>

<style scoped>
.dialog-fade-enter-active,
.dialog-fade-leave-active {
  transition: opacity 0.2s ease;
}
.dialog-fade-enter-from,
.dialog-fade-leave-to {
  opacity: 0;
}

.dialog-zoom-enter-active {
  transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.dialog-zoom-leave-active {
  transition: transform 0.2s ease;
}
.dialog-zoom-enter-from,
.dialog-zoom-leave-to {
  transform: scale(0.9);
}
</style>
