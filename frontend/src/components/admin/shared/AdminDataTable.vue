<script setup lang="ts" generic="T">
defineProps<{
 columns: { key: string; label: string; align?: 'left' | 'center' | 'right' }[];
 data: T[];
 loading?: boolean;
 total?: number;
 perPage?: number;
 currentPage?: number;
}>();

defineEmits<{
 (e: 'update:currentPage', page: number): void;
}>();
</script>

<template>
 <div class="bg-theme-surface rounded-xl border border-theme-border shadow-sm overflow-hidden flex flex-col h-full">
 <div class="overflow-x-auto flex-1">
 <table class="w-full text-left text-sm text-theme-text-sec">
 <thead class="bg-theme-bg border-b border-theme-border text-theme-text font-semibold sticky top-0 z-10">
 <tr>
 <th 
 v-for="col in columns" 
 :key="col.key"
 class="px-6 py-4 whitespace-nowrap"
 :class="{
 'text-center': col.align === 'center',
 'text-right': col.align === 'right'
 }"
 >
 {{ col.label }}
 </th>
 </tr>
 </thead>
 <tbody class="divide-y divide-theme-border">
 <tr v-if="loading" class="animate-pulse">
 <td :colspan="columns.length" class="px-6 py-12 text-center text-theme-text-sec">
 Đang tải dữ liệu...
 </td>
 </tr>
 <tr v-else-if="!data || data.length === 0">
 <td :colspan="columns.length" class="px-6 py-12 text-center text-theme-text-sec">
 Không tìm thấy dữ liệu.
 </td>
 </tr>
 <tr v-else v-for="(row, index) in data" :key="index" class="hover:bg-theme-bg/50 transition-colors">
 <td 
 v-for="col in columns" 
 :key="col.key"
 class="px-6 py-4 align-middle"
 :class="{
 'text-center': col.align === 'center',
 'text-right': col.align === 'right'
 }"
 >
 <slot :name="col.key" :item="row" :index="index">
 {{ (row as any)[col.key] }}
 </slot>
 </td>
 </tr>
 </tbody>
 </table>
 </div>

 <!-- Pagination -->
 <div v-if="total && perPage && total > perPage" class="px-6 py-4 border-t border-theme-border bg-theme-bg flex items-center justify-between">
 <span class="text-sm text-theme-text-sec">
 Hiển thị {{ Math.min(((currentPage || 1) - 1) * perPage + 1, total) }} đến {{ Math.min((currentPage || 1) * perPage, total) }} trong tổng số {{ total }}
 </span>
 <div class="flex items-center gap-1">
 <button 
 :disabled="(currentPage || 1) === 1"
 @click="$emit('update:currentPage', (currentPage || 1) - 1)"
 class="px-3 py-1.5 border border-theme-border rounded text-sm text-theme-text-sec bg-theme-surface hover:bg-theme-bg disabled:opacity-50 disabled:cursor-not-allowed"
 >
 Trang trước
 </button>
 <button 
 :disabled="(currentPage || 1) * perPage >= total"
 @click="$emit('update:currentPage', (currentPage || 1) + 1)"
 class="px-3 py-1.5 border border-theme-border rounded text-sm text-theme-text-sec bg-theme-surface hover:bg-theme-bg disabled:opacity-50 disabled:cursor-not-allowed"
 >
 Trang sau
 </button>
 </div>
 </div>
 </div>
</template>
