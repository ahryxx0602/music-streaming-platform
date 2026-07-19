import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/services/api';

export const usePlaylistStore = defineStore('playlist', () => {
  const playlists = ref<any[]>([]);
  const meta = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0
  });
  
  const availableSongs = ref<any[]>([]);
  const isLoading = ref(false);
  const isSearchingSongs = ref(false);
  const error = ref<string | null>(null);

  const fetchPlaylists = async (page = 1, search = '') => {
    isLoading.value = true;
    error.value = null;
    try {
      const response = await api.get('/admin/playlists', {
        params: {
          page,
          'filter[search]': search,
        }
      });
      if (response.data && response.data.success) {
        playlists.value = response.data.data.data || response.data.data.items || [];
        meta.value = {
          current_page: response.data.data.current_page || 1,
          last_page: response.data.data.last_page || 1,
          per_page: response.data.data.per_page || 15,
          total: response.data.data.total || 0,
        };
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi tải danh sách Playlist';
    } finally {
      isLoading.value = false;
    }
  };

  const searchSongs = async (query: string) => {
    if (!query) {
      availableSongs.value = [];
      return;
    }
    isSearchingSongs.value = true;
    try {
      const response = await api.get('/admin/songs', {
        params: { 
          'filter[search]': query,
          'filter[status]': 'Approved', // Ràng buộc rule [RULE-ADM-PL-UI-01]
          per_page: 20
        }
      });
      if (response.data && response.data.success) {
        availableSongs.value = response.data.data.data || response.data.data.items || [];
      }
    } catch (err: any) {
      console.error('Lỗi tìm bài hát:', err);
      availableSongs.value = [];
    } finally {
      isSearchingSongs.value = false;
    }
  };

  const savePlaylist = async (id: number | null, payload: FormData | any) => {
    isLoading.value = true;
    error.value = null;
    try {
      if (id) {
        // Có thể cần method spoofing nếu dùng form data
        if (payload instanceof FormData) {
          payload.append('_method', 'PUT');
        }
        await api.post(`/admin/playlists/${id}`, payload, {
          headers: payload instanceof FormData ? { 'Content-Type': 'multipart/form-data' } : {}
        });
      } else {
        await api.post('/admin/playlists', payload, {
          headers: payload instanceof FormData ? { 'Content-Type': 'multipart/form-data' } : {}
        });
      }
      await fetchPlaylists();
      return true;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi khi lưu playlist';
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  const deletePlaylist = async (id: number) => {
    isLoading.value = true;
    error.value = null;
    try {
      await api.delete(`/admin/playlists/${id}`);
      await fetchPlaylists();
      return true;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi khi xóa playlist';
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  return {
    playlists,
    meta,
    availableSongs,
    isLoading,
    isSearchingSongs,
    error,
    fetchPlaylists,
    searchSongs,
    savePlaylist,
    deletePlaylist
  };
});
