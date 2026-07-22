<script setup lang="ts">
import { ref } from 'vue';
import GenreTreeTable from './components/GenreTreeTable.vue';
import GenreDrawer from './components/GenreDrawer.vue';
import BaseAdminButton from '@/components/admin/ui/button/BaseAdminButton.vue';
import { IconPlus } from '@tabler/icons-vue';

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
 <h1 class="text-2xl font-bold text-theme-text">{{ $t('admin.menu.genres') }}</h1>
 <p class="text-sm text-theme-text-sec mt-1">{{ $t('admin.genres.subtitle') }}</p>
 </div>
 <BaseAdminButton variant="primary" :icon="IconPlus" @click="openDrawer()">
 {{ $t('admin.genres.add_new') }}
 </BaseAdminButton>
 </div>

 <div class="flex-1 bg-theme-surface rounded-xl border border-theme-border overflow-hidden flex flex-col">
 <GenreTreeTable @edit="openDrawer" />
 </div>

 <GenreDrawer 
 v-model:is-open="isDrawerOpen" 
 :genre="editingGenre"
 />
 </div>
</template>
