<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import api from '../../services/api';
import BaseInput from '../../components/base/BaseInput.vue';
import BaseButton from '../../components/base/BaseButton.vue';
import { IconUser, IconMail, IconLock, IconMicrophone, IconUserPlus, IconPlayerPlay } from '@tabler/icons-vue';

const router = useRouter();
const route = useRoute();

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  stage_name: '',
  token: route.query.token as string || ''
});

const loading = ref(false);
const error = ref('');
const tokenValidating = ref(true);

onMounted(async () => {
  if (!form.value.token) {
    error.value = 'Không tìm thấy mã mời hợp lệ.';
    tokenValidating.value = false;
    return;
  }
  
  try {
    const res = await api.get('/guest/auth/artist-register/validate', { params: { token: form.value.token } });
    form.value.email = res.data.data.email;
  } catch (err: any) {
    error.value = 'Mã mời không hợp lệ hoặc đã bị vô hiệu hóa.';
    alert("Mã mời này vừa bị vô hiệu hóa hoặc đã hết hạn.");
    router.push('/login');
  } finally {
    tokenValidating.value = false;
  }
});

const handleRegister = async () => {
  if (form.value.password !== form.value.password_confirmation) {
    error.value = 'Mật khẩu xác nhận không khớp.';
    return;
  }

  error.value = '';
  loading.value = true;
  
  try {
    await api.post('/guest/auth/artist-register', form.value);
    router.push({ path: '/login', query: { registered: 'success' } });
  } catch (err: any) {
    if (err.response?.status === 403) {
       alert("Mã mời này vừa bị vô hiệu hóa");
       router.push('/');
    } else {
       error.value = err.response?.data?.message || 'Đăng ký thất bại.';
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
      <h1 class="text-3xl font-bold mb-2">Artist Portal</h1>
      <p class="subtitle-text">Đăng ký tài khoản Nghệ sĩ</p>
    </div>
    
    <div v-if="tokenValidating" class="text-center py-8 subtitle-text">
      Đang xác thực mã mời...
    </div>
    <div v-else-if="error && !form.email" class="error-alert">{{ error }}</div>
    <div v-else>
      <div v-if="error" class="error-alert">{{ error }}</div>

      <form @submit.prevent="handleRegister" class="space-y-4" novalidate>
        <!-- disabled thay cho readonly trên UI -->
        <div class="readonly-field">
          <label class="base-label">Email (Cố định)</label>
          <div class="input-container">
            <div class="icon-wrapper"><IconMail size="20" stroke-width="1.5" /></div>
            <input class="base-input disabled has-icon" :value="form.email" disabled />
          </div>
        </div>
        <BaseInput v-model="form.name" label="Họ tên thật" :icon="IconUser" :error="!!error" required />
        <BaseInput v-model="form.stage_name" label="Nghệ danh (Stage Name)" :icon="IconMicrophone" :error="!!error" required />
        <BaseInput v-model="form.password" type="password" label="Mật khẩu" :icon="IconLock" :error="!!error" required />
        <BaseInput v-model="form.password_confirmation" type="password" label="Xác nhận mật khẩu" :icon="IconLock" :error="!!error" required />
        
        <div class="pt-2">
          <BaseButton type="submit" variant="primary" :loading="loading" :icon="IconUserPlus">Đăng Ký Nghệ Sĩ</BaseButton>
        </div>
      </form>
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
.py-8 { padding-top: 2rem; padding-bottom: 2rem; }
.mb-8 { margin-bottom: 2rem; }
.space-y-4 > :not([hidden]) ~ :not([hidden]) { margin-top: 1rem; }
.pt-2 { padding-top: 0.5rem; }
.text-3xl { font-size: 1.875rem; line-height: 2.25rem; }

.readonly-field { margin-bottom: 1.25rem; display: flex; flex-direction: column; }
.base-label { font-size: 0.875rem; font-weight: 500; color: var(--color-text-secondary, #CBD5E1); margin-bottom: 0.75rem; letter-spacing: 0.025em;}
.input-container { position: relative; display: flex; align-items: center; }
.icon-wrapper { position: absolute; left: 1rem; color: #7C8CA5; display: flex; align-items: center; justify-content: center; pointer-events: none; transition: var(--transition-smooth); }
.base-input { width: 100%; height: 48px; background: #131B2F; border: 1px solid #2A3B57; border-radius: 12px; padding: 0 1rem; color: #FFFFFF; font-size: 1rem; outline: none; box-sizing: border-box; transition: var(--transition-smooth); }
.base-input.has-icon { padding-left: 2.75rem; }
.base-input.disabled { opacity: 0.6; cursor: not-allowed; background: rgba(0,0,0,0.2); border-color: transparent; }

/* Nâng cấp Typography */
.subtitle-text { color: var(--color-text-secondary, #CBD5E1); font-weight: 500; }
</style>
