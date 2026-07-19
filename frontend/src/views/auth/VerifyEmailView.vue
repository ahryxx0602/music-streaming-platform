<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRoute, useRouter } from 'vue-router';
import api from '../../services/api';
import BaseButton from '../../components/base/BaseButton.vue';
import { IconCheck, IconX, IconLoader2, IconPlayerPlay, IconHome } from '@tabler/icons-vue';
const { t } = useI18n();

const route = useRoute();
const router = useRouter();

const status = ref<'loading' | 'success' | 'error'>('loading');
const message = ref(t('auth.verifying_email'));

onMounted(async () => {
  const id = route.params.id;
  const hash = route.params.hash;
  const expires = route.query.expires;
  const signature = route.query.signature;
  
  if (!id || !hash || !expires || !signature) {
    status.value = 'error';
    message.value = t('auth.verify_invalid_link');
    return;
  }
  
  try {
    // API-006 Verify URL logic
    const verifyUrl = `/guest/auth/email/verify/${id}/${hash}?expires=${expires}&signature=${signature}`;
    const res = await api.get(verifyUrl);
    
    status.value = 'success';
    message.value = res.data?.message || t('auth.verify_success');
  } catch (err: any) {
    status.value = 'error';
    message.value = err.response?.data?.message || t('auth.verify_failed');
  }
});
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

    <div class="mb-8">
      <h1 class="text-3xl font-bold mb-2">{{ $t('auth.verify_email_title') }}</h1>
    </div>

    <div class="verification-card flex flex-col items-center justify-center py-6 px-4">
      <div v-if="status === 'loading'" class="status-icon loading-icon mb-4">
        <IconLoader2 class="animate-spin text-blue-500" size="48" stroke-width="2" />
      </div>
      
      <div v-else-if="status === 'success'" class="status-icon success-icon mb-4">
        <div class="icon-circle bg-green-500/20 text-green-500">
          <IconCheck size="40" stroke-width="2.5" />
        </div>
      </div>
      
      <div v-else class="status-icon error-icon mb-4">
        <div class="icon-circle bg-red-500/20 text-red-500">
          <IconX size="40" stroke-width="2.5" />
        </div>
      </div>

      <p class="text-lg font-medium mb-6 text-gray-200">{{ message }}</p>

      <div v-if="status !== 'loading'" class="w-full pt-4">
        <BaseButton @click="router.push('/')" variant="primary" :icon="IconHome">
          {{ $t('auth.back_to_home') }}
        </BaseButton>
      </div>
    </div>
  </div>
</template>

<style scoped>
.auth-form-container { width: 100%; }
.verification-card { 
  background: rgba(15, 23, 42, 0.4); 
  border: 1px solid rgba(255, 255, 255, 0.05); 
  border-radius: 16px; 
}

.icon-circle { 
  width: 80px; height: 80px; 
  border-radius: 50%; 
  display: flex; align-items: center; justify-content: center; 
}

/* Tailwind Utilities */
.flex { display: flex; }
.flex-col { flex-direction: column; }
.items-center { align-items: center; }
.justify-center { justify-content: center; }
.mb-2 { margin-bottom: 0.5rem; }
.mb-4 { margin-bottom: 1rem; }
.mb-6 { margin-bottom: 1.5rem; }
.mb-8 { margin-bottom: 2rem; }
.py-6 { padding-top: 1.5rem; padding-bottom: 1.5rem; }
.px-4 { padding-left: 1rem; padding-right: 1rem; }
.pt-4 { padding-top: 1rem; }
.w-full { width: 100%; }
.w-12 { width: 3rem; }
.h-12 { height: 3rem; }
.rounded-xl { border-radius: 0.75rem; }
.bg-gradient-to-tr { background-image: linear-gradient(to top right, var(--tw-gradient-stops)); }
.from-blue-600 { --tw-gradient-from: #2563eb; --tw-gradient-to: rgba(37, 99, 235, 0); --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to); }
.to-cyan-400 { --tw-gradient-to: #22d3ee; }
.bg-green-500\/20 { background-color: rgba(34, 197, 94, 0.2); }
.bg-red-500\/20 { background-color: rgba(239, 68, 68, 0.2); }
.text-white { color: #ffffff; }
.text-blue-500 { color: #3b82f6; }
.text-green-500 { color: #22c55e; }
.text-red-500 { color: #ef4444; }
.text-gray-200 { color: #e5e7eb; }
.fill-white { fill: #ffffff; }
.shadow-lg { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
.shadow-blue-500\/30 { box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3), 0 4px 6px -2px rgba(59, 130, 246, 0.15); }
.text-xl { font-size: 1.25rem; line-height: 1.75rem; }
.text-lg { font-size: 1.125rem; line-height: 1.75rem; }
.text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
.font-bold { font-weight: 700; }
.font-medium { font-weight: 500; }
.tracking-wide { letter-spacing: 0.025em; }
.text-center { text-align: center; }

@keyframes spin { to { transform: rotate(360deg); } }
.animate-spin { animation: spin 1s linear infinite; }
</style>
