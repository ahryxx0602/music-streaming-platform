<script setup lang="ts">
import { ref, computed } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import api from '@/services/api';
import BaseInput from '@/components/base/BaseInput.vue';
import BaseButton from '@/components/base/BaseButton.vue';
import { IconShieldLock } from '@tabler/icons-vue';

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
    error.value = 'Vui lòng nhập mật khẩu hiện tại.';
    return;
  }
  if (!validatePassword(form.value.password)) {
    error.value = 'Mật khẩu mới phải từ 8 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt.';
    return;
  }
  if (form.value.password !== form.value.password_confirmation) {
    error.value = 'Mật khẩu xác nhận không khớp.';
    return;
  }

  loading.value = true;
  error.value = '';
  message.value = '';

  try {
    await api.put(`/${role.value}/auth/password`, form.value);
    message.value = 'Đổi mật khẩu thành công!';
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
    <h2 class="text-2xl font-bold text-white mb-6">Bảo mật & Đăng nhập</h2>
    
    <section class="max-w-xl">
      <h3 class="text-lg font-medium text-secondary mb-4">Đổi mật khẩu</h3>
      
      <form @submit.prevent="changePassword" class="space-y-4" novalidate>
        <BaseInput v-model="form.current_password" type="password" label="Mật khẩu hiện tại" placeholder="Nhập mật khẩu hiện tại" required />
        <BaseInput v-model="form.password" type="password" label="Mật khẩu mới" placeholder="Tối thiểu 8 ký tự, có ký tự đặc biệt" required />
        <BaseInput v-model="form.password_confirmation" type="password" label="Xác nhận mật khẩu mới" placeholder="Nhập lại mật khẩu mới" required />
        
        <p v-if="message" class="text-sm text-green-400 p-3 bg-green-500/10 rounded-lg border border-green-500/20">{{ message }}</p>
        <p v-if="error" class="text-sm text-red-400 p-3 bg-red-500/10 rounded-lg border border-red-500/20">{{ error }}</p>
        
        <div class="pt-2">
          <BaseButton type="submit" variant="primary" :loading="loading" :icon="IconShieldLock">Cập nhật mật khẩu</BaseButton>
        </div>
      </form>
    </section>
  </div>
</template>

<style scoped>
.text-secondary { color: var(--color-text-secondary, #CBD5E1); }
</style>
