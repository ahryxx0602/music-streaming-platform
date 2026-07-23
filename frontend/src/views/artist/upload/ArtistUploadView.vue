<script setup lang="ts">
import { ref, reactive, computed, onUnmounted } from 'vue';
import { 
  IconUpload, 
  IconPhotoEdit, 
  IconMusic,
  IconX,
  IconCheck,
  IconAlertCircle
} from '@tabler/icons-vue';
import { useAuthStore } from '@/stores/authStore';
import api from '@/services/api';
import { s3UploadService } from '@/services/s3UploadService';
import axios, { type CancelTokenSource } from 'axios';
import { useToast } from 'vue-toastification';

const authStore = useAuthStore();
const toast = useToast();

const MAX_AUDIO_SIZE = 50 * 1024 * 1024; // 50MB
const ALLOWED_AUDIO_TYPES = ['audio/mpeg', 'audio/wav', 'audio/x-wav'];

const MAX_COVER_SIZE = 5 * 1024 * 1024; // 5MB
const ALLOWED_COVER_TYPES = ['image/jpeg', 'image/png', 'image/jpg'];

// Form State
const formData = reactive({
  title: '',
  genre_id: '',
  lyrics: '',
});

// File State
const audioFile = ref<File | null>(null);
const coverFile = ref<File | null>(null);
const coverPreviewUrl = ref<string | null>(null);
const audioDuration = ref<number>(0);

// Upload State
const uploadStatus = ref<'idle' | 'uploading' | 'success' | 'error' | 'canceled'>('idle');
const uploadProgress = ref(0);
const errorMessage = ref('');

// Cancel Token
let cancelTokenSource: CancelTokenSource | null = null;

// Genres
const genres = ref<{ id: number, name: string }[]>([]);

// Temporary mock for genres (should fetch from API in real implementation)
genres.value = [
  { id: 1, name: 'Pop' },
  { id: 2, name: 'Rock' },
  { id: 3, name: 'Hip Hop / Rap' },
  { id: 4, name: 'Electronic / EDM' },
  { id: 5, name: 'R&B' }
];

// Computed
const formattedDuration = computed(() => {
  if (!audioDuration.value) return '00:00';
  const minutes = Math.floor(audioDuration.value / 60);
  const seconds = Math.floor(audioDuration.value % 60);
  return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
});

const isFormValid = computed(() => {
  return formData.title.trim() !== '' && formData.genre_id !== '' && audioFile.value !== null;
});

// Audio Validation & Duration Extraction
const handleAudioDrop = (e: DragEvent) => {
  e.preventDefault();
  const file = e.dataTransfer?.files?.[0];
  if (file) {
    processAudioFile(file);
  }
};

const handleAudioSelect = (e: Event) => {
  const target = e.target as HTMLInputElement;
  const file = target.files?.[0];
  if (file) {
    processAudioFile(file);
  }
};

const processAudioFile = (file: File) => {
  if (!ALLOWED_AUDIO_TYPES.includes(file.type)) {
    toast.error('Chỉ hỗ trợ file MP3 hoặc WAV!');
    return;
  }
  if (file.size > MAX_AUDIO_SIZE) {
    toast.error('File không được vượt quá 50MB!');
    return;
  }

  audioFile.value = file;
  if (!formData.title) {
    // Auto fill title without extension
    formData.title = file.name.replace(/\.[^/.]+$/, "");
  }

  // Extract duration using Web Audio API (hidden audio element)
  const objectUrl = URL.createObjectURL(file);
  const audio = new Audio(objectUrl);
  audio.onloadedmetadata = () => {
    audioDuration.value = audio.duration;
    URL.revokeObjectURL(objectUrl); // Clean up
  };
};

// Cover Validation & Preview
const handleCoverSelect = (e: Event) => {
  const target = e.target as HTMLInputElement;
  const file = target.files?.[0];
  if (file) {
    
    if (!ALLOWED_COVER_TYPES.includes(file.type)) {
      toast.error('Ảnh bìa phải là JPG hoặc PNG!');
      return;
    }
    if (file.size > MAX_COVER_SIZE) {
      toast.error('Ảnh bìa không được vượt quá 5MB!');
      return;
    }

    coverFile.value = file;
    coverPreviewUrl.value = URL.createObjectURL(file);
  }
};

