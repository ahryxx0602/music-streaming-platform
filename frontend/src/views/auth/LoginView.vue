<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/authStore';
import BaseInput from '../../components/base/BaseInput.vue';
import BaseButton from '../../components/base/BaseButton.vue';

const router = useRouter();
const authStore = useAuthStore();

const email = ref('');
const password = ref('');
const loading = ref(false);
const error = ref('');

const handleLogin = async () => {
  if (!email.value || !password.value) {
    error.value = 'Vui lòng nhập đầy đủ email và mật khẩu.';
    return;
  }
  
  error.value = '';
  loading.value = true;
  
  try {
    await authStore.login({ email: email.value, password: password.value });
    
    // Xử lý redirect theo [RULE-AUTH-06]
    if (authStore.role === 'Admin') {
      router.push('/admin');
    } else if (authStore.role === 'Artist') {
      router.push('/artist');
    } else {
      router.push('/'); // Listener
    }
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Đăng nhập thất bại. Vui lòng thử lại.';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="auth-form-container">
    <div class="text-center mb-8">
      <h1 class="text-3xl font-bold mb-2">Đăng Nhập</h1>
      <p class="text-gray-400">Chào mừng trở lại! Hãy trải nghiệm âm nhạc.</p>
    </div>
    
    <div v-if="error" class="error-alert">{{ error }}</div>

    <form @submit.prevent="handleLogin" class="space-y-4">
      <BaseInput 
        v-model="email" 
        type="email" 
        label="Email" 
        placeholder="Nhập email của bạn" 
        required 
      />
      
      <BaseInput 
        v-model="password" 
        type="password" 
        label="Mật khẩu" 
        placeholder="Nhập mật khẩu" 
        required 
      />
      
      <div class="pt-2">
        <BaseButton type="submit" variant="primary" :loading="loading">
          Đăng Nhập
        </BaseButton>
      </div>
    </form>
    
    <div class="mt-6 text-center text-sm">
      <p class="text-gray-400">
        Chưa có tài khoản? 
        <router-link to="/register" class="text-primary hover:underline">Đăng ký ngay</router-link>
      </p>
    </div>
  </div>
</template>

<style scoped>
.auth-form-container {
  width: 100%;
}
.error-alert {
  background: rgba(244, 63, 94, 0.1);
  border: 1px solid rgba(244, 63, 94, 0.2);
  color: #f43f5e;
  padding: 0.75rem 1rem;
  border-radius: var(--radius-md);
  margin-bottom: 1.5rem;
  font-size: 0.875rem;
}
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
.text-primary {
  color: var(--color-primary);
}
.hover\:underline:hover { text-decoration: underline; }
</style>
