import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/services/api';

export const useModerationStore = defineStore('moderation', () => {
  const pendingSongs = ref<any[]>([]);
  const meta = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0
  });
  
  const selectedSong = ref<any>(null);
  const isLoading = ref(false);
  const isProcessing = ref(false);
  const error = ref<string | null>(null);

  const fetchPendingSongs = async (page = 1, search = '') => {
    isLoading.value = true;
    error.value = null;
    try {
      const response = await api.get('/admin/moderation/songs', {
        params: {
          page,
          'filter[search]': search,
          // 'filter[status]': 'Pending' // Backend có thể tự lo việc filter status
        }
      });
      if (response.data && response.data.success) {
        pendingSongs.value = response.data.data.data || response.data.data.items || [];
        meta.value = {
          current_page: response.data.data.current_page || 1,
          last_page: response.data.data.last_page || 1,
          per_page: response.data.data.per_page || 15,
          total: response.data.data.total || 0,
        };
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi tải danh sách chờ duyệt';
    } finally {
      isLoading.value = false;
    }
  };

  const approveSong = async (id: number) => {
    isProcessing.value = true;
    error.value = null;
    try {
      await api.put(`/admin/moderation/songs/${id}/approve`);
      // Lọc bỏ bài hát đã duyệt khỏi mảng
      pendingSongs.value = pendingSongs.value.filter(song => song.id !== id);
      meta.value.total--;
      return true;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi khi duyệt bài hát';
      throw err;
    } finally {
      isProcessing.value = false;
    }
  };

  const rejectSong = async (id: number, reason: string) => {
    isProcessing.value = true;
    error.value = null;
    try {
      await api.put(`/admin/moderation/songs/${id}/reject`, { reject_reason: reason });
      // Lọc bỏ bài hát đã từ chối khỏi mảng
      pendingSongs.value = pendingSongs.value.filter(song => song.id !== id);
      meta.value.total--;
      return true;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi khi từ chối bài hát';
      throw err;
    } finally {
      isProcessing.value = false;
    }
  };

  return {
    pendingSongs,
    meta,
    selectedSong,
    isLoading,
    isProcessing,
    error,
    fetchPendingSongs,
    approveSong,
    rejectSong
  };
});
