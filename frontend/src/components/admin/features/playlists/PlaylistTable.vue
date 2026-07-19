<script setup lang="ts">
import { usePlaylistStore } from '@/stores/playlistStore';
import { IconPlaylist, IconEdit, IconTrash } from '@tabler/icons-vue';

const playlistStore = usePlaylistStore();
const emit = defineEmits(['edit']);

const handleDelete = async (playlist: any) => {
  if (confirm(`Bạn có chắc chắn muốn xóa playlist "${playlist.title}"?`)) {
    await playlistStore.deletePlaylist(playlist.id);
  }
};
</script>

<template>
  <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col h-full">
    <div class="flex-1 overflow-auto">
      <table class="w-full text-left border-collapse min-w-[800px]">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-200 sticky top-0 z-10">
            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider w-12">ID</th>
            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Playlist</th>
            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Số bài</th>
            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Trạng thái</th>
            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Thao tác</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-if="playlistStore.isLoading && playlistStore.playlists.length === 0">
            <td colspan="5" class="px-4 py-8 text-center text-slate-500">Đang tải dữ liệu...</td>
          </tr>
          <tr v-else-if="playlistStore.playlists.length === 0">
            <td colspan="5" class="px-4 py-16 text-center">
              <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-3">
                <IconPlaylist size="32" />
              </div>
              <h3 class="text-sm font-bold text-slate-700 mb-1">Chưa có Playlist nào</h3>
              <p class="text-xs text-slate-500">Hãy tạo các Playlist hệ thống để đề xuất cho người dùng.</p>
            </td>
          </tr>
          
          <tr v-for="playlist in playlistStore.playlists" :key="playlist.id" class="hover:bg-slate-50 transition-colors">
            <td class="px-4 py-3 text-sm text-slate-500">#{{ playlist.id }}</td>
            <td class="px-4 py-3">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-md bg-slate-200 overflow-hidden shrink-0">
                  <img v-if="playlist.cover_image" :src="playlist.cover_image" class="w-full h-full object-cover" />
                  <div v-else class="w-full h-full bg-indigo-100 flex items-center justify-center text-indigo-400">
                    <IconPlaylist size="20" />
                  </div>
                </div>
                <div>
                  <div class="font-medium text-slate-900">{{ playlist.title }}</div>
                  <div class="text-[11px] text-slate-500 mt-0.5 truncate max-w-xs">{{ playlist.description || '---' }}</div>
                </div>
              </div>
            </td>
            <td class="px-4 py-3 text-sm font-medium text-slate-700">
              {{ playlist.songs_count || 0 }} bài
            </td>
            <td class="px-4 py-3">
              <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-[11px] font-bold rounded-full uppercase tracking-wider">Public</span>
            </td>
            <td class="px-4 py-3 text-right">
              <div class="flex justify-end gap-2">
                <button @click="emit('edit', playlist)" class="p-1.5 text-slate-400 hover:text-admin-primary bg-slate-50 hover:bg-admin-primary/10 rounded">
                  <IconEdit size="18" />
                </button>
                <button @click="handleDelete(playlist)" class="p-1.5 text-slate-400 hover:text-rose-500 bg-slate-50 hover:bg-rose-50 rounded">
                  <IconTrash size="18" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="p-3 border-t border-slate-200 flex items-center justify-between text-sm text-slate-500 bg-slate-50 shrink-0">
      <div>Hiển thị <span class="font-bold text-slate-900">{{ playlistStore.playlists.length }}</span> playlist</div>
      <div class="flex items-center gap-1">
        <button 
          :disabled="playlistStore.meta.current_page === 1"
          @click="playlistStore.fetchPlaylists(playlistStore.meta.current_page - 1)"
          class="px-3 py-1.5 border border-slate-200 rounded-lg hover:bg-white disabled:opacity-50 transition-colors"
        >Trước</button>
        <span class="px-3">Trang {{ playlistStore.meta.current_page }} / {{ playlistStore.meta.last_page }}</span>
        <button 
          :disabled="playlistStore.meta.current_page === playlistStore.meta.last_page"
          @click="playlistStore.fetchPlaylists(playlistStore.meta.current_page + 1)"
          class="px-3 py-1.5 border border-slate-200 rounded-lg hover:bg-white disabled:opacity-50 transition-colors"
        >Sau</button>
      </div>
    </div>
  </div>
</template>
