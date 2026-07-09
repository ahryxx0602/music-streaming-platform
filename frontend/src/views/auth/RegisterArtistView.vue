<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import api from '../../services/api';
import BaseInput from '../../components/base/BaseInput.vue';
import BaseButton from '../../components/base/BaseButton.vue';

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
    <div class="text-center mb-8">
      <h1 class="text-3xl font-bold mb-2">Artist Portal</h1>
      <p class="text-gray-400">Đăng ký tài khoản Nghệ sĩ</p>
    </div>
    
    <div v-if="tokenValidating" class="text-center py-8 text-gray-400">
      Đang xác thực mã mời...
    </div>
    <div v-else-if="error && !form.email" class="error-alert">{{ error }}</div>
    <div v-else>
      <div v-if="error" class="error-alert">{{ error }}</div>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <!-- disabled thay cho readonly trên UI -->
        <div class="readonly-field">
          <label class="base-label">Email (Cố định)</label>
          <input class="base-input disabled" :value="form.email" disabled />
        </div>
        <BaseInput v-model="form.name" label="Họ tên thật" required />
        <BaseInput v-model="form.stage_name" label="Nghệ danh (Stage Name)" required />
        <BaseInput v-model="form.password" type="password" label="Mật khẩu" required />
        <BaseInput v-model="form.password_confirmation" type="password" label="Xác nhận mật khẩu" required />
        
        <div class="pt-2">
          <BaseButton type="submit" variant="primary" :loading="loading">Đăng Ký Nghệ Sĩ</BaseButton>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.auth-form-container { width: 100%; }
.error-alert { background: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.2); color: #f43f5e; padding: 0.75rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.875rem; }
.text-center { text-align: center; }
.py-8 { padding-top: 2rem; padding-bottom: 2rem; }
.mb-8 { margin-bottom: 2rem; }
.mb-2 { margin-bottom: 0.5rem; }
.space-y-4 > :not([hidden]) ~ :not([hidden]) { margin-top: 1rem; }
.pt-2 { padding-top: 0.5rem; }
.text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
.font-bold { font-weight: 700; }
.text-gray-400 { color: #9ca3af; }

.readonly-field { margin-bottom: 1.25rem; display: flex; flex-direction: column; }
.base-label { font-size: 0.875rem; font-weight: 500; color: #cbd5e1; margin-bottom: 0.5rem; }
.base-input { width: 100%; background: var(--color-glass-input); border: 1px solid var(--color-glass-border); border-radius: var(--radius-md); padding: 0.875rem 1rem; color: #f8fafc; font-size: 1rem; outline: none; box-sizing: border-box; }
.base-input.disabled { opacity: 0.7; cursor: not-allowed; background: rgba(0,0,0,0.2); }
</style>
