<script setup lang="ts">
import { ref } from 'vue';
import { useModerationStore } from '@/stores/moderationStore';
import BaseModal from '@/components/admin/ui/modal/BaseModal.vue';
import BaseAdminButton from '@/components/admin/ui/button/BaseAdminButton.vue';
import { IconSend, IconAlertTriangle } from '@tabler/icons-vue';

const props = defineProps<{
  isOpen: boolean;
  songData: any;
}>();

const emit = defineEmits(['update:isOpen', 'success']);
const moderationStore = useModerationStore();
const reason = ref('');

const handleSubmit = async () => {
  if (!reason.value.trim() || !props.songData) return;
  
  try {
    await moderationStore.rejectSong(props.songData.id, reason.value);
    reason.value = ''; // Reset form
    emit('success');
    emit('update:isOpen', false);
  } catch (e) {
    // Error handled by store
  }
};
</script>

<template>
  <BaseModal
    :modelValue="isOpen"
    @update:modelValue="emit('update:isOpen', $event)"
    :title="$t('admin.moderation.reason')"
    size="sm"
  >
    <div class="space-y-4">
      <div class="flex items-start gap-3 p-3 bg-amber-50 text-amber-800 rounded-lg text-sm border border-amber-200/50">
        <IconAlertTriangle size="20" class="text-amber-500 shrink-0 mt-0.5" />
        <p>Bạn đang từ chối bài hát <strong>"{{ songData?.title }}"</strong>. Vui lòng cung cấp lý do để gửi email thông báo cho nghệ sĩ.</p>
      </div>

      <div>
        <textarea
          v-model="reason"
          rows="4"
          :placeholder="$t('admin.moderation.reason_placeholder')"
          class="w-full p-3 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none resize-none transition-all shadow-sm"
        ></textarea>
      </div>

      <!-- Error message -->
      <div v-if="moderationStore.error" class="p-3 bg-rose-50 text-rose-700 text-sm rounded-lg">
        {{ moderationStore.error }}
      </div>

      <div class="flex justify-end gap-3 pt-2">
        <BaseAdminButton type="button" variant="secondary" @click="emit('update:isOpen', false)">
          Hủy
        </BaseAdminButton>
        <button 
          type="button"
          @click="handleSubmit"
          :disabled="!reason.trim() || moderationStore.isProcessing"
          class="flex items-center gap-2 px-4 py-2 bg-rose-500 text-white rounded-lg font-medium hover:bg-rose-600 disabled:opacity-50 disabled:hover:bg-rose-500 transition-colors shadow-sm shadow-rose-500/20"
        >
          <IconSend size="16" />
          {{ moderationStore.isProcessing ? 'Đang gửi...' : $t('admin.moderation.confirm_reject') }}
        </button>
      </div>
    </div>
  </BaseModal>
</template>
