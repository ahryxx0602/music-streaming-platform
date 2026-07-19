<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useModerationStore } from '@/stores/moderationStore';
import ModerationTable from '@/components/admin/features/moderation/ModerationTable.vue';
import PreviewModal from '@/components/admin/features/moderation/PreviewModal.vue';
import RejectReasonModal from '@/components/admin/features/moderation/RejectReasonModal.vue';

const moderationStore = useModerationStore();

const isPreviewOpen = ref(false);
const isRejectOpen = ref(false);
const selectedSong = ref<any>(null);

onMounted(() => {
  moderationStore.fetchPendingSongs();
});

const openPreview = (song: any) => {
  selectedSong.value = song;
  isPreviewOpen.value = true;
};

const openReject = (song: any) => {
  // Đóng modal preview nếu đang mở
  isPreviewOpen.value = false;
  // Mở modal từ chối
  selectedSong.value = song;
  setTimeout(() => {
    isRejectOpen.value = true;
  }, 200); // Đợi modal preview đóng hẳn để mượt hơn
};
</script>

<template>
  <div class="h-full flex flex-col p-6 max-w-[1400px] mx-auto w-full">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-900 mb-1">
          {{ $t('admin.moderation.title', 'Kiểm duyệt bài hát') }}
        </h1>
        <p class="text-sm text-slate-500">
          {{ $t('admin.moderation.subtitle', 'Xem xét và phê duyệt các bài hát do nghệ sĩ tải lên') }}
        </p>
      </div>
      
      <!-- Có thể thêm thống kê nhỏ ở góc phải (Số bài chờ duyệt) -->
      <div class="bg-amber-50 border border-amber-100 px-4 py-2 rounded-xl flex items-center gap-3">
        <div class="w-8 h-8 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center font-bold">
          {{ moderationStore.meta.total }}
        </div>
        <div class="text-sm font-medium text-amber-800">Bài hát đang chờ</div>
      </div>
    </div>

    <!-- Table Content -->
    <div class="flex-1 min-h-0">
      <ModerationTable @preview="openPreview" />
    </div>

    <!-- Modals -->
    <PreviewModal 
      v-model:is-open="isPreviewOpen" 
      :song-data="selectedSong" 
      @reject="openReject"
    />
    
    <RejectReasonModal 
      v-model:is-open="isRejectOpen" 
      :song-data="selectedSong"
    />
  </div>
</template>
