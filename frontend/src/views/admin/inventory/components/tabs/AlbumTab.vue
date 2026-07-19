<script setup lang="ts">
import { ref } from 'vue';
import { useAlbumStore } from '@/stores/albumStore';
import BaseAdminButton from '@/components/admin/ui/button/BaseAdminButton.vue';
import { IconDisc, IconFilter, IconEdit, IconTrash } from '@tabler/icons-vue';

const albumStore = useAlbumStore();
const emit = defineEmits(['openUpload', 'editAlbum']);

albumStore.fetchAlbums();

const formatDate = (dateString: string) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('en-GB'); 
};

const handleDelete = async (album: any) => {
  if (confirm(`Bạn có chắc chắn muốn xóa album "${album.title}"? Các bài hát trong album sẽ bị gỡ ra thành bài hát lẻ.`)) {
    await albumStore.deleteAlbum(album.id);
  }
};
</script>

<template>
  <div class="h-full flex flex-col bg-white rounded-xl shadow-sm border border-slate-200">
    <!-- Toolbar -->
    <div class="p-4 border-b border-slate-200 flex items-center justify-between gap-4">
      <div class="flex items-center gap-3 flex-1">
        <div class="relative w-64">
          <input 
            type="text" 
            placeholder="Tìm kiếm album..." 
            class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-admin-primary outline-none"
          />
          <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        <button class="px-3 py-2 bg-slate-50 border border-slate-200 text-slate-700 rounded-lg text-sm hover:bg-slate-100 flex items-center gap-2">
          <IconFilter size="16" /> Lọc
        </button>
      </div>

      <BaseAdminButton variant="primary" :icon="IconDisc" @click="emit('openUpload')">
        Tạo Album Mới
      </BaseAdminButton>
    </div>

    <!-- Data Table -->
    <div class="flex-1 overflow-auto">
      <table class="w-full text-left border-collapse min-w-[800px]">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-200">
            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase w-10">ID</th>
            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Album</th>
            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Nghệ Sĩ</th>
            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Loại</th>
            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Số bài</th>
            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase text-right">Thao tác</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-if="albumStore.isLoading && albumStore.albums.length === 0">
            <td colspan="6" class="px-4 py-8 text-center text-slate-500">Đang tải dữ liệu...</td>
          </tr>
          <tr v-else-if="albumStore.albums.length === 0">
            <td colspan="6" class="px-4 py-12 text-center">
              <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-3">
                <IconDisc size="32" />
              </div>
              <h3 class="text-sm font-bold text-slate-900 mb-1">Chưa có Album nào</h3>
              <BaseAdminButton variant="primary" @click="emit('openUpload')" class="mt-4">
                Tạo Album Ngay
              </BaseAdminButton>
            </td>
          </tr>
          <tr v-for="album in albumStore.albums" :key="album.id" class="hover:bg-slate-50">
            <td class="px-4 py-3 text-sm text-slate-500">#{{ album.id }}</td>
            <td class="px-4 py-3">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-md bg-slate-200 overflow-hidden shrink-0">
                  <img v-if="album.cover_image" :src="album.cover_image" class="w-full h-full object-cover" />
                  <div v-else class="w-full h-full bg-indigo-100 flex items-center justify-center text-indigo-400">
                    <IconDisc size="20" />
                  </div>
                </div>
                <div>
                  <div class="font-medium text-slate-900">{{ album.title }}</div>
                  <div class="text-[11px] text-slate-500 uppercase mt-0.5 tracking-wider">{{ formatDate(album.release_date) }}</div>
                </div>
              </div>
            </td>
            <td class="px-4 py-3">
              <span class="text-sm text-slate-700">{{ album.artist?.artistProfile?.stage_name || album.artist?.name || 'Unknown' }}</span>
            </td>
            <td class="px-4 py-3">
              <span class="px-2 py-1 bg-slate-100 text-slate-600 text-xs rounded-md font-medium">{{ album.type || 'Album' }}</span>
            </td>
            <td class="px-4 py-3">
              <span class="text-sm font-medium text-slate-900">{{ album.songs_count || 0 }}</span>
            </td>
            <td class="px-4 py-3 text-right">
              <div class="flex justify-end gap-2">
                <button @click="emit('editAlbum', album)" class="p-1.5 text-slate-400 hover:text-admin-primary bg-slate-50 hover:bg-admin-primary/10 rounded">
                  <IconEdit size="18" />
                </button>
                <button @click="handleDelete(album)" class="p-1.5 text-slate-400 hover:text-rose-500 bg-slate-50 hover:bg-rose-50 rounded">
                  <IconTrash size="18" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
