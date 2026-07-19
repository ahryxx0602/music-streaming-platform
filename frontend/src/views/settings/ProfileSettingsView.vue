<script setup lang="ts">
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useAuthStore } from '@/stores/authStore';
import api from '@/services/api';
import BaseInput from '@/components/base/BaseInput.vue';
import BaseButton from '@/components/base/BaseButton.vue';
import { IconUpload, IconDeviceFloppy } from '@tabler/icons-vue';
const { t } = useI18n();

const authStore = useAuthStore();
const user = computed(() => authStore.user);
const role = computed(() => authStore.role?.toLowerCase() || 'listener');

const form = ref({
  name: user.value?.name || '',
  stage_name: user.value?.artist_profile?.stage_name || '',
  bio: user.value?.artist_profile?.bio || '',
});

const fileInput = ref<HTMLInputElement | null>(null);
const avatarPreview = ref(user.value?.avatar_url || user.value?.avatar || null);
const selectedFile = ref<File | null>(null);

const loadingInfo = ref(false);
const loadingAvatar = ref(false);
const messageInfo = ref('');
const errorInfo = ref('');
const messageAvatar = ref('');
const errorAvatar = ref('');

const triggerFileInput = () => {
  fileInput.value?.click();
};

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    const file = target.files[0];
    
    if (!file) return;

    // Validate File Size (Max 2MB)
    if (file.size > 2 * 1024 * 1024) {
      errorAvatar.value = t('settings.profile.avatar_too_large');
      return;
    }
    errorAvatar.value = '';
    
    selectedFile.value = file;
    // Create preview
    const reader = new FileReader();
    reader.onload = (e) => {
      avatarPreview.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
  }
};

const uploadAvatar = async () => {
  if (!selectedFile.value) return;
  
  loadingAvatar.value = true;
  errorAvatar.value = '';
  messageAvatar.value = '';
  
  const formData = new FormData();
  formData.append('avatar', selectedFile.value);
  
  try {
    const res = await api.post(`/${role.value}/profile/avatar`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    messageAvatar.value = t('settings.profile.avatar_success');
    // Update store
    if (authStore.user) {
      authStore.user.avatar = res.data.data.user.avatar;
      authStore.user.avatar_url = res.data.data.user.avatar_url;
    }
  } catch (err: any) {
    errorAvatar.value = err.response?.data?.message || t('settings.profile.avatar_error');
  } finally {
    loadingAvatar.value = false;
  }
};

const saveProfileInfo = async () => {
  loadingInfo.value = true;
  errorInfo.value = '';
  messageInfo.value = '';
  
  try {
    const res = await api.put(`/${role.value}/profile`, form.value);
    messageInfo.value = t('settings.profile.info_success');
    if (authStore.user) {
      authStore.user.name = res.data.data.user.name;
      if (res.data.data.user.artist_profile) {
        authStore.user.artist_profile = res.data.data.user.artist_profile;
      }
    }
  } catch (err: any) {
    errorInfo.value = err.response?.data?.message || t('settings.profile.info_error');
  } finally {
    loadingInfo.value = false;
  }
};
</script>

<template>
  <div class="profile-settings">
    <h2 class="text-2xl font-bold text-theme-text mb-6">Hồ sơ cá nhân</h2>
    
    <!-- Avatar Section -->
    <section class="mb-10 pb-8 border-b border-theme-border">
      <h3 class="text-lg font-medium text-theme-text-sec mb-4">{{ $t('settings.profile.avatar_title') }}</h3>
      <div class="flex items-center gap-6">
        <div class="relative w-24 h-24 rounded-full overflow-hidden bg-theme-bg border-2 border-theme-border flex-shrink-0">
          <img v-if="avatarPreview" :src="avatarPreview" alt="Avatar" class="w-full h-full object-cover" />
          <div v-else class="w-full h-full flex items-center justify-center text-theme-text-sec font-bold text-2xl">
            {{ user?.name?.charAt(0).toUpperCase() }}
          </div>
        </div>
        
        <div class="flex flex-col gap-2">
          <div class="flex gap-3">
            <input type="file" ref="fileInput" @change="handleFileChange" accept="image/jpeg, image/png, image/webp" class="hidden" />
            <BaseButton @click="triggerFileInput" variant="outline" size="sm">{{ $t('settings.profile.choose_new_avatar') }}</BaseButton>
            <BaseButton v-if="selectedFile" @click="uploadAvatar" :loading="loadingAvatar" variant="primary" size="sm" :icon="IconUpload">Tải lên</BaseButton>
          </div>
          <p class="text-xs text-theme-text-sec">Định dạng JPG, PNG hoặc WebP. Tối đa 2MB.</p>
          <p v-if="messageAvatar" class="text-sm text-green-400 mt-1">{{ messageAvatar }}</p>
          <p v-if="errorAvatar" class="text-sm text-red-400 mt-1">{{ errorAvatar }}</p>
        </div>
      </div>
    </section>

    <!-- Info Section -->
    <section>
      <h3 class="text-lg font-medium text-theme-text-sec mb-4">Thông tin cơ bản</h3>
      <form @submit.prevent="saveProfileInfo" class="space-y-4 max-w-xl">
        <BaseInput v-model="form.name" label="Tên hiển thị" required />
        
        <template v-if="role === 'artist'">
          <BaseInput v-model="form.stage_name" label="Nghệ danh (Stage Name)" required />
          <div class="flex flex-col">
            <label class="text-sm font-medium text-theme-text-sec mb-1">Tiểu sử</label>
            <textarea v-model="form.bio" rows="4" class="w-full bg-theme-bg border border-theme-border rounded-xl p-3 text-theme-text focus:outline-none focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 resize-none" placeholder="Giới thiệu về bạn..."></textarea>
          </div>
        </template>
        
        <p v-if="messageInfo" class="text-sm text-green-400">{{ messageInfo }}</p>
        <p v-if="errorInfo" class="text-sm text-red-400">{{ errorInfo }}</p>
        
        <div class="pt-4">
          <BaseButton type="submit" variant="primary" :loading="loadingInfo" :icon="IconDeviceFloppy">{{ $t('common.save') }}</BaseButton>
        </div>
      </form>
    </section>
  </div>
</template>

<style scoped>
/* Scoped overrides if needed */
</style>
