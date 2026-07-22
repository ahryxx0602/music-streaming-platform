<script setup lang="ts">
import { onMounted, computed } from 'vue';
import { useGenreStore } from '@/stores/genreStore';
import { IconEdit, IconPlus, IconCheck, IconX } from '@tabler/icons-vue';

const emit = defineEmits(['edit']);
const genreStore = useGenreStore();

onMounted(() => {
 genreStore.fetchGenres();
});

const flattenedGenres = computed(() => {
 return genreStore.flattenGenres(genreStore.genresTree);
});

const handleToggleActive = async (genre: any) => {
 try {
 await genreStore.saveGenre(genre.id, {
 ...genre,
 is_active: !genre.is_active
 });
 } catch (error) {
 console.error('Failed to toggle active state');
 }
};
</script>

<template>
 <div class="overflow-x-auto">
 <table class="w-full text-left border-collapse">
 <thead>
 <tr class="bg-theme-surface-hover border-b border-theme-border">
 <th class="px-6 py-4 text-xs font-semibold text-theme-text-sec uppercase tracking-wider">{{ $t('admin.genres.name') || 'Tên Thể loại' }}</th>
 <th class="px-6 py-4 text-xs font-semibold text-theme-text-sec uppercase tracking-wider">{{ $t('admin.genres.slug') || 'Đường dẫn (Slug)' }}</th>
 <th class="px-6 py-4 text-xs font-semibold text-theme-text-sec uppercase tracking-wider">{{ $t('admin.genres.status') || 'Trạng thái' }}</th>
 <th class="px-6 py-4 text-xs font-semibold text-theme-text-sec uppercase tracking-wider text-right">{{ $t('common.actions') || 'Thao tác' }}</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-theme-border">
 <tr v-if="genreStore.isLoading && flattenedGenres.length === 0">
 <td colspan="4" class="px-6 py-8 text-center text-theme-text-sec">{{ $t('common.loading') || 'Đang tải dữ liệu...' }}</td>
 </tr>
 <tr v-else-if="flattenedGenres.length === 0">
 <td colspan="4" class="px-6 py-8 text-center text-theme-text-sec">{{ $t('admin.genres.empty') || 'Chưa có thể loại nào.' }}</td>
 </tr>
 <tr 
 v-for="genre in flattenedGenres" 
 :key="genre.id"
 class="hover:bg-theme-surface-hover transition-colors"
 >
 <td class="px-6 py-4">
 <div class="flex items-center gap-2" :style="{ paddingLeft: `${genre.level * 24}px` }">
 <span v-if="genre.level > 0" class="text-theme-text-sec opacity-50">↳</span>
 <span class="font-medium text-theme-text">{{ genre.name }}</span>
 </div>
 </td>
 <td class="px-6 py-4">
 <span class="text-sm text-theme-text-sec">{{ genre.slug }}</span>
 </td>
 <td class="px-6 py-4">
 <button 
 @click="handleToggleActive(genre)"
 class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-theme-primary focus:ring-offset-2"
 :class="genre.is_active ? 'bg-theme-primary' : 'bg-theme-border'"
 >
 <span 
 class="inline-block h-3 w-3 transform rounded-full bg-theme-surface transition-transform"
 :class="genre.is_active ? 'translate-x-5' : 'translate-x-1'"
 />
 </button>
 </td>
 <td class="px-6 py-4 text-right">
 <div class="flex items-center justify-end gap-2">
 <button 
 @click="emit('edit', { parent_id: genre.id })" 
 class="p-1.5 text-theme-text-sec hover:text-theme-primary hover:bg-theme-primary/10 rounded-lg transition-colors"
 :title="$t('admin.genres.add_child') || 'Thêm danh mục con'"
 >
 <IconPlus size="18" />
 </button>
 <button 
 @click="emit('edit', genre)" 
 class="p-1.5 text-theme-text-sec hover:text-theme-accent hover:bg-theme-accent/10 rounded-lg transition-colors"
 :title="$t('common.edit') || 'Chỉnh sửa'"
 >
 <IconEdit size="18" />
 </button>
 </div>
 </td>
 </tr>
 </tbody>
 </table>
 </div>
</template>