// Upload Logic
const submitUpload = async () => {
  if (!isFormValid.value || !audioFile.value) return;

  uploadStatus.value = 'uploading';
  uploadProgress.value = 0;
  errorMessage.value = '';

  cancelTokenSource = axios.CancelToken.source();

  try {
    // 1. Get Presigned URL
    const { data: presignedData } = await api.post('/v1/artist/songs/presigned-url', {
      file_name: audioFile.value.name,
      content_type: audioFile.value.type
    });
    
    const { url, path } = presignedData.data;

    // 2. Upload directly to S3
    // Note: We need to use raw axios here to avoid our API interceptors (like Bearer token) 
    // which AWS S3 would reject. Also attaching the cancel token.
    await axios.put(url, audioFile.value, {
      headers: {
        'Content-Type': audioFile.value.type,
      },
      onUploadProgress: (progressEvent) => {
        if (progressEvent.total) {
          uploadProgress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total);
        }
      },
      cancelToken: cancelTokenSource.token
    });

    // 3. Submit Metadata to our Backend
    const metaData = new FormData();
    metaData.append('title', formData.title);
    metaData.append('genre_id', formData.genre_id);
    metaData.append('audio_path', path); // Pass the S3 path instead of the file
    if (coverFile.value) metaData.append('cover_image', coverFile.value);
    if (formData.lyrics) metaData.append('lyrics', formData.lyrics);
    // Note: Backend handles duration now via FFmpeg Job, but we could still pass it if needed.

    await api.post('/v1/artist/songs', metaData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    uploadStatus.value = 'success';
    toast.success('Đã tải nhạc lên thành công! Backend đang xử lý âm thanh.');
    
    // Reset form after 3s
    setTimeout(() => {
      resetForm();
    }, 3000);

  } catch (error: any) {
    if (axios.isCancel(error)) {
      uploadStatus.value = 'canceled';
      toast.info('Đã hủy tải lên.');
    } else {
      uploadStatus.value = 'error';
      errorMessage.value = error.response?.data?.message || 'Có lỗi xảy ra khi tải lên. Vui lòng thử lại!';
      toast.error(errorMessage.value);
    }
  }
};

const cancelUpload = () => {
  if (cancelTokenSource) {
    cancelTokenSource.cancel('User canceled upload');
  }
};

const resetForm = () => {
  formData.title = '';
  formData.genre_id = '';
  formData.lyrics = '';
  audioFile.value = null;
  coverFile.value = null;
  if (coverPreviewUrl.value) URL.revokeObjectURL(coverPreviewUrl.value);
  coverPreviewUrl.value = null;
  audioDuration.value = 0;
  uploadStatus.value = 'idle';
  uploadProgress.value = 0;
};

onUnmounted(() => {
  if (coverPreviewUrl.value) {
    URL.revokeObjectURL(coverPreviewUrl.value);
  }
});
</script>

