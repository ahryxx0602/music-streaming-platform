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
  <div class="h-full flex flex-col bg-white rounded-xl shadow-sm border border-slate-200">
    <!-- Toolbar -->
    <div class="p-4 border-b border-slate-200 flex items-center justify-between gap-4">
      <div class="flex items-center gap-3 flex-1">
        <div class="relative w-64">
          <input 
            type="text" 
            placeholder="Tìm kiếm bài hát..." 
            class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-admin-primary focus:border-admin-primary outline-none transition-all"
          />
          <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        
        <!-- Filter Toggle -->
        <button class="px-3 py-2 bg-slate-50 border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-100 flex items-center gap-2">
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
          <tr class="bg-slate-50 border-b border-slate-200">
            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider w-10">ID</th>
            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Bài Hát</th>
            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nghệ Sĩ</th>
            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Thể Loại</th>
            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Lượt Nghe</th>
            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Trạng Thái</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-if="inventoryStore.isLoading && inventoryStore.songs.length === 0">
            <td colspan="6" class="px-4 py-8 text-center text-slate-500">Đang tải dữ liệu...</td>
          </tr>
          <tr v-else-if="inventoryStore.songs.length === 0">
            <td colspan="6" class="px-4 py-12 text-center">
              <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-3">
                <IconMusic size="32" />
              </div>
              <h3 class="text-sm font-bold text-slate-900 mb-1">Kho nhạc trống</h3>
              <p class="text-xs text-slate-500 mb-4">Chưa có bài hát nào trên hệ thống.</p>
              <BaseAdminButton variant="primary" @click="emit('openUpload')">
                Upload Ngay
              </BaseAdminButton>
            </td>
          </tr>
          <tr v-for="song in inventoryStore.songs" :key="song.id" class="hover:bg-slate-50 transition-colors group">
            <td class="px-4 py-3 text-sm text-slate-500">#{{ song.id }}</td>
            <td class="px-4 py-3">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-md bg-slate-200 overflow-hidden shrink-0">
                  <!-- Cover Image placeholder -->
                  <div class="w-full h-full bg-indigo-100 flex items-center justify-center text-indigo-400">
                    <IconMusic size="20" />
                  </div>
                </div>
                <div>
                  <div class="font-medium text-slate-900 line-clamp-1">{{ song.title }}</div>
                  <div class="text-[11px] text-slate-500 uppercase mt-0.5 tracking-wider">Audio File</div>
                </div>
              </div>
            </td>
            <td class="px-4 py-3">
              <span class="text-sm text-slate-700">{{ song.artist?.artistProfile?.stage_name || song.artist?.name || 'Unknown' }}</span>
            </td>
            <td class="px-4 py-3">
              <span class="px-2 py-1 bg-slate-100 text-slate-600 text-xs rounded-md font-medium">{{ song.genre?.name || '---' }}</span>
            </td>
            <td class="px-4 py-3">
              <span class="text-sm font-medium text-slate-900">{{ song.streams || 0 }}</span>
            </td>
            <td class="px-4 py-3">
              <span class="px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wider"
                :class="{
                  'bg-emerald-100 text-emerald-700': song.status === 'Approved',
                  'bg-amber-100 text-amber-700': song.status === 'Pending',
                  'bg-rose-100 text-rose-700': song.status === 'Hidden',
                  'bg-slate-100 text-slate-700': !['Approved', 'Pending', 'Hidden'].includes(song.status)
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
    <div class="p-3 border-t border-slate-200 flex items-center justify-between text-sm text-slate-500 bg-slate-50 rounded-b-xl">
      <div>Hiển thị <span class="font-bold text-slate-900">{{ inventoryStore.songs.length }}</span> / <span class="font-bold text-slate-900">{{ inventoryStore.meta.total }}</span> bài hát</div>
      <div class="flex items-center gap-1">
        <button 
          :disabled="inventoryStore.meta.current_page === 1"
          @click="inventoryStore.fetchSongs(inventoryStore.meta.current_page - 1)"
          class="px-3 py-1.5 border border-slate-200 rounded-lg hover:bg-white disabled:opacity-50 transition-colors"
        >Trước</button>
        <span class="px-3">Trang {{ inventoryStore.meta.current_page }} / {{ inventoryStore.meta.last_page }}</span>
        <button 
          :disabled="inventoryStore.meta.current_page === inventoryStore.meta.last_page"
          @click="inventoryStore.fetchSongs(inventoryStore.meta.current_page + 1)"
          class="px-3 py-1.5 border border-slate-200 rounded-lg hover:bg-white disabled:opacity-50 transition-colors"
        >Sau</button>
      </div>
    </div>
  </div>
</template>
