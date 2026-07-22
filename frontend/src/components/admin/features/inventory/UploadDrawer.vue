<script setup lang="ts">
import BaseDrawer from '@/components/admin/ui/drawer/BaseDrawer.vue';
import AdminUploadForm from './AdminUploadForm.vue';
import { IconUpload } from '@tabler/icons-vue';

const props = defineProps<{
 isOpen: boolean;
 fixedArtistId?: number;
 fixedArtistName?: string;
}>();

const emit = defineEmits(['update:isOpen', 'success']);

const handleSuccess = () => {
 // Bắn sự kiện lên trên để reload danh sách (mặc dù form upload đã gọi fetchSongs)
 emit('success');
 emit('update:isOpen', false);
};
</script>

<template>
 <BaseDrawer
 :modelValue="isOpen"
 @update:modelValue="emit('update:isOpen', $event)"
 title="Tải Nhạc Lên Kho"
 size="md"
 >
 <template #header-icon>
 <div class="w-8 h-8 rounded-lg bg-theme-primary/10 text-theme-primary flex items-center justify-center border border-theme-primary/20">
 <IconUpload size="18" />
 </div>
 </template>

 <div class="mb-5 border-b border-theme-border pb-5">
 <h3 class="text-xs font-bold text-theme-text-sec uppercase tracking-wider mb-1">Thông tin bài hát</h3>
 <p class="text-[13px] text-theme-text-sec mb-4">Các tệp tải lên qua màn hình này sẽ tự động được phê duyệt và lưu vào kho âm thanh.</p>
 
 <AdminUploadForm 
 :fixed-artist-id="fixedArtistId"
 :fixed-artist-name="fixedArtistName"
 @success="handleSuccess"
 @cancel="emit('update:isOpen', false)"
 />
 </div>
 </BaseDrawer>
</template>
