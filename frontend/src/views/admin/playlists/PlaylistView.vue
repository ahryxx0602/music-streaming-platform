<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { usePlaylistStore } from '@/stores/playlistStore';
import PlaylistTable from '@/components/admin/features/playlists/PlaylistTable.vue';
import PlaylistBuilder from '@/components/admin/features/playlists/PlaylistBuilder.vue';
import BaseAdminButton from '@/components/admin/ui/button/BaseAdminButton.vue';
import { IconPlaylist, IconPlus } from '@tabler/icons-vue';

const playlistStore = usePlaylistStore();
const isBuilding = ref(false);
const selectedPlaylist = ref<any>(null);

onMounted(() => {
  playlistStore.fetchPlaylists();
});

const openCreate = () => {
  selectedPlaylist.value = null;
  isBuilding.value = true;
};

const openEdit = (playlist: any) => {
  selectedPlaylist.value = playlist;
  isBuilding.value = true;
};

const handleSuccess = () => {
  isBuilding.value = false;
  selectedPlaylist.value = null;
};
</script>

<template>
  <div class="h-full flex flex-col p-6 max-w-[1400px] mx-auto w-full">
    <!-- View Mode -->
    <div v-if="!isBuilding" class="flex flex-col h-full">
      <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 mb-1">
            {{ $t('admin.playlists.title', 'Quản lý Danh sách phát') }}
          </h1>
          <p class="text-sm text-slate-500">
            {{ $t('admin.playlists.subtitle', 'Quản lý các Playlist hệ thống (Trending, Top 100, Chủ đề...)') }}
          </p>
        </div>
        
        <BaseAdminButton variant="primary" :icon="IconPlus" @click="openCreate">
          {{ $t('admin.playlists.create') }}
        </BaseAdminButton>
      </div>

      <div class="flex-1 min-h-0">
        <PlaylistTable @edit="openEdit" />
      </div>
    </div>

    <!-- Build Mode -->
    <div v-else class="h-full">
      <PlaylistBuilder 
        :initial-data="selectedPlaylist" 
        @success="handleSuccess"
        @cancel="isBuilding = false"
      />
    </div>
  </div>
</template>
