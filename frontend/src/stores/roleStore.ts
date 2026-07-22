import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/services/api';

export const useRoleStore = defineStore('role', () => {
  const roles = ref<any[]>([]);
  const permissions = ref<any[]>([]);
  
  const isLoading = ref(false);
  const error = ref<string | null>(null);

  const fetchRoles = async () => {
    isLoading.value = true;
    error.value = null;
    try {
      const response = await api.get('/admin/roles');
      if (response.data && response.data.success) {
        roles.value = response.data.data || [];
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi tải danh sách vai trò';
    } finally {
      isLoading.value = false;
    }
  };

  const fetchPermissions = async () => {
    error.value = null;
    try {
      const response = await api.get('/admin/permissions');
      if (response.data && response.data.success) {
        permissions.value = response.data.data || [];
      }
    } catch (err: any) {
      console.error('Lỗi tải danh sách quyền', err);
    }
  };

  const createRole = async (payload: { name: string; permission_ids: number[] }) => {
    isLoading.value = true;
    error.value = null;
    try {
      await api.post('/admin/roles', payload);
      await fetchRoles();
      return true;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi khi tạo vai trò';
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  const updateRole = async (id: number, payload: { name: string; permission_ids: number[] }) => {
    isLoading.value = true;
    error.value = null;
    try {
      await api.put(`/admin/roles/${id}`, payload);
      await fetchRoles();
      return true;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi khi cập nhật vai trò';
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  const deleteRole = async (id: number) => {
    isLoading.value = true;
    error.value = null;
    try {
      await api.delete(`/admin/roles/${id}`);
      await fetchRoles();
      return true;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi khi xóa vai trò';
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  return {
    roles,
    permissions,
    isLoading,
    error,
    fetchRoles,
    fetchPermissions,
    createRole,
    updateRole,
    deleteRole
  };
});
