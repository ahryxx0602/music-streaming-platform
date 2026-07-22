<script setup lang="ts">
import BaseDrawer from '@/components/admin/ui/drawer/BaseDrawer.vue';
import BaseAdminInput from '@/components/admin/ui/form/BaseAdminInput.vue';
import BaseAdminButton from '@/components/admin/ui/button/BaseAdminButton.vue';
import { computed, ref, watch } from 'vue';
import { useGenreStore } from '@/stores/genreStore';

const props = defineProps<{
 isOpen: boolean;
 genre: any;
}>();

const emit = defineEmits(['update:isOpen']);
const genreStore = useGenreStore();

const isDrawerOpen = computed({
 get: () => props.isOpen,
 set: (val) => emit('update:isOpen', val)
});

const form = ref({
 id: null as number | null,
 name: '',
 slug: '',
 parent_id: null as number | null,
});

// Helper to generate slug
const generateSlug = (text: string) => {
 return text.toString().toLowerCase()
 .replace(/\s+/g, '-')
 .replace(/[^\w\-]+/g, '')
 .replace(/\-\-+/g, '-')
 .replace(/^-+/, '')
 .replace(/-+$/, '');
};

let autoSlug = true;

watch(() => props.genre, (newVal) => {
 if (newVal && newVal.id) {
 // Edit mode
 form.value.id = newVal.id;
 form.value.name = newVal.name;
 form.value.slug = newVal.slug;
 form.value.parent_id = newVal.parent_id;
 autoSlug = false;
 } else if (newVal && newVal.parent_id) {
 // Add child mode
 form.value = { id: null, name: '', slug: '', parent_id: newVal.parent_id };
 autoSlug = true;
 } else {
 // Add root mode
 form.value = { id: null, name: '', slug: '', parent_id: null };
 autoSlug = true;
 }
}, { immediate: true });

watch(() => form.value.name, (newVal) => {
 if (autoSlug && !form.value.id) {
 form.value.slug = generateSlug(newVal);
 }
});

const isSaving = ref(false);

const handleSave = async () => {
 isSaving.value = true;
 try {
 const payload = {
 name: form.value.name,
 parent_id: form.value.parent_id,
 slug: form.value.slug // Backend will use this or auto-generate if empty
 };
 await genreStore.saveGenre(form.value.id, payload);
 isDrawerOpen.value = false;
 } catch (err) {
 // Error handled by store, can show toast here
 } finally {
 isSaving.value = false;
 }
};

// Flattened genres for select options
const flattenedGenres = computed(() => {
 return genreStore.flattenGenres(genreStore.genresTree);
});

// Disable self and descendants to prevent infinite loops
const isOptionDisabled = (option: any) => {
 if (!form.value.id) return false;
 if (option.id === form.value.id) return true;
 
 // Find if option is a descendant
 // Since flat list preserves order, we can find form.value.id, and any subsequent items with level > its level are descendants
 const flat = flattenedGenres.value;
 const selfIndex = flat.findIndex(g => g.id === form.value.id);
 if (selfIndex === -1) return false;
 
 const selfLevel = flat[selfIndex].level;
 const optionIndex = flat.findIndex(g => g.id === option.id);
 
 // If option is after self, check if all items between them (including option) have level > selfLevel
 if (optionIndex > selfIndex) {
 for (let i = selfIndex + 1; i <= optionIndex; i++) {
 if (flat[i].level <= selfLevel) {
 return false; // Found an item that is not a descendant, so the chain is broken
 }
 }
 return true; // All items in between were descendants, so option is a descendant
 }
 
 return false;
};
</script>

<template>
 <BaseDrawer v-model="isDrawerOpen" :title="form.id ? $t('admin.genres.edit') : $t('admin.genres.add_new')" size="md">
 <div class="p-6 space-y-6">
 <div v-if="genreStore.error" class="p-3 bg-theme-danger/10 text-theme-danger rounded-lg text-sm font-medium">
 {{ genreStore.error }}
 </div>
 
 <div class="space-y-4">
 <div>
 <label class="block text-sm font-medium text-[var(--color-label)] mb-1">{{ $t('admin.genres.parent_genre') || 'Danh mục Cha' }}</label>
 <select 
 v-model="form.parent_id"
 class="w-full bg-[var(--color-input-bg)] border border-[var(--color-input-border)] text-theme-text text-sm rounded-lg focus:ring-theme-primary focus:border-theme-primary block p-2.5 outline-none transition-colors"
 >
 <option :value="null">{{ $t('admin.genres.root_genre') || '-- Không có (Danh mục Gốc) --' }}</option>
 <option 
 v-for="g in flattenedGenres" 
 :key="g.id" 
 :value="g.id"
 :disabled="isOptionDisabled(g)"
 >
 {{ g.displayName }}
 </option>
 </select>
 </div>

 <BaseAdminInput
 v-model="form.name"
 :label="$t('admin.genres.name')"
 :placeholder="$t('admin.genres.name_ph') || 'VD: Nhạc Trẻ'"
 required
 />
 
 <BaseAdminInput
 v-model="form.slug"
 :label="$t('admin.genres.slug') || 'Đường dẫn (Slug)'"
 :placeholder="$t('admin.genres.slug_ph') || 'vd: nhac-tre'"
 :disabled="!!form.id"
 class="opacity-70"
 />
 
 <p class="text-xs text-theme-text-sec">
 {{ $t('admin.genres.slug_help') || '* Đường dẫn (slug) sẽ được tự động tạo dựa trên tên thể loại khi thêm mới.' }}
 </p>
 </div>
 </div>
 
 <template #footer>
 <div class="flex items-center justify-end gap-3 w-full">
 <BaseAdminButton variant="secondary" @click="isDrawerOpen = false" :disabled="isSaving">
 {{ $t('common.cancel') }}
 </BaseAdminButton>
 <BaseAdminButton variant="primary" @click="handleSave" :loading="isSaving" :disabled="!form.name">
 {{ $t('common.save') }}
 </BaseAdminButton>
 </div>
 </template>
 </BaseDrawer>
</template>
