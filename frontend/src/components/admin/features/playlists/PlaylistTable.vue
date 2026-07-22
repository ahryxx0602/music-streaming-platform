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
 <div class="bg-theme-surface rounded-xl shadow-[var(--shadow-glow)] border border-theme-border overflow-hidden flex flex-col h-full">
 <div class="flex-1 overflow-auto">
 <table class="w-full text-left border-collapse min-w-[800px]">
 <thead>
 <tr class="bg-theme-surface-hover border-b border-theme-border sticky top-0 z-10">
 <th class="px-4 py-3 text-xs font-semibold text-theme-text-sec uppercase tracking-wider w-12">ID</th>
 <th class="px-4 py-3 text-xs font-semibold text-theme-text-sec uppercase tracking-wider">Playlist</th>
 <th class="px-4 py-3 text-xs font-semibold text-theme-text-sec uppercase tracking-wider">Số bài</th>
 <th class="px-4 py-3 text-xs font-semibold text-theme-text-sec uppercase tracking-wider">Trạng thái</th>
 <th class="px-4 py-3 text-xs font-semibold text-theme-text-sec uppercase tracking-wider text-right">Thao tác</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-theme-border">
 <tr v-if="playlistStore.isLoading && playlistStore.playlists.length === 0">
 <td colspan="5" class="px-4 py-8 text-center text-theme-text-sec">Đang tải dữ liệu...</td>
 </tr>
 <tr v-else-if="playlistStore.playlists.length === 0">
 <td colspan="5" class="px-4 py-16 text-center">
 <div class="w-16 h-16 bg-theme-surface-hover text-theme-text-sec rounded-full flex items-center justify-center mx-auto mb-3">
 <IconPlaylist size="32" />
 </div>
 <h3 class="text-sm font-bold text-theme-text-sec mb-1">Chưa có Playlist nào</h3>
 <p class="text-xs text-theme-text-sec">Hãy tạo các Playlist hệ thống để đề xuất cho người dùng.</p>
 </td>
 </tr>
 
 <tr v-for="playlist in playlistStore.playlists" :key="playlist.id" class="hover:bg-theme-surface-hover transition-colors">
 <td class="px-4 py-3 text-sm text-theme-text-sec">#{{ playlist.id }}</td>
 <td class="px-4 py-3">
 <div class="flex items-center gap-3">
 <div class="w-10 h-10 rounded-md bg-theme-surface-hover overflow-hidden shrink-0">
 <img v-if="playlist.cover_image" :src="playlist.cover_image" class="w-full h-full object-cover" />
 <div v-else class="w-full h-full bg-theme-primary/15 flex items-center justify-center text-theme-primary">
 <IconPlaylist size="20" />
 </div>
 </div>
 <div>
 <div class="font-medium text-theme-text">{{ playlist.title }}</div>
 <div class="text-[11px] text-theme-text-sec mt-0.5 truncate max-w-xs">{{ playlist.description || '---' }}</div>
 </div>
 </div>
 </td>
 <td class="px-4 py-3 text-sm font-medium text-theme-text-sec">
 {{ playlist.songs_count || 0 }} bài
 </td>
 <td class="px-4 py-3">
 <span class="px-2 py-1 bg-theme-success/15 text-theme-success text-[11px] font-bold rounded-full uppercase tracking-wider">Public</span>
 </td>
 <td class="px-4 py-3 text-right">
 <div class="flex justify-end gap-2">
 <button @click="emit('edit', playlist)" class="p-1.5 text-theme-text-sec hover:text-theme-primary bg-theme-surface-hover hover:bg-theme-primary/10 rounded">
 <IconEdit size="18" />
 </button>
 <button @click="handleDelete(playlist)" class="p-1.5 text-theme-text-sec hover:text-theme-danger bg-theme-surface-hover hover:bg-theme-danger/10 rounded">
 <IconTrash size="18" />
 </button>
 </div>
 </td>
 </tr>
 </tbody>
 </table>
 </div>

 <!-- Pagination -->
 <div class="p-3 border-t border-theme-border flex items-center justify-between text-sm text-theme-text-sec bg-theme-surface-hover shrink-0">
 <div>Hiển thị <span class="font-bold text-theme-text">{{ playlistStore.playlists.length }}</span> playlist</div>
 <div class="flex items-center gap-1">
 <button 
 :disabled="playlistStore.meta.current_page === 1"
 @click="playlistStore.fetchPlaylists(playlistStore.meta.current_page - 1)"
 class="px-3 py-1.5 border border-theme-border rounded-lg hover:bg-theme-surface disabled:opacity-50 transition-colors"
 >Trước</button>
 <span class="px-3">Trang {{ playlistStore.meta.current_page }} / {{ playlistStore.meta.last_page }}</span>
 <button 
 :disabled="playlistStore.meta.current_page === playlistStore.meta.last_page"
 @click="playlistStore.fetchPlaylists(playlistStore.meta.current_page + 1)"
 class="px-3 py-1.5 border border-theme-border rounded-lg hover:bg-theme-surface disabled:opacity-50 transition-colors"
 >Sau</button>
 </div>
 </div>
 </div>
</template>
