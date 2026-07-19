<script setup lang="ts">
import { ref } from 'vue';
import SongTab from './components/tabs/SongTab.vue';
import AlbumTab from './components/tabs/AlbumTab.vue';
import UploadDrawer from '@/components/admin/features/inventory/UploadDrawer.vue';
import { IconMusic, IconDisc } from '@tabler/icons-vue';

const activeTab = ref('songs');
const isUploadDrawerOpen = ref(false);

const openUploadDrawer = () => {
  isUploadDrawerOpen.value = true;
};
</script>

<template>
  <div class="h-full flex flex-col p-6 max-w-[1400px] mx-auto w-full">
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">
        {{ $t('admin.menu.inventory', 'Kho Nhạc & Album') }}
      </h1>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        Quản lý tất cả tài sản âm thanh, trực tiếp tải lên nhạc và tạo album không cần qua kiểm duyệt.
      </p>
    </div>

    <!-- Tabs Header -->
    <div class="flex items-center gap-1 mb-6 p-1 bg-slate-100/50 dark:bg-slate-800/50 rounded-xl w-fit border border-slate-200 dark:border-slate-800">
      <button 
        @click="activeTab = 'songs'"
        class="flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-200"
        :class="activeTab === 'songs' ? 'bg-white dark:bg-slate-700 text-admin-primary shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
      >
        <IconMusic size="18" /> Bài Hát
      </button>
      <button 
        @click="activeTab = 'albums'"
        class="flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-200"
        :class="activeTab === 'albums' ? 'bg-white dark:bg-slate-700 text-admin-primary shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
      >
        <IconDisc size="18" /> Albums
      </button>
    </div>

    <!-- Tab Content -->
    <div class="flex-1 min-h-0">
      <Transition name="fade" mode="out-in">
        <SongTab v-if="activeTab === 'songs'" @openUpload="openUploadDrawer" />
        <AlbumTab v-else-if="activeTab === 'albums'" />
      </Transition>
    </div>

    <!-- Modals / Drawers -->
    <UploadDrawer v-model:is-open="isUploadDrawerOpen" />
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(4px);
}
</style>
