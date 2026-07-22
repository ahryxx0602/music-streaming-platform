<script setup lang="ts">
import { useModerationStore } from '@/stores/moderationStore';
import { IconMusic, IconCheck, IconX, IconEye } from '@tabler/icons-vue';

const moderationStore = useModerationStore();
const emit = defineEmits(['preview']);

const formatDate = (dateString: string) => {
 if (!dateString) return '';
 return new Date(dateString).toLocaleString('vi-VN'); 
};
</script>

<template>
 <div class="bg-theme-surface rounded-xl shadow-[var(--shadow-glow)] border border-theme-border overflow-hidden flex flex-col h-full">
 <div class="flex-1 overflow-auto">
 <table class="w-full text-left border-collapse min-w-[800px]">
 <thead>
 <tr class="bg-theme-surface-hover border-b border-theme-border sticky top-0 z-10">
 <th class="px-4 py-3 text-xs font-semibold text-theme-text-sec uppercase tracking-wider w-12">ID</th>
 <th class="px-4 py-3 text-xs font-semibold text-theme-text-sec uppercase tracking-wider">Bài Hát</th>
 <th class="px-4 py-3 text-xs font-semibold text-theme-text-sec uppercase tracking-wider">Nghệ Sĩ</th>
 <th class="px-4 py-3 text-xs font-semibold text-theme-text-sec uppercase tracking-wider">Ngày Tải Lên</th>
 <th class="px-4 py-3 text-xs font-semibold text-theme-text-sec uppercase tracking-wider text-right">Thao tác</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-theme-border">
 <tr v-if="moderationStore.isLoading && moderationStore.pendingSongs.length === 0">
 <td colspan="5" class="px-4 py-8 text-center text-theme-text-sec">Đang tải dữ liệu...</td>
 </tr>
 <tr v-else-if="moderationStore.pendingSongs.length === 0">
 <td colspan="5" class="px-4 py-16 text-center">
 <div class="w-16 h-16 bg-theme-surface-hover text-theme-text-sec rounded-full flex items-center justify-center mx-auto mb-3">
 <IconCheck size="32" />
 </div>
 <h3 class="text-sm font-bold text-theme-text-sec mb-1">Không có bài hát nào chờ duyệt</h3>
 <p class="text-xs text-theme-text-sec">Tất cả các bài hát đã được xử lý.</p>
 </td>
 </tr>
 
 <tr v-for="song in moderationStore.pendingSongs" :key="song.id" class="hover:bg-theme-surface-hover transition-colors group">
 <td class="px-4 py-3 text-sm text-theme-text-sec">#{{ song.id }}</td>
 <td class="px-4 py-3">
 <div class="flex items-center gap-3">
 <div class="w-10 h-10 rounded-md bg-theme-primary/10 text-theme-primary flex items-center justify-center shrink-0">
 <IconMusic size="20" />
 </div>
 <div>
 <div class="font-medium text-theme-text line-clamp-1">{{ song.title }}</div>
 <div class="text-[11px] text-theme-text-sec mt-0.5">{{ song.genre?.name || '---' }}</div>
 </div>
 </div>
 </td>
 <td class="px-4 py-3">
 <div class="flex items-center gap-2">
 <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold text-theme-text-sec">
 {{ (song.artist?.artistProfile?.stage_name || song.artist?.name || 'U').charAt(0).toUpperCase() }}
 </div>
 <span class="text-sm font-medium text-theme-text-sec">{{ song.artist?.artistProfile?.stage_name || song.artist?.name || 'Unknown' }}</span>
 </div>
 </td>
 <td class="px-4 py-3">
 <span class="text-sm text-theme-text-sec">{{ formatDate(song.created_at) }}</span>
 </td>
 <td class="px-4 py-3 text-right">
 <button 
 @click="emit('preview', song)" 
 class="px-3 py-1.5 bg-theme-primary/10 text-theme-primary hover:bg-theme-primary hover:text-white rounded-lg text-sm font-medium transition-colors flex items-center gap-1.5 ml-auto"
 >
 <IconEye size="16" />
 Kiểm duyệt
 </button>
 </td>
 </tr>
 </tbody>
 </table>
 </div>

 <!-- Pagination -->
 <div class="p-3 border-t border-theme-border flex items-center justify-between text-sm text-theme-text-sec bg-theme-surface-hover shrink-0">
 <div>Hiển thị <span class="font-bold text-theme-text">{{ moderationStore.pendingSongs.length }}</span> bài hát</div>
 <div class="flex items-center gap-1">
 <button 
 :disabled="moderationStore.meta.current_page === 1"
 @click="moderationStore.fetchPendingSongs(moderationStore.meta.current_page - 1)"
 class="px-3 py-1.5 border border-theme-border rounded-lg hover:bg-theme-surface disabled:opacity-50 transition-colors"
 >Trước</button>
 <span class="px-3">Trang {{ moderationStore.meta.current_page }} / {{ moderationStore.meta.last_page }}</span>
 <button 
 :disabled="moderationStore.meta.current_page === moderationStore.meta.last_page"
 @click="moderationStore.fetchPendingSongs(moderationStore.meta.current_page + 1)"
 class="px-3 py-1.5 border border-theme-border rounded-lg hover:bg-theme-surface disabled:opacity-50 transition-colors"
 >Sau</button>
 </div>
 </div>
 </div>
</template>
