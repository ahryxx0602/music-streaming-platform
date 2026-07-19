<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/authStore';
import BaseInput from '../../components/base/BaseInput.vue';
import BaseButton from '../../components/base/BaseButton.vue';
import { IconMail, IconLock, IconLogin, IconPlayerPlay } from '@tabler/icons-vue';

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
    
    const role = authStore.role?.toLowerCase();
    if (role === 'admin') {
      router.push('/admin');
    } else if (role === 'artist') {
      router.push('/artist');
    } else {
      router.push('/'); 
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
    <!-- Brand Identity -->
    <div class="brand-header flex flex-col items-center justify-center mb-6">
      <div class="brand-logo w-12 h-12 rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 flex items-center justify-center mb-2 shadow-lg shadow-blue-500/30">
        <IconPlayerPlay class="text-white fill-white" size="24" stroke-width="1.5" />
      </div>
      <h2 class="text-xl font-bold tracking-wide text-gray-200">AuroraStream</h2>
    </div>

    <div class="text-center mb-8">
      <h1 class="text-3xl font-bold mb-2">{{ $t('auth.login') }}</h1>
      <p class="subtitle-text">{{ $t('auth.welcome_back') }}</p>
    </div>
    
    <div v-if="error" class="error-alert">{{ error }}</div>

    <form @submit.prevent="handleLogin" class="space-y-4" novalidate>
      <BaseInput 
        v-model="email" 
        type="email" 
        :label="$t('auth.email')" 
        :placeholder="$t('auth.ph_email')" 
        :icon="IconMail"
        :error="!!error"
        required 
      />
      
      <BaseInput 
        v-model="password" 
        type="password" 
        :label="$t('auth.password')" 
        :placeholder="$t('auth.ph_password')" 
        :icon="IconLock"
        :error="!!error"
        required 
      />

      <div class="flex justify-end mt-1 mb-2">
        <router-link to="/forgot-password" class="cta-link text-sm font-normal">{{ $t('auth.forgot_password') }}</router-link>
      </div>
      
      <div class="pt-2">
        <BaseButton type="submit" variant="primary" :loading="loading" :icon="IconLogin">
          {{ $t('auth.login') }}
        </BaseButton>
      </div>
    </form>
    
    <div class="mt-6 text-center text-sm">
      <p class="subtitle-text">
        {{ $t('auth.no_account') }} 
        <router-link to="/register" class="cta-link">{{ $t('auth.register_now') }}</router-link>
      </p>
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
