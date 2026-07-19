import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/services/api';
import { s3UploadService } from '@/services/s3UploadService';

export const useInventoryStore = defineStore('inventory', () => {
  const songs = ref<any[]>([]);
  const meta = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0
  });
  
  const isLoading = ref(false);
  const error = ref<string | null>(null);

  // Trạng thái upload
  const isUploading = ref(false);
  const uploadProgress = ref(0);
  const uploadStatusText = ref('');

  const fetchSongs = async (page = 1, search = '', status = '', artistId = '') => {
    isLoading.value = true;
    error.value = null;
    try {
      const response = await api.get('/admin/songs', {
        params: {
          page,
          'filter[search]': search,
          'filter[status]': status,
          'filter[artist_id]': artistId
        }
      });
      if (response.data && response.data.success) {
        songs.value = response.data.data.data || response.data.data.items || [];
        meta.value = {
          current_page: response.data.data.current_page || 1,
          last_page: response.data.data.last_page || 1,
          per_page: response.data.data.per_page || 15,
          total: response.data.data.total || 0,
        };
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi tải danh sách bài hát';
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Upload bài hát qua S3 Presigned URL và lưu Metadata
   */
  const uploadSong = async (file: File, metadata: { title: string, artist_id: number, genre_id: number }) => {
    isUploading.value = true;
    uploadProgress.value = 0;
    error.value = null;

    try {
      // 1. Lấy Pre-signed URL
      uploadStatusText.value = 'Đang khởi tạo kết nối...';
      const presignedRes = await api.post('/admin/songs/presigned-url', {
        file_name: file.name,
        content_type: file.type,
        file_size: file.size
      });

      if (!presignedRes.data.success) {
        throw new Error('Không thể lấy URL upload');
      }

      const { upload_url, s3_key } = presignedRes.data.data;

      // 2. Upload file trực tiếp lên S3
      uploadStatusText.value = 'Đang tải tệp lên...';
      await s3UploadService.uploadFile(file, upload_url, (progress) => {
        uploadProgress.value = progress;
      });

      // 3. Lưu Metadata vào Backend
      uploadStatusText.value = 'Đang lưu thông tin...';
      await api.post('/admin/songs', {
        title: metadata.title,
        artist_id: metadata.artist_id,
        genre_id: metadata.genre_id,
        s3_key: s3_key
      });

      // 4. Thành công
      uploadStatusText.value = 'Hoàn tất!';
      await fetchSongs();
      
      return true;
    } catch (err: any) {
      console.error(err);
      error.value = err.response?.data?.message || err.message || 'Lỗi trong quá trình tải lên';
      throw err;
    } finally {
      isUploading.value = false;
    }
  };

  return {
    songs,
    meta,
    isLoading,
    error,
    isUploading,
    uploadProgress,
    uploadStatusText,
    fetchSongs,
    uploadSong
  };
});
