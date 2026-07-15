<script setup lang="ts">
import { ref } from 'vue';
import BaseAdminInput from '@/components/admin/ui/form/BaseAdminInput.vue';

const props = defineProps<{
  loading?: boolean;
  serverError?: string;
  initialData?: any;
}>();

const emit = defineEmits(['submit']);

const form = ref({
  name: props.initialData?.name || '',
  email: props.initialData?.email || '',
  password: '',
  roles: props.initialData?.roles?.map((r: any) => r.name || r) || []
});

const errors = ref<Record<string, string>>({});
const availableRoles = ['Super Admin', 'Content Moderator', 'Support Agent'];

const validateAndSubmit = () => {
  errors.value = {};
  if (!form.value.name) errors.value.name = 'Vui lòng nhập tên nhân viên.';
  if (!form.value.email) errors.value.email = 'Vui lòng nhập email.';
  if (!props.initialData && !form.value.password) errors.value.password = 'Vui lòng nhập mật khẩu khởi tạo.';
  if (form.value.roles.length === 0) errors.value.roles = 'Vui lòng chọn ít nhất 1 quyền.';

  if (Object.keys(errors.value).length === 0) {
    emit('submit', form.value);
  }
};

const reset = () => {
  form.value = { name: '', email: '', password: '', roles: [] };
  errors.value = {};
};

defineExpose({ validateAndSubmit, reset });
</script>

<template>
  <div class="space-y-4">
    <div v-if="serverError" class="p-3 text-sm text-rose-600 bg-rose-50 rounded-lg border border-rose-200">
      {{ serverError }}
    </div>

    <BaseAdminInput 
      label="Họ và tên" 
      v-model="form.name" 
      placeholder="VD: Trần Văn A" 
      :error="errors.name"
      :disabled="loading"
    />

    <BaseAdminInput 
      label="Email nội bộ" 
      type="email"
      v-model="form.email" 
      placeholder="VD: vana@aurora.com" 
      :error="errors.email"
      :disabled="loading"
    />

    <BaseAdminInput 
      v-if="!initialData"
      label="Mật khẩu khởi tạo" 
      type="password"
      v-model="form.password" 
      placeholder="Tối thiểu 8 ký tự" 
      :error="errors.password"
      :disabled="loading"
    />

    <div class="space-y-2">
      <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wide">Quyền hạn</label>
      <div class="space-y-2">
        <label v-for="role in availableRoles" :key="role" class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
          <input 
            type="checkbox" 
            :value="role" 
            v-model="form.roles"
            :disabled="loading"
            class="w-4 h-4 text-admin-primary rounded border-slate-300 focus:ring-admin-primary"
          />
          {{ role }}
        </label>
      </div>
      <p v-if="errors.roles" class="text-xs text-rose-500 mt-1">{{ errors.roles }}</p>
    </div>
  </div>
</template>
