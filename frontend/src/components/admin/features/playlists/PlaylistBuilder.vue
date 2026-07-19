<script setup lang="ts">
import { ref, watch } from 'vue';
import { usePlaylistStore } from '@/stores/playlistStore';
import BaseAdminInput from '@/components/admin/ui/form/BaseAdminInput.vue';
import BaseAdminButton from '@/components/admin/ui/button/BaseAdminButton.vue';
import { IconSearch, IconPlus, IconMinus, IconGripVertical, IconMusic } from '@tabler/icons-vue';

const props = defineProps<{
  initialData?: any;
}>();

const emit = defineEmits(['success', 'cancel']);
const playlistStore = usePlaylistStore();

const form = ref({
  id: null as number | null,
  title: '',
  description: ''
});

const searchQuery = ref('');
const selectedSongs = ref<any[]>([]);
let searchTimeout: any;

watch(() => props.initialData, (newVal) => {
  if (newVal) {
    form.value.id = newVal.id;
    form.value.title = newVal.title;
    form.value.description = newVal.description || '';
    if (newVal.songs) {
      selectedSongs.value = [...newVal.songs];
    }
  } else {
    form.value = { id: null, title: '', description: '' };
    selectedSongs.value = [];
  }
}, { immediate: true });

watch(searchQuery, (newVal) => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    playlistStore.searchSongs(newVal);
  }, 400);
});

const addSong = (song: any) => {
  if (!selectedSongs.value.find(s => s.id === song.id)) {
    selectedSongs.value.push(song);
  }
};

const removeSong = (index: number) => {
  selectedSongs.value.splice(index, 1);
};

// --- DRAG & DROP NATIVE LOGIC CHO DANH SÁCH BÊN PHẢI ---
const draggedIndex = ref<number | null>(null);

const onDragStart = (index: number, event: DragEvent) => {
  draggedIndex.value = index;
  if (event.dataTransfer) {
    event.dataTransfer.effectAllowed = 'move';
    // Đặt dummy data để HTML5 cho phép drag
    event.dataTransfer.setData('text/plain', index.toString());
  }
};

const onDragOver = (index: number, event: DragEvent) => {
  event.preventDefault();
  // Có thể thêm class feedback ở đây
};

const onDrop = (index: number, event: DragEvent) => {
  event.preventDefault();
  if (draggedIndex.value !== null && draggedIndex.value !== index) {
    // Hoán vị mảng
    const item = selectedSongs.value.splice(draggedIndex.value, 1)[0];
    selectedSongs.value.splice(index, 0, item);
  }
  draggedIndex.value = null;
};

const onDragEnd = () => {
  draggedIndex.value = null;
};
// ----------------------------------------------------

const handleSubmit = async () => {
  if (!form.value.title || selectedSongs.value.length === 0) {
    alert("Vui lòng nhập tên Playlist và chọn ít nhất 1 bài hát!");
    return;
  }
  
  const payload = new FormData();
  payload.append('title', form.value.title);
  if (form.value.description) payload.append('description', form.value.description);
  
  // Nén mảng song_ids theo thứ tự
  selectedSongs.value.forEach((song) => {
    payload.append('song_ids[]', song.id.toString());
  });

  try {
    await playlistStore.savePlaylist(form.value.id, payload);
    emit('success');
  } catch (e) {}
};
</script>

