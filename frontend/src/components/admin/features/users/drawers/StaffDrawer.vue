<script setup lang="ts">
import { ref, watch } from 'vue';
import api from '@/services/api';
import { IconShieldLock } from '@tabler/icons-vue';
import BaseDrawer from '@/components/admin/ui/drawer/BaseDrawer.vue';
import BaseAdminButton from '@/components/admin/ui/button/BaseAdminButton.vue';
import StaffForm from '../forms/StaffForm.vue';

const props = defineProps<{
 modelValue: boolean;
 staffData?: any; // Nếu có tức là Update
}>();

const emit = defineEmits(['update:modelValue', 'saved']);

const loading = ref(false);
const serverError = ref('');
const formRef = ref<InstanceType<typeof StaffForm> | null>(null);

const isUpdate = ref(false);

watch(() => props.modelValue, (newVal) => {
 if (newVal) {
 isUpdate.value = !!props.staffData;
 }
});

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
 await api.put(`/admin/users/${props.staffData.id}`, payload);
 // Nếu có cập nhật quyền
 if (payload.roles && payload.roles.length) {
 await api.put(`/admin/users/${props.staffData.id}/roles`, { roles: payload.roles });
 }
 } else {
 await api.post('/admin/users/staff', payload);
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
 :title="isUpdate ? $t('admin.drawers.edit_staff') : $t('admin.drawers.add_staff')" 
 size="md"
 >
 <template #header-icon>
 <div class="w-8 h-8 rounded-lg bg-slate-100 text-theme-text flex items-center justify-center border border-theme-border">
 <IconShieldLock size="18" />
 </div>
 </template>

 <div class="mb-5 border-b border-theme-border pb-5">
 <h3 class="text-xs font-bold text-theme-text-sec uppercase tracking-wider mb-1">Thông tin & Cấp quyền</h3>
 <p class="text-[13px] text-theme-text-sec mb-4">Nhân viên sẽ dùng email này để đăng nhập vào Admin Portal.</p>
 
 <StaffForm 
 ref="formRef"
 :loading="loading"
 :server-error="serverError"
 :initial-data="staffData"
 @submit="handleFormSubmit"
 />
 </div>

 <template #footer>
 <div class="flex items-center justify-end gap-3 w-full">
 <BaseAdminButton variant="secondary" @click="handleClose" :disabled="loading">
 {{ $t('admin.drawers.cancel') }}
 </BaseAdminButton>
 <BaseAdminButton variant="primary" @click="triggerSubmit" :loading="loading">
 {{ isUpdate ? $t('admin.drawers.update') : $t('admin.drawers.invite_staff') }}
 </BaseAdminButton>
 </div>
 </template>
 </BaseDrawer>
</template>
