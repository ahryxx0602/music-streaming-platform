<script setup lang="ts">
import { useModerationStore } from '@/stores/moderationStore';
import BaseModal from '@/components/admin/ui/modal/BaseModal.vue';
import BaseAdminButton from '@/components/admin/ui/button/BaseAdminButton.vue';
import { IconCheck, IconX, IconMusic } from '@tabler/icons-vue';

const props = defineProps<{
  isOpen: boolean;
  songData: any;
}>();

const emit = defineEmits(['update:isOpen', 'approve', 'reject']);
const moderationStore = useModerationStore();

const handleApprove = async () => {
  if (!props.songData) return;
  try {
    await moderationStore.approveSong(props.songData.id);
    emit('update:isOpen', false);
  } catch (e) {
    // Lỗi đã được store xử lý
  }
};

const handleReject = () => {
  emit('reject', props.songData); // Bắn sự kiện mở modal từ chối
};
</script>

<template>
  <BaseModal
    :modelValue="isOpen"
    @update:modelValue="emit('update:isOpen', $event)"
    :title="$t('admin.moderation.preview')"
    size="md"
  >
    <div v-if="songData" class="space-y-6">
      
      <!-- Song Info -->
      <div class="flex items-center gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100">
        <div class="w-16 h-16 bg-indigo-100 text-indigo-500 rounded-lg flex items-center justify-center shrink-0 shadow-inner">
          <IconMusic size="32" />
        </div>
        <div class="flex-1 min-w-0">
          <h2 class="text-lg font-bold text-slate-900 truncate">{{ songData.title }}</h2>
          <div class="text-sm text-slate-600 mt-1 flex items-center gap-2">
            <span class="font-medium text-slate-800">{{ songData.artist?.artistProfile?.stage_name || songData.artist?.name }}</span>
            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
            <span>{{ songData.genre?.name || 'Chưa phân loại' }}</span>
          </div>
        </div>
      </div>

      <!-- Audio Player -->
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Nghe thử Âm thanh</label>
        <div class="bg-slate-900 p-4 rounded-xl shadow-inner">
          <audio 
            controls 
            class="w-full h-10 outline-none" 
            controlsList="nodownload"
            :src="songData.original_file_path || songData.s3_key || songData.hls_path"
          >
            Trình duyệt của bạn không hỗ trợ thẻ audio.
          </audio>
        </div>
        <p class="text-[11px] text-slate-500 mt-2 text-center">Nguồn phát: Audio File gốc / HLS Stream</p>
      </div>

      <!-- Lyrics / Description (Tùy chọn) -->
      <div v-if="songData.lyrics" class="border border-slate-200 rounded-xl overflow-hidden">
        <div class="bg-slate-50 px-3 py-2 border-b border-slate-200 text-xs font-bold text-slate-600 uppercase">
          Lời bài hát
        </div>
        <div class="p-3 max-h-40 overflow-y-auto text-sm text-slate-700 whitespace-pre-wrap">
          {{ songData.lyrics }}
        </div>
      </div>

      <!-- Error message -->
      <div v-if="moderationStore.error" class="p-3 bg-rose-50 text-rose-700 text-sm rounded-lg">
        {{ moderationStore.error }}
      </div>

      <!-- Actions -->
      <div class="grid grid-cols-2 gap-3 pt-4 border-t border-slate-100">
        <button 
          type="button"
          @click="handleReject"
          :disabled="moderationStore.isProcessing"
          class="flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl font-semibold border-2 border-rose-100 text-rose-600 hover:bg-rose-50 hover:border-rose-200 disabled:opacity-50 transition-colors"
        >
          <IconX size="18" />
          {{ $t('admin.moderation.reject') }}
        </button>
        
        <button 
          type="button"
          @click="handleApprove"
          :disabled="moderationStore.isProcessing"
          class="flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl font-semibold bg-emerald-500 text-white hover:bg-emerald-600 disabled:opacity-50 shadow-sm shadow-emerald-500/30 transition-all"
        >
          <IconCheck size="18" />
          {{ moderationStore.isProcessing ? 'Đang xử lý...' : $t('admin.moderation.approve') }}
        </button>
      </div>

    </div>
  </BaseModal>
</template>

<style scoped>
/* Tuỳ chỉnh audio player mặc định nếu cần */
audio::-webkit-media-controls-panel {
  background-color: #f8fafc;
}
audio::-webkit-media-controls-play-button,
audio::-webkit-media-controls-mute-button {
  background-color: #e2e8f0;
  border-radius: 50%;
}
</style>
