import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/services/api';

export const useAlbumStore = defineStore('album', () => {
  const albums = ref<any[]>([]);
  const meta = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0
  });
  
  const unassignedSongs = ref<any[]>([]);
  const isLoading = ref(false);
  const isFetchingSongs = ref(false);
  const error = ref<string | null>(null);

  const fetchAlbums = async (page = 1, search = '', artistId = '') => {
    isLoading.value = true;
    error.value = null;
    try {
      const response = await api.get('/admin/albums', {
        params: {
          page,
          'filter[search]': search,
          'filter[artist_id]': artistId
        }
      });
      if (response.data && response.data.success) {
        albums.value = response.data.data.data || response.data.data.items || [];
        meta.value = {
          current_page: response.data.data.current_page || 1,
          last_page: response.data.data.last_page || 1,
          per_page: response.data.data.per_page || 15,
          total: response.data.data.total || 0,
        };
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi tải danh sách album';
    } finally {
      isLoading.value = false;
    }
  };

  const fetchUnassignedSongs = async (artistId: number) => {
    if (!artistId) {
      unassignedSongs.value = [];
      return;
    }
    isFetchingSongs.value = true;
    try {
      const response = await api.get('/admin/songs/unassigned', {
        params: { artist_id: artistId }
      });
      if (response.data && response.data.success) {
        // Backend trả về nguyên danh sách ko phân trang
        unassignedSongs.value = response.data.data || [];
      }
    } catch (err: any) {
      console.error('Lỗi tải bài hát lẻ:', err);
      unassignedSongs.value = [];
    } finally {
      isFetchingSongs.value = false;
    }
  };

  const saveAlbum = async (id: number | null, payload: FormData) => {
    isLoading.value = true;
    error.value = null;
    try {
      if (id) {
        payload.append('_method', 'PUT'); // Method spoofing for form-data
        await api.post(`/admin/albums/${id}`, payload, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
      } else {
        await api.post('/admin/albums', payload, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
      }
      await fetchAlbums();
      return true;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi khi lưu album';
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  const deleteAlbum = async (id: number) => {
    isLoading.value = true;
    error.value = null;
    try {
      await api.delete(`/admin/albums/${id}`);
      await fetchAlbums();
      return true;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi khi xóa album';
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  return {
    albums,
    meta,
    unassignedSongs,
    isLoading,
    isFetchingSongs,
    error,
    fetchAlbums,
    fetchUnassignedSongs,
    saveAlbum,
    deleteAlbum
  };
});
