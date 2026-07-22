<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from 'vue';
import BaseAdminInput from '@/components/admin/ui/form/BaseAdminInput.vue';
import BaseAdminButton from '@/components/admin/ui/button/BaseAdminButton.vue';
import api from '@/services/api';
import { useAlbumStore } from '@/stores/albumStore';
import { IconPhoto, IconSearch, IconX, IconMusic, IconChevronRight, IconChevronLeft } from '@tabler/icons-vue';

const props = defineProps<{
 initialData?: any;
}>();

const emit = defineEmits(['success', 'cancel']);
const albumStore = useAlbumStore();

// Form data
const form = ref({
 id: null as number | null,
 title: '',
 artist_id: null as number | null,
 release_date: '',
 type: 'Album',
 description: '',
});

const selectedArtistName = ref('');
const coverImageFile = ref<File | null>(null);
const coverImagePreview = ref<string | null>(null);

// Track selector lists
const availableSongs = ref<any[]>([]); // Danh sách bên trái
const selectedSongs = ref<any[]>([]); // Danh sách bên phải

// Chuyển đổi dữ liệu khi mở form (Edit mode)
watch(() => props.initialData, (newVal) => {
 if (newVal) {
 form.value = {
 id: newVal.id,
 title: newVal.title,
 artist_id: newVal.artist_id,
 release_date: newVal.release_date ? newVal.release_date.split('T')[0] : '',
 type: newVal.type || 'Album',
 description: newVal.description || ''
 };
 selectedArtistName.value = newVal.artist?.artistProfile?.stage_name || newVal.artist?.name || '';
 coverImagePreview.value = newVal.cover_image || null;
 
 // Nạp danh sách nhạc đã chọn
 if (newVal.songs) {
 selectedSongs.value = [...newVal.songs];
 }
 
 // Gọi API lấy bài chưa gán cho artist này
 if (form.value.artist_id) {
 fetchAvailable(form.value.artist_id);
 }
 } else {
 // Thêm mới
 resetForm();
 }
}, { immediate: true });

const resetForm = () => {
 form.value = { id: null, title: '', artist_id: null, release_date: '', type: 'Album', description: '' };
 selectedArtistName.value = '';
 coverImageFile.value = null;
 coverImagePreview.value = null;
 availableSongs.value = [];
 selectedSongs.value = [];
};

const handleImageSelect = (e: Event) => {
 const target = e.target as HTMLInputElement;
 if (target.files && target.files.length > 0) {
 const file = target.files[0] as File;
 if (file.type.startsWith('image/')) {
 coverImageFile.value = file;
 coverImagePreview.value = URL.createObjectURL(file);
 }
 }
};

const removeImage = () => {
 coverImageFile.value = null;
 coverImagePreview.value = null;
};

// Artist Search Logic
const artistSearchQuery = ref('');
const artists = ref<any[]>([]);
const showArtistDropdown = ref(false);
const isSearchingArtist = ref(false);
let searchTimeout: any;

const searchArtists = (query: string) => {
 isSearchingArtist.value = true;
 clearTimeout(searchTimeout);
 if (!query) {
 artists.value = [];
 isSearchingArtist.value = false;
 return;
 }
 searchTimeout = setTimeout(async () => {
 try {
 const res = await api.get('/admin/users', { params: { 'filter[role]': 'artist', 'filter[search]': query, per_page: 5 } });
 artists.value = res.data.data.items || res.data.data.data || [];
 } catch (e) {} finally { isSearchingArtist.value = false; }
 }, 300);
};

watch(artistSearchQuery, searchArtists);

const fetchAvailable = async (artistId: number) => {
 await albumStore.fetchUnassignedSongs(artistId);
 // Cập nhật list bên trái (Loại trừ những bài đã có trong bên phải - trường hợp edit)
 const selectedIds = selectedSongs.value.map(s => s.id);
 availableSongs.value = albumStore.unassignedSongs.filter(s => !selectedIds.includes(s.id));
};

const selectArtist = (artist: any) => {
 // Nếu chọn nghệ sĩ khác với nghệ sĩ hiện tại, phải reset danh sách bài hát
 if (form.value.artist_id !== artist.id) {
 availableSongs.value = [];
 selectedSongs.value = [];
 form.value.artist_id = artist.id;
 selectedArtistName.value = artist.artistProfile?.stage_name || artist.name;
 fetchAvailable(artist.id);
 }
 showArtistDropdown.value = false;
 artistSearchQuery.value = '';
};

const clearArtist = () => {
 form.value.artist_id = null;
 selectedArtistName.value = '';
 availableSongs.value = [];
 selectedSongs.value = [];
};

