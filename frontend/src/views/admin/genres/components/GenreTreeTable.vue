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
        <tr class="bg-gray-50 dark:bg-[#252b36] border-b border-gray-200 dark:border-gray-800">
          <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tên Thể loại</th>
          <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Đường dẫn (Slug)</th>
          <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Trạng thái</th>
          <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Thao tác</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
        <tr v-if="genreStore.isLoading && flattenedGenres.length === 0">
          <td colspan="4" class="px-6 py-8 text-center text-gray-500">Đang tải dữ liệu...</td>
        </tr>
        <tr v-else-if="flattenedGenres.length === 0">
          <td colspan="4" class="px-6 py-8 text-center text-gray-500">Chưa có thể loại nào.</td>
        </tr>
        <tr 
          v-for="genre in flattenedGenres" 
          :key="genre.id"
          class="hover:bg-gray-50 dark:hover:bg-slate-800/30 transition-colors"
        >
          <td class="px-6 py-4">
            <div class="flex items-center gap-2" :style="{ paddingLeft: `${genre.level * 24}px` }">
              <span v-if="genre.level > 0" class="text-gray-400">↳</span>
              <span class="font-medium text-gray-900 dark:text-white">{{ genre.name }}</span>
            </div>
          </td>
          <td class="px-6 py-4">
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ genre.slug }}</span>
          </td>
          <td class="px-6 py-4">
            <button 
              @click="handleToggleActive(genre)"
              class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-admin-primary focus:ring-offset-2 dark:focus:ring-offset-slate-900"
              :class="genre.is_active ? 'bg-admin-primary' : 'bg-gray-200 dark:bg-gray-700'"
            >
              <span 
                class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform"
                :class="genre.is_active ? 'translate-x-5' : 'translate-x-1'"
              />
            </button>
          </td>
          <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-2">
              <button 
                @click="emit('edit', { parent_id: genre.id })" 
                class="p-1.5 text-gray-400 hover:text-admin-primary hover:bg-admin-primary/10 rounded-lg transition-colors"
                title="Thêm danh mục con"
              >
                <IconPlus size="18" />
              </button>
              <button 
                @click="emit('edit', genre)" 
                class="p-1.5 text-gray-400 hover:text-white hover:bg-slate-700 rounded-lg transition-colors"
                title="Chỉnh sửa"
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
