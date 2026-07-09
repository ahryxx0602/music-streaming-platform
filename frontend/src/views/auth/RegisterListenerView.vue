<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../services/api';
import BaseInput from '../../components/base/BaseInput.vue';
import BaseButton from '../../components/base/BaseButton.vue';

const router = useRouter();

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
});

const loading = ref(false);
const error = ref('');

const handleRegister = async () => {
  if (form.value.password !== form.value.password_confirmation) {
    error.value = 'Mật khẩu xác nhận không khớp.';
    return;
  }

  error.value = '';
  loading.value = true;
  
  try {
    await api.post('/guest/auth/register', form.value);
    router.push({ path: '/login', query: { registered: 'success' } });
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Đăng ký thất bại. Vui lòng kiểm tra lại.';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="auth-form-container">
    <div class="text-center mb-8">
      <h1 class="text-3xl font-bold mb-2">Tạo Tài Khoản</h1>
      <p class="text-gray-400">Tham gia để nghe các bài hát yêu thích.</p>
    </div>
    
    <div v-if="error" class="error-alert">{{ error }}</div>

    <form @submit.prevent="handleRegister" class="space-y-4">
      <BaseInput v-model="form.name" label="Họ và tên" placeholder="Nhập tên của bạn" required />
      <BaseInput v-model="form.email" type="email" label="Email" placeholder="Nhập email" required />
      <BaseInput v-model="form.password" type="password" label="Mật khẩu" placeholder="Mật khẩu (Tối thiểu 8 ký tự)" required />
      <BaseInput v-model="form.password_confirmation" type="password" label="Xác nhận mật khẩu" placeholder="Nhập lại mật khẩu" required />
      
      <div class="pt-2">
        <BaseButton type="submit" variant="primary" :loading="loading">Đăng Ký</BaseButton>
      </div>
    </form>
    
    <div class="mt-6 text-center text-sm">
      <p class="text-gray-400">Đã có tài khoản? <router-link to="/login" class="text-primary hover:underline">Đăng nhập</router-link></p>
    </div>
  </div>
</template>

<style scoped>
.auth-form-container { width: 100%; }
.error-alert { background: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.2); color: #f43f5e; padding: 0.75rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.875rem; }
.text-center { text-align: center; }
.mb-8 { margin-bottom: 2rem; }
.mb-2 { margin-bottom: 0.5rem; }
.space-y-4 > :not([hidden]) ~ :not([hidden]) { margin-top: 1rem; }
.pt-2 { padding-top: 0.5rem; }
.mt-6 { margin-top: 1.5rem; }
.text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
.font-bold { font-weight: 700; }
.text-gray-400 { color: #9ca3af; }
.text-sm { font-size: 0.875rem; }
.text-primary { color: var(--color-primary); }
.hover\:underline:hover { text-decoration: underline; }
</style>
