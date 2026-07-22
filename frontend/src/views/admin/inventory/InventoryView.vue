<script setup lang="ts">
import { ref } from 'vue';
import SongTab from './components/tabs/SongTab.vue';
import AlbumTab from './components/tabs/AlbumTab.vue';
import UploadDrawer from '@/components/admin/features/inventory/UploadDrawer.vue';
import AlbumDrawer from '@/components/admin/features/inventory/AlbumDrawer.vue';
import { IconMusic, IconDisc } from '@tabler/icons-vue';

const activeTab = ref('songs');
const isUploadDrawerOpen = ref(false);
const isAlbumDrawerOpen = ref(false);
const selectedAlbum = ref<any>(null);

const openUploadDrawer = () => {
 isUploadDrawerOpen.value = true;
};

const openAlbumDrawer = (album: any = null) => {
 selectedAlbum.value = album;
 isAlbumDrawerOpen.value = true;
};
</script>

<template>
 <div class="h-full flex flex-col p-6 max-w-[1400px] mx-auto w-full">
 <!-- Header -->
 <div class="mb-6">
 <h1 class="text-2xl font-bold text-theme-text mb-1">
 {{ $t('admin.menu.inventory') }}
 </h1>
 <p class="text-sm text-theme-text-sec">
 {{ $t('admin.inventory.subtitle') || 'Quản lý tất cả tài sản âm thanh, trực tiếp tải lên nhạc và tạo album không cần qua kiểm duyệt.' }}
 </p>
 </div>

 <!-- Tabs Header -->
 <div class="flex items-center gap-1 mb-6 p-1 bg-theme-surface-hover/50 rounded-xl w-fit border border-theme-border">
 <button 
 @click="activeTab = 'songs'"
 class="flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 cursor-pointer"
 :class="activeTab === 'songs' ? 'bg-theme-surface text-theme-primary shadow-[var(--shadow-glow)]' : 'text-theme-text-sec hover:text-theme-text'"
 >
 <IconMusic size="18" /> {{ $t('admin.menu.tracks') }}
 </button>
 <button 
 @click="activeTab = 'albums'"
 class="flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 cursor-pointer"
 :class="activeTab === 'albums' ? 'bg-theme-surface text-theme-primary shadow-[var(--shadow-glow)]' : 'text-theme-text-sec hover:text-theme-text'"
 >
 <IconDisc size="18" /> {{ $t('admin.menu.albums') }}
 </button>
 </div>

 <!-- Tab Content -->
 <div class="flex-1 min-h-0">
 <Transition name="fade" mode="out-in">
 <SongTab v-if="activeTab === 'songs'" @openUpload="openUploadDrawer" />
 <AlbumTab v-else-if="activeTab === 'albums'" @openUpload="openAlbumDrawer" @editAlbum="openAlbumDrawer" />
 </Transition>
 </div>

 <!-- Modals / Drawers -->
 <UploadDrawer v-model:is-open="isUploadDrawerOpen" />
 <AlbumDrawer v-model:is-open="isAlbumDrawerOpen" :album-data="selectedAlbum" />
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
