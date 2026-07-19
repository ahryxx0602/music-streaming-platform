<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useBannerStore } from '@/stores/bannerStore';
import BannerList from '@/components/admin/features/banners/BannerList.vue';
import BannerFormModal from '@/components/admin/features/banners/BannerFormModal.vue';
import BaseAdminButton from '@/components/admin/ui/button/BaseAdminButton.vue';
import { IconPhoto, IconPlus } from '@tabler/icons-vue';

const bannerStore = useBannerStore();
const isFormOpen = ref(false);
const selectedBanner = ref<any>(null);

onMounted(() => {
  bannerStore.fetchBanners();
});

const openCreate = () => {
  selectedBanner.value = null;
  isFormOpen.value = true;
};

const openEdit = (banner: any) => {
  selectedBanner.value = banner;
  isFormOpen.value = true;
};
</script>

<template>
  <div class="h-full flex flex-col p-6 max-w-[1200px] mx-auto w-full">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-900 mb-1">
          {{ $t('admin.banners.title') }}
        </h1>
        <p class="text-sm text-slate-500">
          {{ $t('admin.banners.subtitle') }}
        </p>
      </div>
      
      <BaseAdminButton variant="primary" :icon="IconPlus" @click="openCreate">
        {{ $t('admin.banners.create') }}
      </BaseAdminButton>
    </div>

    <!-- Hướng dẫn tương tác -->
    <div class="mb-6 bg-slate-50 border border-slate-200 rounded-xl p-4 flex gap-3 text-sm text-slate-600">
      <IconPhoto size="20" class="text-admin-primary shrink-0" />
      <p>Kéo thả biểu tượng <span class="font-bold text-slate-900">⋮⋮</span> để sắp xếp lại thứ tự ưu tiên của các banner trên trang chủ. Những banner nào bị Tắt sẽ không hiển thị bất kể vị trí của nó.</p>
    </div>

    <!-- Content -->
    <div class="flex-1 overflow-auto custom-scrollbar pb-6">
      <BannerList @edit="openEdit" />
    </div>

    <!-- Modals -->
    <BannerFormModal 
      v-model:is-open="isFormOpen" 
      :banner-data="selectedBanner"
    />
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #94a3b8; }
</style>
