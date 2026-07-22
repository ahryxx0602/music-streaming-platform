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
 <div v-if="serverError" class="p-3 text-sm text-theme-danger bg-theme-danger/10 rounded-lg border border-theme-danger/20 font-medium">
 {{ serverError }}
 </div>

 <BaseAdminInput 
 :label="$t('admin.users_page.form.real_name')" 
 v-model="form.name" 
 :placeholder="$t('admin.users_page.form.real_name_ph')" 
 :error="errors.name"
 :disabled="loading"
 />

 <BaseAdminInput 
 :label="$t('admin.users_page.form.email')" 
 type="email"
 v-model="form.email" 
 :placeholder="$t('admin.users_page.form.email_ph')" 
 :error="errors.email"
 :disabled="loading"
 />

 <BaseAdminInput 
 v-if="!initialData"
 :label="$t('admin.users_page.form.password')" 
 type="password"
 v-model="form.password" 
 :placeholder="$t('admin.users_page.form.password_ph')" 
 :error="errors.password"
 :disabled="loading"
 />

 <div class="space-y-2">
 <label class="block text-xs font-semibold text-[var(--color-label)] uppercase tracking-wide">{{ $t('admin.roles.title') }}</label>
 <div class="space-y-2">
 <label v-for="role in availableRoles" :key="role" class="flex items-center gap-2 text-sm text-theme-text cursor-pointer hover:text-theme-primary transition-colors">
 <input 
 type="checkbox" 
 :value="role" 
 v-model="form.roles"
 :disabled="loading"
 class="w-4 h-4 text-theme-primary rounded border-theme-border focus:ring-theme-primary bg-[var(--color-input-bg)] cursor-pointer"
 />
 {{ role }}
 </label>
 </div>
 <p v-if="errors.roles" class="text-xs text-theme-danger mt-1 font-medium">{{ errors.roles }}</p>
 </div>
 </div>
</template>
