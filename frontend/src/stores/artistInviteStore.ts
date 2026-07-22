import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/services/api';

export const useArtistInviteStore = defineStore('artistInvite', () => {
  const invites = ref<any[]>([]);
  const meta = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0
  });
  
  const isLoading = ref(false);
  const error = ref<string | null>(null);

  const fetchInvites = async (page = 1, search = '') => {
    isLoading.value = true;
    error.value = null;
    try {
      const response = await api.get('/admin/artist-invites', {
        params: {
          page,
          'filter[search]': search,
        }
      });
      if (response.data && response.data.success) {
        invites.value = response.data.data.data || response.data.data.items || [];
        meta.value = {
          current_page: response.data.data.current_page || 1,
          last_page: response.data.data.last_page || 1,
          per_page: response.data.data.per_page || 15,
          total: response.data.data.total || 0,
        };
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi tải danh sách mã mời';
    } finally {
      isLoading.value = false;
    }
  };

  const createInvite = async (payload: { email?: string; valid_days: number }) => {
    isLoading.value = true;
    error.value = null;
    try {
      const response = await api.post('/admin/artist-invites', { email: payload.email, expires_in_days: payload.valid_days });
      // Lấy link trả về (Giả sử Backend trả về link ở data.registration_url)
      const registrationUrl = response.data.data?.registration_url || response.data.registration_url;
      
      await fetchInvites(1); // Reset về trang 1 để xem invite mới
      return registrationUrl;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi khi tạo mã mời';
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  const revokeInvite = async (id: number) => {
    isLoading.value = true;
    error.value = null;
    try {
      await api.delete(`/admin/artist-invites/${id}`);
      await fetchInvites(meta.value.current_page);
      return true;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi khi thu hồi mã mời';
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  return {
    invites,
    meta,
    isLoading,
    error,
    fetchInvites,
    createInvite,
    revokeInvite
  };
});
