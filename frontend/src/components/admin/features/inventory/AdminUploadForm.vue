<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import BaseAdminInput from '@/components/admin/ui/form/BaseAdminInput.vue';
import BaseAdminButton from '@/components/admin/ui/button/BaseAdminButton.vue';
import api from '@/services/api';
import { useGenreStore } from '@/stores/genreStore';
import { useInventoryStore } from '@/stores/inventoryStore';
import { IconUpload, IconSearch, IconX, IconMusic, IconCheck } from '@tabler/icons-vue';

const props = defineProps<{
  // Truyền prop này nếu mở từ màn hình chi tiết Artist, ô chọn Artist sẽ disable
  fixedArtistId?: number;
  fixedArtistName?: string;
}>();

const emit = defineEmits(['success', 'cancel']);

const inventoryStore = useInventoryStore();
const genreStore = useGenreStore();

// Form data
const form = ref({
  title: '',
  artist_id: props.fixedArtistId || null as number | null,
  genre_id: null as number | null
});

const selectedArtistName = ref(props.fixedArtistName || '');

// Audio file handling
const fileInput = ref<HTMLInputElement | null>(null);
const selectedFile = ref<File | null>(null);
const isDragging = ref(false);

const handleFileSelect = (e: Event) => {
  const target = e.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    selectedFile.value = target.files[0];
    if (!form.value.title) {
      // Auto fill title từ tên file
      form.value.title = selectedFile.value.name.replace(/\.[^/.]+$/, "");
    }
  }
};

const handleDrop = (e: DragEvent) => {
  isDragging.value = false;
  if (e.dataTransfer?.files && e.dataTransfer.files.length > 0) {
    const file = e.dataTransfer.files[0];
    if (file.type.includes('audio')) {
      selectedFile.value = file;
      if (!form.value.title) {
        form.value.title = file.name.replace(/\.[^/.]+$/, "");
      }
    } else {
      alert('Vui lòng chọn file âm thanh (.mp3, .wav)');
    }
  }
};

const removeFile = () => {
  selectedFile.value = null;
  if (fileInput.value) fileInput.value.value = '';
};

// Artist Async Search
const artistSearchQuery = ref('');
const artists = ref<any[]>([]);
const showArtistDropdown = ref(false);
const isSearchingArtist = ref(false);
let searchTimeout: any;

const searchArtists = (query: string) => {
  if (props.fixedArtistId) return; // Không cho search nếu đã fix

  isSearchingArtist.value = true;
  clearTimeout(searchTimeout);
  
  if (!query) {
    artists.value = [];
    isSearchingArtist.value = false;
    return;
  }

  searchTimeout = setTimeout(async () => {
    try {
      const res = await api.get('/admin/users', {
        params: {
          'filter[role]': 'artist',
          'filter[search]': query,
          per_page: 5
        }
      });
      if (res.data && res.data.data) {
        // Tuỳ vào pagination response của backend, có thể là res.data.data.items hoặc res.data.data.data
        artists.value = res.data.data.items || res.data.data.data || [];
      }
    } catch (e) {
      console.error('Search artist error', e);
    } finally {
      isSearchingArtist.value = false;
    }
  }, 300);
};

watch(artistSearchQuery, (newVal) => {
  searchArtists(newVal);
});

const selectArtist = (artist: any) => {
  form.value.artist_id = artist.id;
  selectedArtistName.value = artist.artistProfile?.stage_name || artist.name;
  showArtistDropdown.value = false;
  artistSearchQuery.value = '';
};

const clearArtist = () => {
  if (props.fixedArtistId) return;
  form.value.artist_id = null;
  selectedArtistName.value = '';
};

// Genres (Lấy từ store)
onMounted(() => {
  if (genreStore.genresTree.length === 0) {
    genreStore.fetchGenres();
  }
});
const flattenedGenres = computed(() => {
  return genreStore.flattenGenres(genreStore.genresTree);
});

// Close dropdown on click outside
const closeDropdown = (e: MouseEvent) => {
  const target = e.target as HTMLElement;
  if (!target.closest('.artist-search-container')) {
    showArtistDropdown.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', closeDropdown);
});
onUnmounted(() => {
  document.removeEventListener('click', closeDropdown);
});

