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
  email: props.initialData?.email || '',
  password: '',
  password_confirmation: ''
});

const localError = ref('');

watch(() => props.initialData, (newVal) => {
  formData.name = newVal?.name || '';
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
  formData.email = '';
  formData.password = '';
  formData.password_confirmation = '';
  localError.value = '';
};

defineExpose({ reset, validateAndSubmit });
</script>

<template>
  <form id="listener-form" @submit.prevent="validateAndSubmit" class="space-y-3">
    
    <div v-if="localError || serverError" class="mb-3 p-3 bg-rose-50 text-rose-700 border border-rose-200 rounded-lg text-sm font-medium flex items-start gap-2">
      <svg class="w-5 h-5 text-rose-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      {{ localError || serverError }}
    </div>

    <BaseAdminInput 
      v-model="formData.name" 
      label="Tên hiển thị" 
      placeholder="Ví dụ: Nguyễn Văn A" 
      required 
      :disabled="loading" 
    />
    
    <!-- Email của listener thường không cho sửa dễ dàng, nhưng admin có quyền -->
    <BaseAdminInput 
      v-model="formData.email" 
      type="email" 
      label="Địa chỉ Email" 
      placeholder="listener@domain.com" 
      required 
      :disabled="loading" 
    />
    
    <template v-if="!initialData">
      <BaseAdminInput v-model="formData.password" type="password" label="Mật khẩu" placeholder="Tối thiểu 8 ký tự" required :disabled="loading" />
      <BaseAdminInput v-model="formData.password_confirmation" type="password" label="Xác nhận mật khẩu" placeholder="Nhập lại mật khẩu" required :disabled="loading" />
    </template>
    
  </form>
</template>
