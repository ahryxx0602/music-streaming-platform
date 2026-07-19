<script setup lang="ts">
import { ref } from 'vue';
import GenreTreeTable from './components/GenreTreeTable.vue';
import GenreDrawer from './components/GenreDrawer.vue';
import BaseButton from '@/components/base/BaseButton.vue';

const isDrawerOpen = ref(false);
const editingGenre = ref(null);

const openDrawer = (genre = null) => {
  editingGenre.value = genre;
  isDrawerOpen.value = true;
};
</script>

<template>
  <div class="h-full flex flex-col gap-6 p-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $t('admin.menu.genres') }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $t('admin.genres.subtitle') }}</p>
      </div>
      <BaseButton @click="openDrawer()">
        + {{ $t('admin.genres.add_new') }}
      </BaseButton>
    </div>

    <div class="flex-1 bg-white dark:bg-[#1E232D] rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden flex flex-col">
      <GenreTreeTable @edit="openDrawer" />
    </div>

    <GenreDrawer 
      v-model:is-open="isDrawerOpen" 
      :genre="editingGenre"
    />
  </div>
</template>
