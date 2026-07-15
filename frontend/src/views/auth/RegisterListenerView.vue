<script setup lang="ts">
import { ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../services/api';
import BaseInput from '../../components/base/BaseInput.vue';
import BaseButton from '../../components/base/BaseButton.vue';
import { IconUser, IconMail, IconLock, IconUserPlus, IconPlayerPlay } from '@tabler/icons-vue';

const router = useRouter();

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
});

const errors = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
});

// Xóa lỗi ngay khi người dùng bắt đầu gõ lại vào ô input
watch(() => form.value.name, () => { errors.value.name = ''; });
watch(() => form.value.email, () => { errors.value.email = ''; });
watch(() => form.value.password, () => { errors.value.password = ''; });
watch(() => form.value.password_confirmation, () => { errors.value.password_confirmation = ''; });

const loading = ref(false);
const globalError = ref('');

const validateForm = () => {
  let isValid = true;
  errors.value = { name: '', email: '', password: '', password_confirmation: '' };
  
  if (!form.value.name.trim()) {
    errors.value.name = 'Vui lòng nhập họ và tên.';
    isValid = false;
  }
  
  if (!form.value.email.trim()) {
    errors.value.email = 'Vui lòng nhập email.';
    isValid = false;
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
    errors.value.email = 'Email không hợp lệ.';
    isValid = false;
  }
  
  if (!form.value.password) {
    errors.value.password = 'Vui lòng nhập mật khẩu.';
    isValid = false;
  } else if (form.value.password.length < 8) {
    errors.value.password = 'Mật khẩu phải dài ít nhất 8 ký tự.';
    isValid = false;
  }
  
  if (form.value.password !== form.value.password_confirmation) {
    errors.value.password_confirmation = 'Mật khẩu xác nhận không khớp.';
    isValid = false;
  }
  
  return isValid;
};

const handleRegister = async () => {
  globalError.value = '';
  
  if (!validateForm()) {
    return;
  }

  loading.value = true;
  
  try {
    const backendUrl = import.meta.env.VITE_BACKEND_URL || 'http://localhost:8000';
    await api.get(`${backendUrl}/sanctum/csrf-cookie`);
    await api.post('/guest/auth/register', form.value);
    router.push({ path: '/login', query: { registered: 'success' } });
  } catch (err: any) {
    if (err.response?.status === 422) {
      const validationErrors = err.response.data.errors;
      errors.value.name = validationErrors.name ? validationErrors.name[0] : '';
      errors.value.email = validationErrors.email ? validationErrors.email[0] : '';
      errors.value.password = validationErrors.password ? validationErrors.password[0] : '';
    } else {
      globalError.value = err.response?.data?.message || 'Đăng ký thất bại. Vui lòng kiểm tra lại.';
    }
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="auth-form-container">
    <!-- Brand Identity -->
    <div class="brand-header flex flex-col items-center justify-center mb-6">
      <div class="brand-logo w-12 h-12 rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 flex items-center justify-center mb-2 shadow-lg shadow-blue-500/30">
        <IconPlayerPlay class="text-white fill-white" size="24" stroke-width="1.5" />
      </div>
      <h2 class="text-xl font-bold tracking-wide text-gray-200">AuroraStream</h2>
    </div>

    <div class="text-center mb-8">
      <h1 class="text-3xl font-bold mb-2">Tạo Tài Khoản</h1>
      <p class="subtitle-text">Tham gia để nghe các bài hát yêu thích.</p>
    </div>
    
    <div v-if="globalError" class="error-alert">{{ globalError }}</div>

    <form @submit.prevent="handleRegister" class="space-y-4" novalidate>
      <BaseInput v-model="form.name" label="Họ và tên" placeholder="Nhập tên của bạn" :icon="IconUser" :error="errors.name" required />
      <BaseInput v-model="form.email" type="email" label="Email" placeholder="Nhập email" :icon="IconMail" :error="errors.email" required />
      <BaseInput v-model="form.password" type="password" label="Mật khẩu" placeholder="Mật khẩu (Tối thiểu 8 ký tự)" :icon="IconLock" :error="errors.password" required />
      <BaseInput v-model="form.password_confirmation" type="password" label="Xác nhận mật khẩu" placeholder="Nhập lại mật khẩu" :icon="IconLock" :error="errors.password_confirmation" required />
      
      <div class="pt-2">
        <BaseButton type="submit" variant="primary" :loading="loading" :icon="IconUserPlus">Đăng Ký</BaseButton>
      </div>
    </form>
    
    <div class="mt-6 text-center text-sm">
      <p class="subtitle-text">Đã có tài khoản? <router-link to="/login" class="cta-link">Đăng nhập</router-link></p>
    </div>
  </div>
</template>

<style scoped>
.auth-form-container { width: 100%; }
.error-alert { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #EF4444; padding: 0.75rem 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 0.875rem; }

/* Tailwind Utilities (custom fallback) */
.flex { display: flex; }
.flex-col { flex-direction: column; }
.items-center { align-items: center; }
.justify-center { justify-content: center; }
.mb-2 { margin-bottom: 0.5rem; }
.mb-6 { margin-bottom: 1.5rem; }
.w-12 { width: 3rem; }
.h-12 { height: 3rem; }
.rounded-xl { border-radius: 0.75rem; }
.bg-gradient-to-tr { background-image: linear-gradient(to top right, var(--tw-gradient-stops)); }
.from-blue-600 { --tw-gradient-from: #2563eb; --tw-gradient-to: rgba(37, 99, 235, 0); --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to); }
.to-cyan-400 { --tw-gradient-to: #22d3ee; }
.text-white { color: #ffffff; }
.fill-white { fill: #ffffff; }
.shadow-lg { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
.shadow-blue-500\/30 { box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3), 0 4px 6px -2px rgba(59, 130, 246, 0.15); }
.text-xl { font-size: 1.25rem; line-height: 1.75rem; }
.font-bold { font-weight: 700; }
.tracking-wide { letter-spacing: 0.025em; }
.text-gray-200 { color: #e5e7eb; }

.text-center { text-align: center; }
.mb-8 { margin-bottom: 2rem; }
.space-y-4 > :not([hidden]) ~ :not([hidden]) { margin-top: 1rem; }
.pt-2 { padding-top: 0.5rem; }
.mt-6 { margin-top: 1.5rem; }
.text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
.text-sm { font-size: 0.875rem; }

/* Nâng cấp Typography */
.subtitle-text { color: var(--color-text-secondary, #CBD5E1); font-weight: 500; }
.cta-link { color: var(--color-primary, #3B82F6); font-weight: 600; text-decoration: none; transition: var(--transition-smooth, all 150ms ease); }
.cta-link:hover { text-decoration: underline; color: var(--color-input-border-focus, #60A5FA); }
</style>
