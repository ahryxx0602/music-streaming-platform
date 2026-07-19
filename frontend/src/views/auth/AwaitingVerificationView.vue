<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/authStore';
import api from '../../services/api';
import BaseButton from '../../components/base/BaseButton.vue';
import { IconMail, IconSend, IconLogout, IconPlayerPlay, IconCheck } from '@tabler/icons-vue';

const router = useRouter();
const authStore = useAuthStore();

const loading = ref(false);
const error = ref('');
const success = ref(false);

const handleResend = async () => {
  error.value = '';
  success.value = false;
  loading.value = true;
  
  try {
    // API-103: Gửi lại email xác thực (Dùng Guest Route và truyền trực tiếp email)
    await api.post('/guest/auth/email/resend', {
      email: authStore.user?.email
    });
    success.value = true;
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Không thể gửi lại email. Vui lòng kiểm tra lại sau.';
  } finally {
    loading.value = false;
  }
};

const handleLogout = () => {
  authStore.logout();
  router.push('/login');
};
</script>

<template>
  <div class="auth-form-container text-center">
    <!-- Brand Identity -->
    <div class="brand-header flex flex-col items-center justify-center mb-6">
      <div class="brand-logo w-12 h-12 rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 flex items-center justify-center mb-2 shadow-lg shadow-blue-500/30">
        <IconPlayerPlay class="text-white fill-white" size="24" stroke-width="1.5" />
      </div>
      <h2 class="text-xl font-bold tracking-wide text-gray-200">AuroraStream</h2>
    </div>

    <div class="mb-6">
      <h1 class="text-3xl font-bold mb-2">{{ $t('auth.awaiting_verification_title') }}</h1>
      <p class="subtitle-text">{{ $t('auth.awaiting_verification_subtitle') }}</p>
    </div>

    <div class="verification-card flex flex-col items-center justify-center p-6 mb-6">
      <div class="icon-circle bg-blue-500/10 text-blue-400 mb-4">
        <IconMail size="48" stroke-width="1.5" />
      </div>
      
      <p class="text-gray-300 mb-4 text-sm leading-relaxed">
        {{ $t('auth.verification_link_sent') }} 
        Vui lòng kiểm tra Hộp thư đến (hoặc Thư rác) và làm theo hướng dẫn.
      </p>

      <div v-if="error" class="error-alert w-full text-left">{{ error }}</div>
      <div v-if="success" class="success-alert w-full flex items-center text-left">
        <IconCheck class="mr-2 flex-shrink-0" size="18" />
        <span class="text-sm">Đã gửi lại! Vui lòng kiểm tra email của bạn.</span>
      </div>

      <div class="w-full pt-2">
        <BaseButton @click="handleResend" variant="primary" :loading="loading" :icon="IconSend">
          {{ $t('auth.resend_verification') }}
        </BaseButton>
      </div>
    </div>

    <div class="text-sm">
      <p class="subtitle-text mb-3">{{ $t('auth.use_different_account') }}</p>
      <BaseButton @click="handleLogout" variant="secondary" :icon="IconLogout">
        Đăng xuất
      </BaseButton>
    </div>
  </div>
</template>

<style scoped>
.auth-form-container { width: 100%; }
.verification-card { 
  background: var(--color-surface, #131B2F); 
  border: 1px solid var(--color-input-border, #2A3B57); 
  border-radius: 16px; 
}

.icon-circle { 
  width: 80px; height: 80px; 
  border-radius: 50%; 
  display: flex; align-items: center; justify-content: center; 
}

.error-alert { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #EF4444; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.875rem; }
.success-alert { background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); color: #22C55E; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.875rem; }

/* Tailwind Utilities */
.flex { display: flex; }
.flex-col { flex-direction: column; }
.items-center { align-items: center; }
.justify-center { justify-content: center; }
.mr-2 { margin-right: 0.5rem; }
.flex-shrink-0 { flex-shrink: 0; }
.mb-2 { margin-bottom: 0.5rem; }
.mb-3 { margin-bottom: 0.75rem; }
.mb-4 { margin-bottom: 1rem; }
.mb-6 { margin-bottom: 1.5rem; }
.p-6 { padding: 1.5rem; }
.pt-2 { padding-top: 0.5rem; }
.w-full { width: 100%; }
.w-12 { width: 3rem; }
.h-12 { height: 3rem; }
.rounded-xl { border-radius: 0.75rem; }
.bg-gradient-to-tr { background-image: linear-gradient(to top right, var(--tw-gradient-stops)); }
.from-blue-600 { --tw-gradient-from: #2563eb; --tw-gradient-to: rgba(37, 99, 235, 0); --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to); }
.to-cyan-400 { --tw-gradient-to: #22d3ee; }
.bg-blue-500\/10 { background-color: rgba(59, 130, 246, 0.1); }
.text-white { color: #ffffff; }
.text-blue-400 { color: #60a5fa; }
.text-gray-200 { color: #e5e7eb; }
.text-gray-300 { color: #d1d5db; }
.fill-white { fill: #ffffff; }
.shadow-lg { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
.shadow-blue-500\/30 { box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3), 0 4px 6px -2px rgba(59, 130, 246, 0.15); }
.text-xl { font-size: 1.25rem; line-height: 1.75rem; }
.text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
.text-sm { font-size: 0.875rem; }
.leading-relaxed { line-height: 1.625; }
.font-bold { font-weight: 700; }
.tracking-wide { letter-spacing: 0.025em; }
.text-center { text-align: center; }
.text-left { text-align: left; }

.subtitle-text { color: var(--color-text-secondary, #CBD5E1); font-weight: 500; }
</style>
