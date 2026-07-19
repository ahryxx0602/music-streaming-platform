<script setup lang="ts">
import { ref, watch } from 'vue';
import { useBannerStore } from '@/stores/bannerStore';
import BaseModal from '@/components/admin/ui/modal/BaseModal.vue';
import BaseAdminInput from '@/components/admin/ui/form/BaseAdminInput.vue';
import BaseAdminButton from '@/components/admin/ui/button/BaseAdminButton.vue';
import { IconPhoto, IconX } from '@tabler/icons-vue';

const props = defineProps<{
  isOpen: boolean;
  bannerData?: any;
}>();

const emit = defineEmits(['update:isOpen', 'success']);
const bannerStore = useBannerStore();

const form = ref({
  id: null as number | null,
  title: '',
  subtitle: '',
  target_url: '',
  is_active: true
});

const imageFile = ref<File | null>(null);
const imagePreview = ref<string | null>(null);

watch(() => props.bannerData, (newVal) => {
  if (newVal) {
    form.value = {
      id: newVal.id,
      title: newVal.title,
      subtitle: newVal.subtitle || '',
      target_url: newVal.target_url || '',
      is_active: newVal.is_active !== undefined ? newVal.is_active : true
    };
    imagePreview.value = newVal.image_url || null;
    imageFile.value = null;
  } else {
    form.value = { id: null, title: '', subtitle: '', target_url: '', is_active: true };
    imagePreview.value = null;
    imageFile.value = null;
  }
}, { immediate: true });

const handleImageSelect = (e: Event) => {
  const target = e.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    const file = target.files[0];
    if (file.type.startsWith('image/')) {
      imageFile.value = file;
      imagePreview.value = URL.createObjectURL(file);
    }
  }
};

const removeImage = () => {
  imageFile.value = null;
  imagePreview.value = null;
};

const handleSubmit = async () => {
  if (!form.value.title) {
    alert("Vui lòng nhập Tiêu đề Banner!");
    return;
  }
  if (!form.value.id && !imageFile.value) {
    alert("Vui lòng tải lên Ảnh Banner!");
    return;
  }
  
  const payload = new FormData();
  payload.append('title', form.value.title);
  if (form.value.subtitle) payload.append('subtitle', form.value.subtitle);
  if (form.value.target_url) payload.append('target_url', form.value.target_url);
  payload.append('is_active', form.value.is_active ? '1' : '0');
  
  if (imageFile.value) {
    payload.append('image', imageFile.value);
  }

  try {
    await bannerStore.saveBanner(form.value.id, payload);
    emit('success');
    emit('update:isOpen', false);
  } catch (e) {}
};
</script>

<template>
  <BaseModal
    :modelValue="isOpen"
    @update:modelValue="emit('update:isOpen', $event)"
    :title="form.id ? $t('admin.banners.edit') : $t('admin.banners.create')"
    size="md"
  >
    <form @submit.prevent="handleSubmit" class="space-y-5">
      
      <!-- Upload Ảnh Bìa Banner -->
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2 flex justify-between">
          <span>Ảnh Banner</span>
          <span class="text-xs text-slate-400 font-normal">{{ $t('admin.banners.image_req') }}</span>
        </label>
        
        <div 
          v-if="!imagePreview"
          class="aspect-[3/1] bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl flex flex-col items-center justify-center cursor-pointer hover:bg-slate-100 hover:border-admin-primary/50 transition-colors"
          @click="() => { const el = $refs.imageInput as HTMLInputElement; el.click() }"
        >
          <IconPhoto size="32" class="text-slate-400 mb-2" />
          <span class="text-sm text-slate-500 font-medium">Nhấn để tải ảnh lên</span>
        </div>
        <div v-else class="aspect-[3/1] rounded-xl overflow-hidden relative group border border-slate-200">
          <img :src="imagePreview" class="w-full h-full object-cover" />
          <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
            <button type="button" @click="removeImage" class="p-2 bg-rose-500 text-white rounded-full hover:bg-rose-600">
              <IconX size="20" />
            </button>
          </div>
        </div>
        <input type="file" ref="imageInput" accept="image/*" class="hidden" @change="handleImageSelect" />
      </div>

      <BaseAdminInput v-model="form.title" label="Tiêu đề chính" placeholder="Ví dụ: Lễ hội âm nhạc mùa hè..." required />
      
      <BaseAdminInput v-model="form.subtitle" label="Phụ đề (Tùy chọn)" placeholder="Nhập một câu mô tả ngắn..." />
      
      <BaseAdminInput v-model="form.target_url" label="Liên kết URL (Tùy chọn)" placeholder="https://..." />
      
      <div class="flex items-center gap-3 p-4 border border-slate-200 rounded-xl bg-slate-50">
        <div class="flex-1">
          <h4 class="text-sm font-bold text-slate-900">Trạng thái hiển thị</h4>
          <p class="text-xs text-slate-500">Banner sẽ lập tức xuất hiện trên trang chủ nếu được bật.</p>
        </div>
        <button 
          type="button" 
          class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
          :class="form.is_active ? 'bg-emerald-500' : 'bg-slate-300'"
          @click="form.is_active = !form.is_active"
        >
          <span class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="form.is_active ? 'translate-x-5' : 'translate-x-0'"></span>
        </button>
      </div>

      <!-- Error Message -->
      <div v-if="bannerStore.error" class="p-3 bg-rose-50 text-rose-700 text-sm rounded-lg">
        {{ bannerStore.error }}
      </div>

      <!-- Footer -->
      <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
        <BaseAdminButton type="button" variant="secondary" @click="emit('update:isOpen', false)">Hủy bỏ</BaseAdminButton>
        <BaseAdminButton type="submit" variant="primary" :loading="bannerStore.isLoading">Lưu Banner</BaseAdminButton>
      </div>
    </form>
  </BaseModal>
</template>
