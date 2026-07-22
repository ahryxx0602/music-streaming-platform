<script setup lang="ts">
import { ref } from 'vue';
import { useInventoryStore } from '@/stores/inventoryStore';
import BaseAdminButton from '@/components/admin/ui/button/BaseAdminButton.vue';
import { IconMusic, IconFilter } from '@tabler/icons-vue';

const inventoryStore = useInventoryStore();

// Logic table có thể dùng table component tương tự AdminDashboard
const emit = defineEmits(['openUpload']);

// Fetch initial data
inventoryStore.fetchSongs();

const formatDate = (dateString: string) => {
 if (!dateString) return '';
 return new Date(dateString).toLocaleDateString('en-GB'); 
};
</script>

<template>
 <div class="h-full flex flex-col bg-theme-surface rounded-xl shadow-[var(--shadow-glow)] border border-theme-border">
 <!-- Toolbar -->
 <div class="p-4 border-b border-theme-border flex items-center justify-between gap-4">
 <div class="flex items-center gap-3 flex-1">
 <div class="relative w-64">
 <input 
 type="text" 
 placeholder="Tìm kiếm bài hát..." 
 class="w-full pl-9 pr-4 py-2 bg-theme-surface-hover border border-theme-border rounded-lg text-sm focus:ring-2 focus:ring-admin-primary focus:border-theme-primary outline-none transition-all"
 />
 <svg class="w-4 h-4 text-theme-text-sec absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
 </svg>
 </div>
 
 <!-- Filter Toggle -->
 <button class="px-3 py-2 bg-theme-surface-hover border border-theme-border text-theme-text-sec rounded-lg text-sm font-medium hover:bg-theme-surface-hover flex items-center gap-2">
 <IconFilter size="16" /> Lọc
 </button>
 </div>

 <BaseAdminButton variant="primary" :icon="IconMusic" @click="emit('openUpload')">
 Tải Nhạc Lên
 </BaseAdminButton>
 </div>

 <!-- Data Table -->
 <div class="flex-1 overflow-auto">
 <table class="w-full text-left border-collapse min-w-[800px]">
 <thead>
 <tr class="bg-theme-surface-hover border-b border-theme-border">
 <th class="px-4 py-3 text-xs font-semibold text-theme-text-sec uppercase tracking-wider w-10">ID</th>
 <th class="px-4 py-3 text-xs font-semibold text-theme-text-sec uppercase tracking-wider">Bài Hát</th>
 <th class="px-4 py-3 text-xs font-semibold text-theme-text-sec uppercase tracking-wider">Nghệ Sĩ</th>
 <th class="px-4 py-3 text-xs font-semibold text-theme-text-sec uppercase tracking-wider">Thể Loại</th>
 <th class="px-4 py-3 text-xs font-semibold text-theme-text-sec uppercase tracking-wider">Lượt Nghe</th>
 <th class="px-4 py-3 text-xs font-semibold text-theme-text-sec uppercase tracking-wider">Trạng Thái</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-theme-border">
 <tr v-if="inventoryStore.isLoading && inventoryStore.songs.length === 0">
 <td colspan="6" class="px-4 py-8 text-center text-theme-text-sec">Đang tải dữ liệu...</td>
 </tr>
 <tr v-else-if="inventoryStore.songs.length === 0">
 <td colspan="6" class="px-4 py-12 text-center">
 <div class="w-16 h-16 bg-theme-surface-hover text-theme-text-sec rounded-full flex items-center justify-center mx-auto mb-3">
 <IconMusic size="32" />
 </div>
 <h3 class="text-sm font-bold text-theme-text mb-1">Kho nhạc trống</h3>
 <p class="text-xs text-theme-text-sec mb-4">Chưa có bài hát nào trên hệ thống.</p>
 <BaseAdminButton variant="primary" @click="emit('openUpload')">
 Upload Ngay
 </BaseAdminButton>
 </td>
 </tr>
 <tr v-for="song in inventoryStore.songs" :key="song.id" class="hover:bg-theme-surface-hover transition-colors group">
 <td class="px-4 py-3 text-sm text-theme-text-sec">#{{ song.id }}</td>
 <td class="px-4 py-3">
 <div class="flex items-center gap-3">
 <div class="w-10 h-10 rounded-md bg-slate-200 overflow-hidden shrink-0">
 <!-- Cover Image placeholder -->
 <div class="w-full h-full bg-theme-primary/15 flex items-center justify-center text-theme-primary">
 <IconMusic size="20" />
 </div>
 </div>
 <div>
 <div class="font-medium text-theme-text line-clamp-1">{{ song.title }}</div>
 <div class="text-[11px] text-theme-text-sec uppercase mt-0.5 tracking-wider">Audio File</div>
 </div>
 </div>
 </td>
 <td class="px-4 py-3">
 <span class="text-sm text-theme-text-sec">{{ song.artist?.artistProfile?.stage_name || song.artist?.name || 'Unknown' }}</span>
 </td>
 <td class="px-4 py-3">
 <span class="px-2 py-1 bg-theme-surface-hover text-theme-text-sec text-xs rounded-md font-medium">{{ song.genre?.name || '---' }}</span>
 </td>
 <td class="px-4 py-3">
 <span class="text-sm font-medium text-theme-text">{{ song.streams || 0 }}</span>
 </td>
 <td class="px-4 py-3">
 <span class="px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wider"
 :class="{
 'bg-theme-accent/15 text-theme-accent': song.status === 'Approved',
 'bg-amber-500/15 text-theme-warning ': song.status === 'Pending',
 'bg-theme-danger/15 text-theme-danger': song.status === 'Hidden',
 'bg-theme-surface-hover text-theme-text-sec': !['Approved', 'Pending', 'Hidden'].includes(song.status)
 }"
 >
 {{ song.status }}
 </span>
 </td>
 </tr>
 </tbody>
 </table>
 </div>

 <!-- Pagination -->
 <div class="p-3 border-t border-theme-border flex items-center justify-between text-sm text-theme-text-sec bg-theme-surface-hover rounded-b-xl">
 <div>Hiển thị <span class="font-bold text-theme-text">{{ inventoryStore.songs.length }}</span> / <span class="font-bold text-theme-text">{{ inventoryStore.meta.total }}</span> bài hát</div>
 <div class="flex items-center gap-1">
 <button 
 :disabled="inventoryStore.meta.current_page === 1"
 @click="inventoryStore.fetchSongs(inventoryStore.meta.current_page - 1)"
 class="px-3 py-1.5 border border-theme-border rounded-lg hover:bg-theme-surface disabled:opacity-50 transition-colors"
 >Trước</button>
 <span class="px-3">Trang {{ inventoryStore.meta.current_page }} / {{ inventoryStore.meta.last_page }}</span>
 <button 
 :disabled="inventoryStore.meta.current_page === inventoryStore.meta.last_page"
 @click="inventoryStore.fetchSongs(inventoryStore.meta.current_page + 1)"
 class="px-3 py-1.5 border border-theme-border rounded-lg hover:bg-theme-surface disabled:opacity-50 transition-colors"
 >Sau</button>
 </div>
 </div>
 </div>
</template>
