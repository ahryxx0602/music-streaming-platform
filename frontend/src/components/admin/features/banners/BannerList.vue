<script setup lang="ts">
import { ref } from 'vue';
import { useBannerStore } from '@/stores/bannerStore';
import { IconGripVertical, IconEdit, IconTrash, IconPhoto } from '@tabler/icons-vue';

const bannerStore = useBannerStore();
const emit = defineEmits(['edit']);

const draggedIndex = ref<number | null>(null);

const onDragStart = (index: number, event: DragEvent) => {
 draggedIndex.value = index;
 if (event.dataTransfer) {
 event.dataTransfer.effectAllowed = 'move';
 event.dataTransfer.setData('text/plain', index.toString());
 }
};

const onDragOver = (index: number, event: DragEvent) => {
 event.preventDefault();
};

const onDrop = async (index: number, event: DragEvent) => {
 event.preventDefault();
 if (draggedIndex.value !== null && draggedIndex.value !== index) {
 // Hoán vị mảng
 const item = bannerStore.banners.splice(draggedIndex.value, 1)[0];
 bannerStore.banners.splice(index, 0, item);
 
 // Bắn API reorder
 const orderedIds = bannerStore.banners.map(b => b.id);
 try {
 await bannerStore.reorderBanners(orderedIds);
 } catch (e) {
 // Reload lại nếu lỗi
 bannerStore.fetchBanners();
 }
 }
 draggedIndex.value = null;
};

const onDragEnd = () => {
 draggedIndex.value = null;
};

const toggleStatus = async (banner: any) => {
 const newStatus = !banner.is_active;
 try {
 await bannerStore.toggleStatus(banner.id, newStatus);
 } catch (e) {
 // Lỗi store đã bắt
 }
};

const handleDelete = async (banner: any) => {
 if (confirm(`Bạn có chắc chắn muốn xóa banner "${banner.title}"?`)) {
 await bannerStore.deleteBanner(banner.id);
 }
};
</script>

<template>
 <div class="space-y-4">
 <div v-if="bannerStore.isLoading && bannerStore.banners.length === 0" class="text-center py-12 text-theme-text-sec">
 Đang tải dữ liệu...
 </div>
 
 <div v-else-if="bannerStore.banners.length === 0" class="text-center py-20 bg-theme-surface border border-theme-border rounded-xl">
 <div class="w-16 h-16 bg-theme-surface-hover text-theme-text-sec rounded-full flex items-center justify-center mx-auto mb-3">
 <IconPhoto size="32" />
 </div>
 <h3 class="text-sm font-bold text-theme-text-sec mb-1">Chưa có Banner nào</h3>
 <p class="text-xs text-theme-text-sec">Hãy tải lên một số banner để hiển thị trên trang chủ.</p>
 </div>

 <!-- Draggable List -->
 <div 
 v-else
 v-for="(banner, idx) in bannerStore.banners" 
 :key="banner.id"
 draggable="true"
 @dragstart="onDragStart(idx, $event)"
 @dragover="onDragOver(idx, $event)"
 @drop="onDrop(idx, $event)"
 @dragend="onDragEnd"
 class="bg-theme-surface border border-theme-border rounded-xl p-4 flex flex-col md:flex-row gap-4 items-center shadow-[var(--shadow-glow)] hover:shadow-[var(--shadow-glow)] hover:border-theme-primary/30 transition-all cursor-grab active:cursor-grabbing"
 :class="{ 'opacity-50 border-dashed': draggedIndex === idx, 'opacity-70 grayscale': !banner.is_active }"
 >
 <!-- Grip Icon -->
 <div class="text-theme-text-sec hover:text-theme-text-sec px-2 py-4 cursor-grab">
 <IconGripVertical size="24" />
 </div>

 <!-- Image -->
 <div class="w-full md:w-64 h-32 bg-theme-surface-hover rounded-lg overflow-hidden shrink-0 border border-theme-border relative">
 <img v-if="banner.image_url" :src="banner.image_url" class="w-full h-full object-cover" />
 <div v-else class="w-full h-full flex flex-col items-center justify-center text-theme-text-sec">
 <IconPhoto size="24" class="mb-1" />
 <span class="text-xs">No Image</span>
 </div>
 
 <!-- Status Overlay -->
 <div v-if="!banner.is_active" class="absolute inset-0 bg-theme-bg/60 flex items-center justify-center">
 <span class="px-3 py-1 bg-black/60 text-white text-xs font-bold rounded-full">Đã ẩn</span>
 </div>
 </div>

 <!-- Content -->
 <div class="flex-1 min-w-0 w-full text-center md:text-left">
 <h3 class="text-base font-bold text-theme-text mb-1">{{ banner.title }}</h3>
 <p v-if="banner.subtitle" class="text-sm text-theme-text-sec mb-2 truncate">{{ banner.subtitle }}</p>
 <a v-if="banner.target_url" :href="banner.target_url" target="_blank" class="text-xs text-theme-primary hover:underline truncate inline-block max-w-full">
 {{ banner.target_url }}
 </a>
 </div>

 <!-- Actions -->
 <div class="flex items-center gap-6 shrink-0 w-full md:w-auto justify-between md:justify-end border-t md:border-t-0 border-theme-border pt-4 md:pt-0">
 
 <!-- Toggle Switch -->
 <div class="flex items-center gap-2">
 <span class="text-xs font-medium" :class="banner.is_active ? 'text-theme-success' : 'text-theme-text-sec'">
 {{ banner.is_active ? $t('admin.banners.status_active') : $t('admin.banners.status_inactive') }}
 </span>
 <button 
 type="button" 
 class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
 :class="banner.is_active ? 'bg-theme-success' : 'bg-theme-border'"
 @click.stop="toggleStatus(banner)"
 >
 <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-theme-surface shadow ring-0 transition duration-200 ease-in-out" :class="banner.is_active ? 'translate-x-5' : 'translate-x-0'"></span>
 </button>
 </div>

 <div class="flex gap-2">
 <button @click.stop="emit('edit', banner)" class="p-2 text-theme-text-sec hover:text-theme-primary bg-theme-surface-hover hover:bg-theme-primary/10 rounded-lg transition-colors">
 <IconEdit size="20" />
 </button>
 <button @click.stop="handleDelete(banner)" class="p-2 text-theme-text-sec hover:text-theme-danger bg-theme-surface-hover hover:bg-theme-danger/10 rounded-lg transition-colors">
 <IconTrash size="20" />
 </button>
 </div>
 </div>
 </div>
 </div>
</template>
