<script setup lang="ts">
import BaseDrawer from '@/components/admin/ui/drawer/BaseDrawer.vue';
import AdminAlbumForm from './AdminAlbumForm.vue';
import { IconDisc } from '@tabler/icons-vue';

const props = defineProps<{
  isOpen: boolean;
  albumData?: any; // Dùng cho Edit mode
}>();

const emit = defineEmits(['update:isOpen', 'success']);

const handleSuccess = () => {
  emit('success');
  emit('update:isOpen', false);
};
</script>

<template>
  <BaseDrawer
    :modelValue="isOpen"
    @update:modelValue="emit('update:isOpen', $event)"
    :title="albumData ? 'Sửa Album' : 'Tạo Album Mới'"
    size="lg"
  >
    <template #header-icon>
      <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100">
        <IconDisc size="18" />
      </div>
    </template>

    <div class="mb-5 border-b border-slate-200 pb-5">
      <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Cấu trúc Album</h3>
      <p class="text-[13px] text-slate-500 mb-4">Bạn có thể tạo Album rỗng trước, sau đó gán bài hát vào sau.</p>
      
      <!-- Chú ý: dùng :key để ép rerender form khi chuyển từ edit sang create -->
      <AdminAlbumForm 
        :key="albumData ? albumData.id : 'new'"
        :initial-data="albumData"
        @success="handleSuccess"
        @cancel="emit('update:isOpen', false)"
      />
    </div>
  </BaseDrawer>
</template>
