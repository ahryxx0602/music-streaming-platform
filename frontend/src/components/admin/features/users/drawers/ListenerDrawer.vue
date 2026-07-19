<script setup lang="ts">
import { ref } from 'vue';
import api from '@/services/api';
import { computed } from 'vue';
import { IconUserEdit, IconCrown, IconUserPlus } from '@tabler/icons-vue';
import BaseDrawer from '@/components/admin/ui/drawer/BaseDrawer.vue';
import BaseAdminButton from '@/components/admin/ui/button/BaseAdminButton.vue';
import ListenerForm from '../forms/ListenerForm.vue';

const props = defineProps<{
  modelValue: boolean;
  listenerData?: any;
}>();

const emit = defineEmits(['update:modelValue', 'saved']);

const loading = ref(false);
const serverError = ref('');
const formRef = ref<InstanceType<typeof ListenerForm> | null>(null);

const isUpdate = computed(() => !!props.listenerData);

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
      await api.put(`/admin/users/${props.listenerData.id}`, payload);
    } else {
      await api.post('/admin/users/listener', payload);
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

const formatDate = (dateString: string) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('en-GB'); 
};
</script>

<template>
  <BaseDrawer 
    :model-value="modelValue" 
    @update:model-value="$emit('update:modelValue', $event)"
    @close="handleClose"
    :title="isUpdate ? $t('admin.drawers.edit_listener') : $t('admin.drawers.add_listener')" 
    size="md"
  >
    <template #header-icon>
      <div class="w-8 h-8 rounded-lg bg-slate-100 text-theme-text flex items-center justify-center border border-theme-border">
        <component :is="isUpdate ? IconUserEdit : IconUserPlus" size="18" />
      </div>
    </template>

    <!-- Subscription Status Card -->
    <div v-if="listenerData" class="mb-5 p-4 rounded-xl border flex items-start gap-4"
         :class="listenerData.is_premium ? 'bg-amber-50 border-amber-200' : 'bg-theme-bg border-theme-border'">
      <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
           :class="listenerData.is_premium ? 'bg-amber-100 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400' : 'bg-slate-200 text-theme-text-sec'">
        <IconCrown size="20" />
      </div>
      <div>
        <h4 class="text-sm font-bold" :class="listenerData.is_premium ? 'text-amber-800' : 'text-theme-text'">
          {{ listenerData.is_premium ? 'Premium Plan' : 'Free Plan' }}
        </h4>
        <p class="text-xs mt-1" :class="listenerData.is_premium ? 'text-amber-700/80' : 'text-theme-text-sec'">
          Tham gia từ ngày {{ formatDate(listenerData.created_at) }}.
        </p>
      </div>
    </div>

    <!-- General Info Section -->
    <div class="mb-5 border-b border-theme-border pb-5">
      <h3 class="text-xs font-bold text-theme-text-sec uppercase tracking-wider mb-1">{{ isUpdate ? $t('admin.drawers.update_info') : $t('admin.drawers.general_info') }}</h3>
      <p class="text-[13px] text-theme-text-sec mb-4">{{ isUpdate ? 'Thay đổi tên hiển thị và email hệ thống.' : 'Khởi tạo tài khoản Người nghe mới.' }}</p>
      
      <ListenerForm 
        ref="formRef"
        :loading="loading"
        :server-error="serverError"
        :initial-data="listenerData"
        @submit="handleFormSubmit"
      />
    </div>

    <template #footer>
      <div class="flex items-center justify-end gap-3 w-full">
        <BaseAdminButton variant="secondary" @click="handleClose" :disabled="loading">
          {{ $t('admin.drawers.cancel') }}
        </BaseAdminButton>
        <BaseAdminButton variant="primary" @click="triggerSubmit" :loading="loading">
          {{ isUpdate ? $t('admin.drawers.update_info') : $t('admin.drawers.add_listener') }}
        </BaseAdminButton>
      </div>
    </template>
  </BaseDrawer>
</template>