<template>
  <div class="p-8 max-w-6xl mx-auto w-full pb-24">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-heading font-bold text-white tracking-wide">
        Tải bài hát mới
      </h1>
      <p class="text-theme-text-sec text-sm mt-1">
        Hãy để thế giới lắng nghe giai điệu của bạn. File hỗ trợ: MP3, WAV (Max 50MB).
      </p>
    </div>

    <!-- Main Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
      
      <!-- Left Column: Files (Col span 5) -->
      <div class="lg:col-span-5 flex flex-col gap-6">
        
        <!-- Cover Image Uploader -->
        <div class="bg-theme-surface/40 backdrop-blur-md border border-white/10 shadow-[0_4px_30px_rgba(0,0,0,0.1)] rounded-2xl p-6 flex flex-col items-center justify-center relative overflow-hidden group aspect-square">
          <input 
            type="file" 
            accept="image/jpeg,image/png,image/jpg" 
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
            @change="handleCoverSelect"
            :disabled="uploadStatus === 'uploading'"
          />
          
          <div v-if="coverPreviewUrl" class="absolute inset-0 w-full h-full">
            <img :src="coverPreviewUrl" class="w-full h-full object-cover" />
            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
              <span class="text-white font-medium flex items-center gap-2">
                <IconPhotoEdit size="20" /> Thay đổi ảnh bìa
              </span>
            </div>
          </div>
          
          <div v-else class="text-center flex flex-col items-center gap-3 text-theme-text-sec group-hover:text-theme-accent transition-colors">
            <div class="w-16 h-16 rounded-full bg-theme-bg border-2 border-dashed border-theme-border flex items-center justify-center group-hover:border-theme-accent group-hover:bg-theme-accent/10 transition-colors">
              <IconPhotoEdit size="32" />
            </div>
            <div>
              <p class="font-medium text-white">Tải ảnh bìa (Tùy chọn)</p>
              <p class="text-xs mt-1">Tỷ lệ 1:1 • JPG, PNG • Max 5MB</p>
            </div>
          </div>
        </div>

        <!-- Audio File Dropzone -->
        <div 
          class="bg-theme-surface/40 backdrop-blur-md border-2 border-dashed rounded-2xl p-8 text-center transition-all duration-300 relative"
          :class="[
            audioFile ? 'border-theme-accent bg-theme-accent/5 shadow-[var(--shadow-glow)]' : 'border-theme-border hover:border-theme-accent/50 hover:bg-white/5'
          ]"
          @dragover.prevent
          @drop="handleAudioDrop"
        >
          <input 
            type="file" 
            accept="audio/mpeg,audio/wav,audio/x-wav" 
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
            @change="handleAudioSelect"
            :disabled="uploadStatus === 'uploading'"
          />
          
          <div v-if="audioFile" class="flex flex-col items-center gap-2 relative z-0">
            <div class="w-12 h-12 rounded-full bg-theme-accent/20 text-theme-accent flex items-center justify-center">
              <IconMusic size="24" />
            </div>
            <p class="font-medium text-theme-accent truncate w-full px-4">{{ audioFile.name }}</p>
            <p class="text-xs text-theme-text-sec">
              {{ (audioFile.size / (1024 * 1024)).toFixed(2) }} MB • Thời lượng: {{ formattedDuration }}
            </p>
            <button 
              @click.stop="audioFile = null" 
              class="absolute top-0 right-0 p-1 text-theme-text-sec hover:text-theme-danger hover:bg-theme-danger/10 rounded-full z-20"
              :disabled="uploadStatus === 'uploading'"
            >
              <IconX size="16" />
            </button>
          </div>
          
          <div v-else class="flex flex-col items-center gap-3 relative z-0 text-theme-text-sec">
            <IconUpload size="32" class="opacity-70" />
            <div>
              <p class="font-medium text-white">Kéo thả file nhạc vào đây</p>
              <p class="text-xs mt-1">hoặc click để duyệt file</p>
            </div>
          </div>
        </div>

      </div>

      <!-- Right Column: Metadata (Col span 7) -->
      <div class="lg:col-span-7">
        <div class="bg-theme-surface/40 backdrop-blur-md border border-white/10 shadow-[0_4px_30px_rgba(0,0,0,0.1)] rounded-2xl p-8">
          
          <h2 class="text-xl font-bold text-white mb-6">Thông tin bài hát</h2>
          
          <form @submit.prevent="submitUpload" class="space-y-6">
            
            <!-- Title -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-theme-label">Tiêu đề bài hát <span class="text-theme-danger">*</span></label>
              <input 
                v-model="formData.title" 
                type="text" 
                placeholder="Ví dụ: Lạc Trôi..." 
                class="w-full bg-theme-input-bg border border-theme-input-border text-theme-input-text rounded-xl px-4 py-3 focus:outline-none focus:border-theme-primary focus:ring-1 focus:ring-theme-primary transition-colors"
                :disabled="uploadStatus === 'uploading'"
                required
              />
            </div>

            <!-- Genre -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-theme-label">Thể loại <span class="text-theme-danger">*</span></label>
              <select 
                v-model="formData.genre_id" 
                class="w-full bg-theme-input-bg border border-theme-input-border text-theme-input-text rounded-xl px-4 py-3 focus:outline-none focus:border-theme-primary focus:ring-1 focus:ring-theme-primary transition-colors appearance-none"
                :disabled="uploadStatus === 'uploading'"
                required
              >
                <option value="" disabled>Chọn thể loại...</option>
                <option v-for="genre in genres" :key="genre.id" :value="genre.id">
                  {{ genre.name }}
                </option>
              </select>
            </div>

            <!-- Lyrics -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-theme-label">Lời bài hát (Tùy chọn)</label>
              <textarea 
                v-model="formData.lyrics" 
                rows="5"
                placeholder="Nhập lời bài hát vào đây..." 
                class="w-full bg-theme-input-bg border border-theme-input-border text-theme-input-text rounded-xl px-4 py-3 focus:outline-none focus:border-theme-primary focus:ring-1 focus:ring-theme-primary transition-colors resize-none"
                :disabled="uploadStatus === 'uploading'"
              ></textarea>
            </div>

            <!-- Upload Progress / Status -->
            <div v-if="uploadStatus !== 'idle'" class="p-4 rounded-xl" :class="{
              'bg-theme-surface/50 border border-theme-border': uploadStatus === 'uploading',
              'bg-theme-success/10 border border-theme-success/30': uploadStatus === 'success',
              'bg-theme-danger/10 border border-theme-danger/30': uploadStatus === 'error' || uploadStatus === 'canceled'
            }">
              
              <!-- Uploading state -->
              <div v-if="uploadStatus === 'uploading'" class="space-y-3">
                <div class="flex justify-between items-center text-sm font-medium text-white">
                  <span>Đang tải lên...</span>
                  <span>{{ uploadProgress }}%</span>
                </div>
                <div class="w-full h-2 bg-theme-bg rounded-full overflow-hidden">
                  <div class="h-full bg-theme-accent transition-all duration-300 relative overflow-hidden" :style="{ width: uploadProgress + '%' }">
                    <div class="absolute inset-0 bg-[linear-gradient(45deg,rgba(255,255,255,0.15)_25%,transparent_25%,transparent_50%,rgba(255,255,255,0.15)_50%,rgba(255,255,255,0.15)_75%,transparent_75%,transparent)] bg-[length:1rem_1rem] animate-[progress_1s_linear_infinite]"></div>
                  </div>
                </div>
                <button type="button" @click="cancelUpload" class="text-xs text-theme-danger hover:underline">Hủy tải lên</button>
              </div>

              <!-- Success state -->
              <div v-if="uploadStatus === 'success'" class="flex items-center gap-3 text-theme-success">
                <IconCheck size="20" />
                <span class="font-medium">Tải nhạc thành công! Đang xử lý âm thanh.</span>
              </div>

              <!-- Error state -->
              <div v-if="uploadStatus === 'error' || uploadStatus === 'canceled'" class="flex items-center gap-3 text-theme-danger">
                <IconAlertCircle size="20" />
                <span class="font-medium">{{ errorMessage || 'Đã hủy tải lên.' }}</span>
              </div>
              
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
              <button 
                type="submit" 
                class="w-full py-4 rounded-xl font-bold text-white transition-all duration-300"
                :class="[
                  !isFormValid || uploadStatus === 'uploading' 
                    ? 'bg-theme-border/50 cursor-not-allowed opacity-50' 
                    : 'bg-gradient-to-r from-theme-secondary to-theme-accent hover:shadow-[var(--shadow-glow)] hover:-translate-y-0.5 active:translate-y-0'
                ]"
                :disabled="!isFormValid || uploadStatus === 'uploading'"
              >
                <span v-if="uploadStatus === 'uploading'" class="flex items-center justify-center gap-2">
                  <span class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                  ĐANG TẢI LÊN...
                </span>
                <span v-else-if="uploadStatus === 'error' || uploadStatus === 'canceled'">THỬ LẠI</span>
                <span v-else>XÁC NHẬN & TẢI LÊN</span>
              </button>
            </div>

          </form>
        </div>
      </div>

    </div>
  </div>
</template>

<style scoped>
@keyframes progress {
  0% { background-position: 1rem 0; }
  100% { background-position: 0 0; }
}
</style>
