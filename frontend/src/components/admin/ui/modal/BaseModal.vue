<script setup lang="ts">
import { computed, watch, onMounted, onUnmounted } from 'vue';
import { IconX } from '@tabler/icons-vue';

const props = defineProps<{
 modelValue: boolean;
 title?: string;
 size?: 'sm' | 'md' | 'lg' | 'xl';
}>();

const emit = defineEmits(['update:modelValue', 'close']);

const sizeClass = computed(() => {
 switch (props.size) {
 case 'sm': return 'max-w-md';
 case 'lg': return 'max-w-4xl';
 case 'xl': return 'max-w-6xl';
 case 'md':
 default:
 return 'max-w-2xl';
 }
});

const close = () => {
 emit('update:modelValue', false);
 emit('close');
};

// Đóng modal khi nhấn phím Esc
const handleEscape = (e: KeyboardEvent) => {
 if (e.key === 'Escape' && props.modelValue) {
 close();
 }
};

// Vô hiệu hóa scroll của body khi modal mở
watch(() => props.modelValue, (isOpen) => {
 if (isOpen) {
 document.body.style.overflow = 'hidden';
 } else {
 document.body.style.overflow = '';
 }
});

onMounted(() => {
 document.addEventListener('keydown', handleEscape);
});

onUnmounted(() => {
 document.removeEventListener('keydown', handleEscape);
 document.body.style.overflow = '';
});
</script>

<template>
 <Teleport to="body">
 <Transition name="modal-fade">
 <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center">
 <!-- Backdrop -->
 <div 
 class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
 @click="close"
 ></div>

 <!-- Modal Panel -->
 <div 
 class="relative bg-theme-surface rounded-2xl shadow-2xl flex flex-col w-full mx-4 max-h-[90vh] transition-all transform overflow-hidden border border-theme-border"
 :class="sizeClass"
 @click.stop
 >
 <!-- Header -->
 <div class="flex items-center justify-between px-6 py-4 border-b border-theme-border shrink-0 bg-theme-surface-hover/50">
 <div class="flex items-center gap-3">
 <slot name="header-icon"></slot>
 <h3 class="text-lg font-bold text-theme-text" v-if="title">{{ title }}</h3>
 </div>
 <button 
 @click="close"
 class="w-8 h-8 rounded-full flex items-center justify-center text-theme-text-sec hover:text-theme-text hover:bg-theme-bg transition-colors cursor-pointer"
 >
 <IconX size="20" />
 </button>
 </div>

 <!-- Body -->
 <div class="p-6 overflow-y-auto custom-scrollbar flex-1">
 <slot></slot>
 </div>
 
 <!-- Optional Footer Slot -->
 <div v-if="$slots.footer" class="px-6 py-4 border-t border-theme-border bg-theme-bg shrink-0">
 <slot name="footer"></slot>
 </div>
 </div>
 </div>
 </Transition>
 </Teleport>
</template>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
 transition: all 0.25s ease-out;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
 opacity: 0;
}

.modal-fade-enter-from .relative,
.modal-fade-leave-to .relative {
 transform: scale(0.95) translateY(10px);
}

.custom-scrollbar::-webkit-scrollbar {
 width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
 background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
 background-color: #cbd5e1;
 border-radius: 10px;
}
.custom-scrollbar:hover::-webkit-scrollbar-thumb {
 background-color: #94a3b8;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
 background-color: #334155;
}
.dark .custom-scrollbar:hover::-webkit-scrollbar-thumb {
 background-color: #475569;
}
</style>
