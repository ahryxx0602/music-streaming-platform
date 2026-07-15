<script setup lang="ts">
import { ref, computed } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import api from '@/services/api';
import BaseInput from '@/components/base/BaseInput.vue';
import BaseButton from '@/components/base/BaseButton.vue';
import { IconUpload, IconDeviceFloppy } from '@tabler/icons-vue';

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
      errorAvatar.value = 'Kích thước ảnh vượt quá 2MB. Vui lòng chọn ảnh nhẹ hơn.';
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
    messageAvatar.value = 'Cập nhật ảnh đại diện thành công!';
    // Update store
    if (authStore.user) {
      authStore.user.avatar = res.data.data.user.avatar;
      authStore.user.avatar_url = res.data.data.user.avatar_url;
    }
  } catch (err: any) {
    errorAvatar.value = err.response?.data?.message || 'Có lỗi xảy ra khi upload ảnh.';
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
    messageInfo.value = 'Lưu thông tin thành công!';
    if (authStore.user) {
      authStore.user.name = res.data.data.user.name;
      if (res.data.data.user.artist_profile) {
        authStore.user.artist_profile = res.data.data.user.artist_profile;
      }
    }
  } catch (err: any) {
    errorInfo.value = err.response?.data?.message || 'Có lỗi xảy ra khi lưu thông tin.';
  } finally {
    loadingInfo.value = false;
  }
};
</script>

<template>
  <div class="profile-settings">
    <h2 class="text-2xl font-bold text-white mb-6">Hồ sơ cá nhân</h2>
    
    <!-- Avatar Section -->
    <section class="mb-10 pb-8 border-b theme-border">
      <h3 class="text-lg font-medium text-secondary mb-4">Ảnh đại diện</h3>
      <div class="flex items-center gap-6">
        <div class="relative w-24 h-24 rounded-full overflow-hidden theme-card border-2 theme-border flex-shrink-0">
          <img v-if="avatarPreview" :src="avatarPreview" alt="Avatar" class="w-full h-full object-cover" />
          <div v-else class="w-full h-full flex items-center justify-center text-secondary font-bold text-2xl">
            {{ user?.name?.charAt(0).toUpperCase() }}
          </div>
        </div>
        
        <div class="flex flex-col gap-2">
          <div class="flex gap-3">
            <input type="file" ref="fileInput" @change="handleFileChange" accept="image/jpeg, image/png, image/webp" class="hidden" />
            <BaseButton @click="triggerFileInput" variant="outline" size="sm">Chọn ảnh mới</BaseButton>
            <BaseButton v-if="selectedFile" @click="uploadAvatar" :loading="loadingAvatar" variant="primary" size="sm" :icon="IconUpload">Tải lên</BaseButton>
          </div>
          <p class="text-xs text-slate-400">Định dạng JPG, PNG hoặc WebP. Tối đa 2MB.</p>
          <p v-if="messageAvatar" class="text-sm text-green-400 mt-1">{{ messageAvatar }}</p>
          <p v-if="errorAvatar" class="text-sm text-red-400 mt-1">{{ errorAvatar }}</p>
        </div>
      </div>
    </section>

    <!-- Info Section -->
    <section>
      <h3 class="text-lg font-medium text-secondary mb-4">Thông tin cơ bản</h3>
      <form @submit.prevent="saveProfileInfo" class="space-y-4 max-w-xl">
        <BaseInput v-model="form.name" label="Tên hiển thị" required />
        
        <template v-if="role === 'artist'">
          <BaseInput v-model="form.stage_name" label="Nghệ danh (Stage Name)" required />
          <div class="flex flex-col">
            <label class="text-sm font-medium text-secondary mb-1">Tiểu sử</label>
            <textarea v-model="form.bio" rows="4" class="w-full theme-card border theme-border rounded-xl p-3 text-white focus:outline-none focus-border-primary resize-none" placeholder="Giới thiệu về bạn..."></textarea>
          </div>
        </template>
        
        <p v-if="messageInfo" class="text-sm text-green-400">{{ messageInfo }}</p>
        <p v-if="errorInfo" class="text-sm text-red-400">{{ errorInfo }}</p>
        
        <div class="pt-4">
          <BaseButton type="submit" variant="primary" :loading="loadingInfo" :icon="IconDeviceFloppy">Lưu thay đổi</BaseButton>
        </div>
      </form>
    </section>
  </div>
</template>

<style scoped>
.theme-card { background-color: var(--color-card, #1A2740); }
.theme-border { border-color: var(--color-input-border, #2A3B57); }
.text-secondary { color: var(--color-text-secondary, #CBD5E1); }
.focus-border-primary:focus { 
  border-color: var(--color-primary, #3B82F6); 
  box-shadow: var(--shadow-input-focus, 0 0 18px rgba(59, 130, 246, 0.18)); 
}
</style>
