import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/services/api';

export const useBannerStore = defineStore('banner', () => {
  const banners = ref<any[]>([]);
  const isLoading = ref(false);
  const error = ref<string | null>(null);

  const fetchBanners = async () => {
    isLoading.value = true;
    error.value = null;
    try {
      const response = await api.get('/admin/banners');
      if (response.data && response.data.success) {
        banners.value = response.data.data || [];
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi tải danh sách Banner';
    } finally {
      isLoading.value = false;
    }
  };

  const saveBanner = async (id: number | null, payload: FormData | any) => {
    isLoading.value = true;
    error.value = null;
    try {
      if (id) {
        // Cập nhật
        if (payload instanceof FormData) {
          payload.append('_method', 'PUT');
        }
        await api.post(`/admin/banners/${id}`, payload, {
          headers: payload instanceof FormData ? { 'Content-Type': 'multipart/form-data' } : {}
        });
      } else {
        // Tạo mới
        await api.post('/admin/banners', payload, {
          headers: payload instanceof FormData ? { 'Content-Type': 'multipart/form-data' } : {}
        });
      }
      await fetchBanners();
      return true;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi khi lưu banner';
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  const reorderBanners = async (orderedIds: number[]) => {
    error.value = null;
    try {
      await api.put('/admin/banners/reorder', { banner_ids: orderedIds });
      return true;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi khi cập nhật thứ tự banner';
      throw err;
    }
  };

  const toggleStatus = async (id: number, isActive: boolean) => {
    const payload = new FormData();
    payload.append('is_active', isActive ? '1' : '0');
    payload.append('_method', 'PUT');
    
    // Gọi PUT mà không refresh toàn bộ table để UI không giật
    try {
      await api.post(`/admin/banners/${id}`, payload);
      const index = banners.value.findIndex(b => b.id === id);
      if (index !== -1) {
        banners.value[index].is_active = isActive;
      }
      return true;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi đổi trạng thái banner';
      throw err;
    }
  };

  const deleteBanner = async (id: number) => {
    isLoading.value = true;
    error.value = null;
    try {
      await api.delete(`/admin/banners/${id}`);
      await fetchBanners();
      return true;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi khi xóa banner';
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  return {
    banners,
    isLoading,
    error,
    fetchBanners,
    saveBanner,
    reorderBanners,
    toggleStatus,
    deleteBanner
  };
});
