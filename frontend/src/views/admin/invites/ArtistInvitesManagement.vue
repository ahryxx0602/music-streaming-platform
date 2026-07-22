<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useArtistInviteStore } from '@/stores/artistInviteStore';
import InviteList from '@/components/admin/features/invites/InviteList.vue';
import CreateInviteModal from '@/components/admin/features/invites/CreateInviteModal.vue';
import BaseAdminButton from '@/components/admin/ui/button/BaseAdminButton.vue';
import { IconMailFast, IconPlus } from '@tabler/icons-vue';

const inviteStore = useArtistInviteStore();
const isCreateOpen = ref(false);

onMounted(() => {
 inviteStore.fetchInvites();
});
</script>

<template>
 <div class="h-full flex flex-col p-6 max-w-[1400px] mx-auto w-full">
 <!-- Header -->
 <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
 <div>
 <h1 class="text-2xl font-bold text-theme-text mb-1">
 {{ $t('admin.invites.title') }}
 </h1>
 <p class="text-sm text-theme-text-sec">
 {{ $t('admin.invites.subtitle') }}
 </p>
 </div>
 
 <BaseAdminButton variant="primary" :icon="IconPlus" @click="isCreateOpen = true">
 {{ $t('admin.invites.create_new') }}
 </BaseAdminButton>
 </div>

 <!-- Hướng dẫn (Tuỳ chọn) -->
 <div class="mb-6 bg-theme-surface-hover border border-theme-border rounded-xl p-4 flex gap-3 text-sm text-theme-text-sec">
 <IconMailFast size="20" class="text-theme-primary shrink-0" />
 <p>Nghệ sĩ chỉ có thể đăng ký tài khoản vào hệ thống thông qua các <strong>Mã mời hợp lệ (Registration Link)</strong> được tạo từ trang này. Admin có quyền thu hồi mã nếu bị lộ.</p>
 </div>

 <!-- Content -->
 <div class="flex-1 min-h-0">
 <InviteList />
 </div>

 <!-- Modal Tạo Mã -->
 <CreateInviteModal v-model:is-open="isCreateOpen" />
 </div>
</template>