// Track Selector Logic (Dual Listbox)
const moveToRight = (song: any) => {
 availableSongs.value = availableSongs.value.filter(s => s.id !== song.id);
 selectedSongs.value.push(song);
};

const moveToLeft = (song: any) => {
 selectedSongs.value = selectedSongs.value.filter(s => s.id !== song.id);
 // Chỉ chuyển sang trái nếu bài này thuộc về artist hiện tại (Đảm bảo an toàn)
 if (song.artist_id === form.value.artist_id) {
 availableSongs.value.push(song);
 }
};

const closeDropdown = (e: MouseEvent) => {
 const target = e.target as HTMLElement;
 if (!target.closest('.artist-search-container')) showArtistDropdown.value = false;
};
onMounted(() => document.addEventListener('click', closeDropdown));
onUnmounted(() => document.removeEventListener('click', closeDropdown));

const handleSubmit = async () => {
 if (!form.value.title || !form.value.artist_id) return;
 
 const payload = new FormData();
 payload.append('title', form.value.title);
 payload.append('artist_id', form.value.artist_id.toString());
 payload.append('type', form.value.type);
 if (form.value.release_date) payload.append('release_date', form.value.release_date);
 if (form.value.description) payload.append('description', form.value.description);
 if (coverImageFile.value) payload.append('cover_image', coverImageFile.value);
 
 // Trích xuất mảng song_ids
 selectedSongs.value.forEach((song) => {
 payload.append('song_ids[]', song.id.toString());
 });

 try {
 await albumStore.saveAlbum(form.value.id, payload);
 emit('success');
 } catch (e) {}
};
</script>