// Submit
const handleSubmit = async () => {
  if (!selectedFile.value || !form.value.title || !form.value.artist_id || !form.value.genre_id) {
    return; // Form validation handled by HTML5 required attribute
  }
  
  try {
    await inventoryStore.uploadSong(selectedFile.value, {
      title: form.value.title,
      artist_id: form.value.artist_id,
      genre_id: form.value.genre_id
    });
    emit('success');
    // Reset form
    form.value.title = '';
    removeFile();
    if (!props.fixedArtistId) clearArtist();
    form.value.genre_id = null;
  } catch (e) {
    // Error handled by store
  }
};
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-6">
    
    <!-- Upload Zone -->
    <div>
      <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">File Âm thanh</label>
      
      <div 
        v-if="!selectedFile"
        class="border-2 border-dashed rounded-xl p-8 text-center transition-colors cursor-pointer"
        :class="isDragging ? 'border-admin-primary bg-admin-primary/5' : 'border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50'"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleDrop"
        @click="fileInput?.click()"
      >
        <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 text-slate-500 rounded-full flex items-center justify-center mx-auto mb-3">
          <IconUpload size="24" />
        </div>
        <p class="text-sm font-medium text-slate-900 dark:text-white mb-1">Nhấn để chọn file hoặc Kéo thả vào đây</p>
        <p class="text-xs text-slate-500 dark:text-slate-400">Hỗ trợ định dạng MP3, WAV (Tối đa 50MB)</p>
        <input type="file" ref="fileInput" accept="audio/mpeg, audio/wav" class="hidden" @change="handleFileSelect" />
      </div>

      <!-- Selected File View -->
      <div v-else class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center gap-4">
        <div class="w-12 h-12 bg-admin-primary/10 text-admin-primary rounded-lg flex items-center justify-center shrink-0">
          <IconMusic size="24" />
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ selectedFile.name }}</p>
          <p class="text-xs text-slate-500">{{ (selectedFile.size / (1024 * 1024)).toFixed(2) }} MB</p>
        </div>
        <button 
          v-if="!inventoryStore.isUploading" 
          type="button" 
          @click="removeFile" 
          class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors shrink-0"
        >
          <IconX size="18" />
        </button>
      </div>

      <!-- Progress Bar -->
      <div v-if="inventoryStore.isUploading" class="mt-4">
        <div class="flex justify-between text-xs mb-1">
          <span class="text-admin-primary font-medium">{{ inventoryStore.uploadStatusText }}</span>
          <span class="text-slate-500">{{ inventoryStore.uploadProgress }}%</span>
        </div>
        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
          <div 
            class="bg-admin-primary h-2 rounded-full transition-all duration-300"
            :style="{ width: `${inventoryStore.uploadProgress}%` }"
          ></div>
        </div>
      </div>
    </div>

    <!-- Metadata Fields -->
    <div class="space-y-4" :class="{ 'opacity-50 pointer-events-none': inventoryStore.isUploading }">
      <BaseAdminInput
        v-model="form.title"
        label="Tên Bài hát"
        placeholder="Nhập tên bài hát"
        required
      />

      <!-- Async Artist Search -->
      <div class="relative artist-search-container">
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nghệ Sĩ Thể Hiện</label>
        
        <div v-if="selectedArtistName" class="flex items-center gap-2 p-2.5 border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-800">
          <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">
            {{ selectedArtistName.charAt(0).toUpperCase() }}
          </div>
          <span class="text-sm font-medium flex-1 text-slate-900 dark:text-white">{{ selectedArtistName }}</span>
          <button v-if="!fixedArtistId" type="button" @click="clearArtist" class="text-slate-400 hover:text-slate-600 p-1">
            <IconX size="16" />
          </button>
        </div>

        <div v-else>
          <div class="relative">
            <input 
              v-model="artistSearchQuery"
              type="text"
              placeholder="Gõ để tìm kiếm nghệ sĩ..."
              class="w-full pl-9 pr-4 py-2.5 bg-white dark:bg-[#1E232D] border border-slate-200 dark:border-slate-700 text-sm rounded-lg focus:ring-2 focus:ring-admin-primary focus:border-admin-primary outline-none transition-all dark:text-white"
              @focus="showArtistDropdown = true"
            />
            <IconSearch size="16" class="absolute left-3 top-3 text-slate-400" />
          </div>

          <!-- Dropdown -->
          <div v-if="showArtistDropdown && artistSearchQuery" class="absolute z-10 w-full mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-lg overflow-hidden max-h-60 overflow-y-auto">
            <div v-if="isSearchingArtist" class="p-3 text-center text-sm text-slate-500">Đang tìm kiếm...</div>
            <div v-else-if="artists.length === 0" class="p-3 text-center text-sm text-slate-500">Không tìm thấy nghệ sĩ nào</div>
            <button
              v-else
              v-for="artist in artists"
              :key="artist.id"
              type="button"
              class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-3"
              @click="selectArtist(artist)"
            >
              <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">
                {{ (artist.artistProfile?.stage_name || artist.name).charAt(0).toUpperCase() }}
              </div>
              <div>
                <div class="font-medium text-slate-900 dark:text-white">{{ artist.artistProfile?.stage_name || artist.name }}</div>
                <div class="text-xs text-slate-500">{{ artist.email }}</div>
              </div>
            </button>
          </div>
        </div>
        <!-- Hidden input to enforce required -->
        <input type="text" v-model="form.artist_id" required class="absolute opacity-0 w-0 h-0" />
      </div>

      <!-- Genre Select -->
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Thể Loại Nhạc</label>
        <select 
          v-model="form.genre_id"
          required
          class="w-full bg-white dark:bg-[#1E232D] border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-admin-primary focus:border-admin-primary block p-2.5 outline-none transition-colors"
        >
          <option :value="null" disabled>-- Chọn thể loại --</option>
          <option v-for="g in flattenedGenres" :key="g.id" :value="g.id">
            {{ g.displayName }}
          </option>
        </select>
      </div>

    </div>

    <!-- Error Message -->
    <div v-if="inventoryStore.error" class="p-3 bg-rose-50 text-rose-700 text-sm rounded-lg">
      {{ inventoryStore.error }}
    </div>

    <!-- Footer -->
    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
      <BaseAdminButton type="button" variant="secondary" @click="emit('cancel')" :disabled="inventoryStore.isUploading">
        Hủy bỏ
      </BaseAdminButton>
      <BaseAdminButton type="submit" variant="primary" :loading="inventoryStore.isUploading">
        Tải Nhạc Lên
      </BaseAdminButton>
    </div>
  </form>
</template>
