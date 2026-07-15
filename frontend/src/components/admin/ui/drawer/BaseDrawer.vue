<script setup lang="ts">
import { watch, onMounted, onUnmounted } from 'vue';
import { IconX } from '@tabler/icons-vue';

const props = defineProps<{
  modelValue: boolean;
  title?: string;
  size?: 'sm' | 'md' | 'lg' | 'xl';
}>();

const emit = defineEmits(['update:modelValue', 'close']);

const close = () => {
  emit('update:modelValue', false);
  emit('close');
};

const handleKeydown = (e: KeyboardEvent) => {
  if (e.key === 'Escape' && props.modelValue) {
    close();
  }
};

onMounted(() => {
  document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown);
});

// Chặn cuộn trang khi Drawer mở
watch(() => props.modelValue, (isOpen) => {
  if (isOpen) {
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = '';
  }
});
</script>

<template>
  <Teleport to="body">
    <transition name="fade">
      <!-- Backdrop Shadow (Tối nhẹ hơn, không bị blur) -->
      <div v-if="modelValue" @click="close" class="fixed inset-0 bg-slate-900/20 z-40 transition-opacity"></div>
    </transition>

    <transition name="slide-right">
      <!-- Drawer Panel Right -->
      <div v-if="modelValue" 
        class="fixed inset-y-0 right-0 z-50 flex flex-col bg-white shadow-xl h-full transform transition-transform duration-300 ease-in-out border-l border-slate-200"
        :class="[
          size === 'sm' ? 'w-full max-w-sm' :
          size === 'lg' ? 'w-full max-w-2xl' :
          size === 'xl' ? 'w-full max-w-4xl' :
          'w-full max-w-md' // md (default)
        ]"
      >
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-white shrink-0">
          <h2 class="text-base font-semibold text-slate-800 flex items-center gap-2">
            <slot name="header-icon"></slot>
            {{ title }}
          </h2>
          <button @click="close" class="text-slate-400 hover:text-slate-700 hover:bg-slate-100 p-1.5 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-slate-200">
            <IconX size="18" />
          </button>
        </div>

        <!-- Body Scrollable -->
        <div class="flex-1 overflow-y-auto p-6 bg-slate-50">
          <slot></slot>
        </div>

        <!-- Footer Fixed (Divider mỏng) -->
        <div v-if="$slots.footer" class="px-6 py-4 border-t border-slate-200 bg-white shrink-0">
          <slot name="footer"></slot>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.slide-right-enter-active, .slide-right-leave-active { transition: transform 0.3s cubic-bezier(0.2, 0.8, 0.2, 1); }
.slide-right-enter-from, .slide-right-leave-to { transform: translateX(100%); }
</style>