<template>
 <form @submit.prevent="handleSubmit" class="space-y-6">
 <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
 
 <!-- Upload Ảnh Bìa -->
 <div class="col-span-1">
 <label class="block text-sm font-medium text-theme-text-sec mb-2">Ảnh bìa Album</label>
 <div 
 v-if="!coverImagePreview"
 class="aspect-square bg-theme-surface-hover ] border-2 border-dashed border-theme-border rounded-xl flex flex-col items-center justify-center cursor-pointer hover:bg-theme-surface-hover transition-colors"
 @click="() => { const el = $refs.imageInput as HTMLInputElement; el.click() }"
 >
 <IconPhoto size="32" class="text-theme-text-sec mb-2" />
 <span class="text-sm text-theme-text-sec font-medium">Tải ảnh lên (Tỉ lệ 1:1)</span>
 </div>
 <div v-else class="aspect-square rounded-xl overflow-hidden relative group border border-theme-border">
 <img :src="coverImagePreview" class="w-full h-full object-cover" />
 <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
 <button type="button" @click="removeImage" class="p-2 bg-rose-500 text-white rounded-full hover:bg-rose-600">
 <IconX size="20" />
 </button>
 </div>
 </div>
 <input type="file" ref="imageInput" accept="image/*" class="hidden" @change="handleImageSelect" />
 </div>

 <!-- Thông tin cơ bản -->
 <div class="col-span-1 md:col-span-2 space-y-4">
 <BaseAdminInput v-model="form.title" label="Tên Album/Đĩa đơn" required />
 
 <!-- Artist Search -->
 <div class="relative artist-search-container">
 <label class="block text-sm font-medium text-theme-text-sec mb-1">Nghệ Sĩ</label>
 <div v-if="selectedArtistName" class="flex items-center gap-2 p-2 border border-theme-border rounded-lg bg-theme-surface-hover">
 <span class="text-sm font-medium flex-1 pl-2">{{ selectedArtistName }}</span>
 <button type="button" @click="clearArtist" class="p-1 hover:text-theme-danger text-theme-text-sec">
 <IconX size="16" />
 </button>
 </div>
 <div v-else class="relative">
 <input 
 v-model="artistSearchQuery" type="text" placeholder="Tìm kiếm nghệ sĩ..." 
 class="w-full pl-9 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-admin-primary outline-none"
 @focus="showArtistDropdown = true"
 />
 <IconSearch size="16" class="absolute left-3 top-2.5 text-theme-text-sec" />
 
 <!-- Dropdown -->
 <div v-if="showArtistDropdown && artistSearchQuery" class="absolute z-10 w-full mt-1 bg-theme-surface border shadow-lg rounded-lg max-h-48 overflow-y-auto">
 <button v-for="artist in artists" :key="artist.id" type="button" class="w-full text-left px-4 py-2 hover:bg-theme-surface-hover text-sm" @click="selectArtist(artist)">
 {{ artist.artistProfile?.stage_name || artist.name }}
 </button>
 </div>
 </div>
 </div>

 <div class="grid grid-cols-2 gap-4">
 <div>
 <label class="block text-sm font-medium text-theme-text-sec mb-1">Loại Phát Hành</label>
 <select v-model="form.type" class="w-full border p-2 rounded-lg text-sm outline-none">
 <option value="Single">{{ $t('admin.albums.type_single') }}</option>
 <option value="EP">{{ $t('admin.albums.type_ep') }}</option>
 <option value="Album">{{ $t('admin.albums.type_album') }}</option>
 </select>
 </div>
 <div>
 <label class="block text-sm font-medium text-theme-text-sec mb-1">Ngày Phát Hành</label>
 <input type="date" v-model="form.release_date" class="w-full border p-2 rounded-lg text-sm outline-none" />
 </div>
 </div>
 </div>
 </div>

 <!-- Track Selector (Dual Listbox) -->
 <div class="border-t border-theme-border pt-6 mt-6">
 <h3 class="text-sm font-bold text-theme-text-sec mb-4 uppercase">Gán Bài Hát Vào Album</h3>
 
 <div v-if="!form.artist_id" class="p-4 bg-theme-warning/10 text-theme-warning text-sm rounded-lg border border-theme-warning/20 text-center">
 {{ $t('admin.albums.select_artist_first') }}
 </div>
 
 <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
 <!-- Left: Unassigned -->
 <div class="border border-theme-border rounded-xl overflow-hidden flex flex-col h-64 bg-theme-surface-hover">
 <div class="bg-theme-surface-hover border-b px-3 py-2 text-xs font-bold text-theme-text-sec uppercase flex justify-between items-center">
 {{ $t('admin.albums.unassigned_songs') }}
 <span class="bg-slate-200 px-2 rounded-full">{{ availableSongs.length }}</span>
 </div>
 <div class="p-2 flex-1 overflow-y-auto space-y-1">
 <div v-if="albumStore.isFetchingSongs" class="text-center text-xs p-4 text-theme-text-sec">Đang tải...</div>
 <div v-else-if="availableSongs.length === 0" class="text-center text-xs p-4 text-theme-text-sec">Không có bài hát nào</div>
 
 <div v-for="song in availableSongs" :key="song.id" class="group flex items-center justify-between p-2 bg-theme-surface rounded border border-theme-border hover:border-theme-primary/50 cursor-pointer shadow-[var(--shadow-glow)] transition-all" @click="moveToRight(song)">
 <div class="flex items-center gap-2 truncate">
 <IconMusic size="14" class="text-theme-text-sec" />
 <span class="text-sm text-theme-text-sec truncate">{{ song.title }}</span>
 </div>
 <IconChevronRight size="16" class="text-theme-text-sec group-hover:text-theme-primary" />
 </div>
 </div>
 </div>

 <!-- Right: Assigned -->
 <div class="border border-theme-primary/30 rounded-xl overflow-hidden flex flex-col h-64 bg-theme-info/10/30">
 <div class="bg-theme-primary/10 border-b border-theme-primary/20 px-3 py-2 text-xs font-bold text-theme-primary uppercase flex justify-between items-center">
 {{ $t('admin.albums.album_songs') }}
 <span class="bg-theme-primary text-white px-2 rounded-full">{{ selectedSongs.length }}</span>
 </div>
 <div class="p-2 flex-1 overflow-y-auto space-y-1">
 <div v-if="selectedSongs.length === 0" class="text-center text-xs p-4 text-theme-primary/50">Chưa chọn bài hát nào</div>
 
 <div v-for="(song, idx) in selectedSongs" :key="song.id" class="group flex items-center gap-2 p-2 bg-theme-surface rounded border border-theme-primary/20 hover:border-theme-danger/30 cursor-pointer shadow-[var(--shadow-glow)] transition-all" @click="moveToLeft(song)">
 <div class="text-xs font-bold text-theme-text-sec w-4 text-center">{{ idx + 1 }}</div>
 <div class="flex-1 truncate text-sm text-theme-text-sec">{{ song.title }}</div>
 <IconChevronLeft size="16" class="text-theme-text-sec group-hover:text-theme-danger" />
 </div>
 </div>
 </div>
 </div>
 </div>

 <!-- Error Message -->
 <div v-if="albumStore.error" class="p-3 bg-theme-danger/10 text-theme-danger text-sm rounded-lg">
 {{ albumStore.error }}
 </div>

 <div class="flex justify-end gap-3 pt-4">
 <BaseAdminButton type="button" variant="secondary" @click="emit('cancel')">Hủy bỏ</BaseAdminButton>
 <BaseAdminButton type="submit" variant="primary" :loading="albumStore.isLoading">Lưu Album</BaseAdminButton>
 </div>
 </form>
</template>