<template>
  <div class="h-[calc(100vh-120px)] flex flex-col bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
      <div>
        <h2 class="text-lg font-bold text-slate-800">{{ form.id ? 'Sửa Playlist' : 'Tạo Playlist Mới' }}</h2>
        <p class="text-xs text-slate-500">Tùy chỉnh thông tin và sắp xếp danh sách phát hệ thống.</p>
      </div>
      <div class="flex gap-2">
        <BaseAdminButton variant="secondary" @click="emit('cancel')">Hủy bỏ</BaseAdminButton>
        <BaseAdminButton variant="primary" @click="handleSubmit" :loading="playlistStore.isLoading">Lưu Playlist</BaseAdminButton>
      </div>
    </div>

    <!-- Body Split View -->
    <div class="flex-1 overflow-hidden flex flex-col md:flex-row">
      
      <!-- Left: Tìm kiếm bài hát -->
      <div class="w-full md:w-1/2 border-r border-slate-200 flex flex-col h-full bg-slate-50/50">
        <div class="p-4 border-b border-slate-200">
          <div class="relative">
            <input 
              v-model="searchQuery"
              type="text" 
              :placeholder="$t('admin.playlists.search_songs')" 
              class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-admin-primary outline-none"
            />
            <IconSearch size="16" class="absolute left-3 top-3 text-slate-400" />
          </div>
          <p class="text-[11px] text-slate-500 mt-2">Chỉ hiển thị các bài hát đã được phê duyệt (Approved).</p>
        </div>
        
        <div class="flex-1 overflow-y-auto p-4 space-y-2">
          <div v-if="playlistStore.isSearchingSongs" class="text-center text-sm text-slate-400 py-4">Đang tìm kiếm...</div>
          <div v-else-if="playlistStore.availableSongs.length === 0 && searchQuery" class="text-center text-sm text-slate-400 py-4">Không tìm thấy bài hát nào khớp.</div>
          <div v-else-if="!searchQuery" class="text-center text-sm text-slate-400 py-8">Gõ từ khóa để tìm bài hát</div>
          
          <!-- Kết quả search -->
          <div 
            v-for="song in playlistStore.availableSongs" 
            :key="song.id"
            class="flex items-center justify-between p-3 bg-white border border-slate-200 rounded-lg shadow-sm hover:border-admin-primary/50 transition-colors"
          >
            <div class="flex items-center gap-3 overflow-hidden">
              <div class="w-10 h-10 bg-indigo-50 text-indigo-400 rounded flex items-center justify-center shrink-0">
                <IconMusic size="18" />
              </div>
              <div class="truncate">
                <div class="font-medium text-sm text-slate-900 truncate">{{ song.title }}</div>
                <div class="text-xs text-slate-500">{{ song.artist?.artistProfile?.stage_name || song.artist?.name || 'Unknown' }}</div>
              </div>
            </div>
            <button 
              @click="addSong(song)" 
              class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-100 text-slate-500 hover:bg-admin-primary hover:text-white transition-colors shrink-0"
              title="Thêm vào Playlist"
            >
              <IconPlus size="16" />
            </button>
          </div>
        </div>
      </div>

      <!-- Right: Form & Selected Songs -->
      <div class="w-full md:w-1/2 flex flex-col h-full bg-white">
        <!-- Playlist Form Info -->
        <div class="p-6 border-b border-slate-200 space-y-4">
          <BaseAdminInput v-model="form.title" label="Tên Playlist" placeholder="Nhập tên danh sách phát..." required />
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Mô tả Playlist</label>
            <textarea 
              v-model="form.description" 
              rows="2" 
              class="w-full p-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-admin-primary outline-none resize-none"
              placeholder="Nhập mô tả ngắn gọn..."
            ></textarea>
          </div>
        </div>

        <!-- Selected Songs List (Drag & Drop) -->
        <div class="flex-1 flex flex-col overflow-hidden bg-slate-50">
          <div class="px-6 py-3 border-b border-slate-200 bg-white flex justify-between items-center shadow-sm z-10">
            <h3 class="font-bold text-slate-700 text-sm flex items-center gap-2">
              {{ $t('admin.playlists.added_songs') }}
              <span class="bg-admin-primary text-white px-2 py-0.5 rounded-full text-xs">{{ selectedSongs.length }}</span>
            </h3>
            <span class="text-[11px] text-slate-500 bg-slate-100 px-2 py-1 rounded">{{ $t('admin.playlists.drag_hint') }}</span>
          </div>
          
          <div class="flex-1 overflow-y-auto p-6 space-y-2">
            <div v-if="selectedSongs.length === 0" class="text-center py-12 border-2 border-dashed border-slate-200 rounded-xl">
              <IconMusic size="32" class="mx-auto text-slate-300 mb-2" />
              <p class="text-sm text-slate-500">{{ $t('admin.playlists.empty_songs') }}</p>
            </div>
            
            <!-- Draggable Items -->
            <div 
              v-for="(song, idx) in selectedSongs" 
              :key="song.id + '_' + idx"
              draggable="true"
              @dragstart="onDragStart(idx, $event)"
              @dragover="onDragOver(idx, $event)"
              @drop="onDrop(idx, $event)"
              @dragend="onDragEnd"
              class="flex items-center gap-3 p-3 bg-white border border-slate-200 rounded-xl shadow-sm transition-all relative group"
              :class="{ 'opacity-50 border-dashed': draggedIndex === idx }"
            >
              <div class="cursor-grab text-slate-300 hover:text-slate-500 active:cursor-grabbing p-1">
                <IconGripVertical size="18" />
              </div>
              <div class="text-xs font-bold text-slate-400 w-5">{{ idx + 1 }}</div>
              
              <div class="flex-1 truncate">
                <div class="font-medium text-sm text-slate-900 truncate">{{ song.title }}</div>
                <div class="text-[11px] text-slate-500">{{ song.artist?.artistProfile?.stage_name || song.artist?.name || 'Unknown' }}</div>
              </div>
              
              <button 
                @click="removeSong(idx)"
                class="w-7 h-7 rounded-full flex items-center justify-center text-slate-400 hover:bg-rose-50 hover:text-rose-500 transition-colors shrink-0"
              >
                <IconMinus size="14" />
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<style scoped>
/* Làm mượt draggable */
.cursor-grab { cursor: grab; }
.active\:cursor-grabbing:active { cursor: grabbing; }
</style>
