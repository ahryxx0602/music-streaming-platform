<script setup lang="ts">
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useAuthStore } from '@/stores/authStore';
import api from '@/services/api';
import BaseInput from '@/components/base/BaseInput.vue';
import BaseButton from '@/components/base/BaseButton.vue';
import { IconShieldLock } from '@tabler/icons-vue';
const { t } = useI18n();

const authStore = useAuthStore();
const role = computed(() => authStore.role?.toLowerCase() || 'listener');

const form = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
});

const loading = ref(false);
const message = ref('');
const error = ref('');

const validatePassword = (pass: string) => {
  const minLength = pass.length >= 8;
  const hasUpper = /[A-Z]/.test(pass);
  const hasLower = /[a-z]/.test(pass);
  const hasNumber = /[0-9]/.test(pass);
  const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(pass);
  return minLength && hasUpper && hasLower && hasNumber && hasSpecial;
};

const changePassword = async () => {
  if (!form.value.current_password) {
    error.value = t('settings.security.err_current_required');
    return;
  }
  if (!validatePassword(form.value.password)) {
    error.value = t('settings.security.err_new_invalid');
    return;
  }
  if (form.value.password !== form.value.password_confirmation) {
    error.value = t('settings.security.err_confirm_not_match');
    return;
  }

  loading.value = true;
  error.value = '';
  message.value = '';

  try {
    await api.put(`/${role.value}/auth/password`, form.value);
    message.value = t('settings.security.success_change');
    form.value = { current_password: '', password: '', password_confirmation: '' };
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Có lỗi xảy ra, vui lòng thử lại.';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="security-settings">
    <h2 class="text-2xl font-bold text-theme-text mb-6">Bảo mật & Đăng nhập</h2>
    
    <section class="max-w-xl">
      <h3 class="text-lg font-medium text-theme-text-sec mb-4">{{ $t('settings.security.change_password') }}</h3>
      
      <form @submit.prevent="changePassword" class="space-y-4" novalidate>
        <BaseInput v-model="form.current_password" type="password" :label="$t('settings.security.current_password')" :placeholder="$t('settings.security.ph_current_password')" required />
        <BaseInput v-model="form.password" type="password" :label="$t('settings.security.new_password')" :placeholder="$t('settings.security.ph_new_password')" required />
        <BaseInput v-model="form.password_confirmation" type="password" :label="$t('settings.security.confirm_new_password')" :placeholder="$t('settings.security.ph_confirm_new_password')" required />
        
        <p v-if="message" class="text-sm text-green-400 p-3 bg-green-500/10 rounded-lg border border-green-500/20">{{ message }}</p>
        <p v-if="error" class="text-sm text-red-400 p-3 bg-red-500/10 rounded-lg border border-red-500/20">{{ error }}</p>
        
        <div class="pt-2">
          <BaseButton type="submit" variant="primary" :loading="loading" :icon="IconShieldLock">{{ $t('settings.security.update_password') }}</BaseButton>
        </div>
      </form>
    </section>
  </div>
</template>

<style scoped>
/* Scoped overrides if needed */
</style>
