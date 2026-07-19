<script setup lang="ts">
import { ref, computed } from 'vue';
import api from '@/services/api';
import { IconUserPlus, IconUserEdit } from '@tabler/icons-vue';
import BaseDrawer from '@/components/admin/ui/drawer/BaseDrawer.vue';
import BaseAdminButton from '@/components/admin/ui/button/BaseAdminButton.vue';
import ArtistForm from '../forms/ArtistForm.vue';

const props = defineProps<{
  modelValue: boolean;
  artistData?: any;
}>();

const emit = defineEmits(['update:modelValue', 'saved']);

const loading = ref(false);
const serverError = ref('');
const formRef = ref<InstanceType<typeof ArtistForm> | null>(null);

const isUpdate = computed(() => !!props.artistData);

const handleClose = () => {
  emit('update:modelValue', false);
  setTimeout(() => {
    formRef.value?.reset();
    serverError.value = '';
  }, 300); 
};

const handleFormSubmit = async (payload: any) => {
  serverError.value = '';
  loading.value = true;
  
  try {
    if (isUpdate.value) {
      await api.put(`/admin/users/${props.artistData.id}`, payload);
    } else {
      await api.post('/admin/users/artist', payload);
    }
    
    emit('saved');
    handleClose();
  } catch (err: any) {
    serverError.value = err.response?.data?.message || 'Có lỗi xảy ra.';
  } finally {
    loading.value = false;
  }
};

const triggerSubmit = () => {
  formRef.value?.validateAndSubmit();
};
</script>

<template>
  <BaseDrawer 
    :model-value="modelValue" 
    @update:model-value="$emit('update:modelValue', $event)"
    @close="handleClose"
    :title="isUpdate ? $t('admin.drawers.edit_artist') : $t('admin.drawers.add_artist')" 
    size="md"
  >
    <template #header-icon>
      <div class="w-8 h-8 rounded-lg bg-slate-100 text-theme-text flex items-center justify-center border border-theme-border">
        <component :is="isUpdate ? IconUserEdit : IconUserPlus" size="18" />
      </div>
    </template>

    <div class="mb-5 border-b border-theme-border pb-5">
      <h3 class="text-xs font-bold text-theme-text-sec uppercase tracking-wider mb-1">Thông tin chung</h3>
      <p class="text-[13px] text-theme-text-sec mb-4">{{ $t('admin.drawers.update_artist_desc') }}</p>
      
      <ArtistForm 
        ref="formRef"
        :loading="loading"
        :server-error="serverError"
        :initial-data="artistData"
        @submit="handleFormSubmit"
      />
    </div>

    <template #footer>
      <div class="flex items-center justify-end gap-3 w-full">
        <BaseAdminButton variant="secondary" @click="handleClose" :disabled="loading">
          {{ $t('admin.drawers.cancel') }}
        </BaseAdminButton>
        <BaseAdminButton variant="primary" @click="triggerSubmit" :loading="loading">
          {{ isUpdate ? $t('admin.drawers.update') : $t('admin.drawers.save_info') }}
        </BaseAdminButton>
      </div>
    </template>
  </BaseDrawer>
</template>
