<script setup lang="ts">
import { ref, reactive, watch } from 'vue';
import BaseAdminInput from '@/components/admin/ui/form/BaseAdminInput.vue';

const props = defineProps<{
 loading?: boolean;
 serverError?: string;
 initialData?: any;
}>();

const emit = defineEmits(['submit']);

const formData = reactive({
 name: props.initialData?.name || '',
 stage_name: props.initialData?.artist_profile?.stage_name || '',
 email: props.initialData?.email || '',
 password: '',
 password_confirmation: ''
});

const localError = ref('');

watch(() => props.initialData, (newVal) => {
 formData.name = newVal?.name || '';
 formData.stage_name = newVal?.artist_profile?.stage_name || '';
 formData.email = newVal?.email || '';
 formData.password = '';
 formData.password_confirmation = '';
}, { deep: true });

const validateAndSubmit = () => {
 if (!props.initialData && formData.password !== formData.password_confirmation) {
 localError.value = 'Mật khẩu xác nhận không khớp.';
 return;
 }
 localError.value = '';
 
 emit('submit', { ...formData });
};

const reset = () => {
 formData.name = '';
 formData.stage_name = '';
 formData.email = '';
 formData.password = '';
 formData.password_confirmation = '';
 localError.value = '';
};

defineExpose({ reset, validateAndSubmit });
</script>

<template>
 <form id="artist-form" @submit.prevent="validateAndSubmit" class="space-y-3">
 
 <div v-if="localError || serverError" class="mb-3 p-3 bg-theme-danger/10 text-theme-danger border border-theme-danger/20 rounded-lg text-sm font-medium flex items-start gap-2">
 <svg class="w-5 h-5 text-theme-danger flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
 </svg>
 {{ localError || serverError }}
 </div>

 <BaseAdminInput v-model="formData.name" :label="$t('admin.users_page.form.real_name')" :placeholder="$t('admin.users_page.form.real_name_ph')" required :disabled="loading" />
 <BaseAdminInput v-model="formData.stage_name" :label="$t('admin.users_page.form.stage_name')" :placeholder="$t('admin.users_page.form.stage_name_ph')" required :disabled="loading" />
 <BaseAdminInput v-model="formData.email" type="email" :label="$t('admin.users_page.form.email')" :placeholder="$t('admin.users_page.form.email_ph')" required :disabled="loading" />
 
 <template v-if="!initialData">
 <BaseAdminInput v-model="formData.password" type="password" :label="$t('admin.users_page.form.password')" :placeholder="$t('admin.users_page.form.password_ph')" required :disabled="loading" />
 <BaseAdminInput v-model="formData.password_confirmation" type="password" :label="$t('admin.users_page.form.password_conf')" :placeholder="$t('admin.users_page.form.password_conf_ph')" required :disabled="loading" />
 </template>
 </form>
</template>
