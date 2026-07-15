import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '../services/api';

export const useAuthStore = defineStore('auth', () => {
  const user = ref<any | null>(null);
  const isAuthenticated = ref<boolean>(false);
  const role = ref<string>('');

  const fetchProfile = async (currentRole: string) => {
    try {
      // API Me: /api/v1/{role}/auth/me
      const response = await api.get(`/${currentRole.toLowerCase()}/auth/me`);
      user.value = response.data.data.user; // JSend format
      isAuthenticated.value = true;
      role.value = currentRole;
    } catch (error) {
      user.value = null;
      isAuthenticated.value = false;
      role.value = '';
      throw error;
    }
  };

  const login = async (credentials: Record<string, string>) => {
    const backendUrl = import.meta.env.VITE_BACKEND_URL || 'http://localhost:8000';
    await api.get(`${backendUrl}/sanctum/csrf-cookie`);

    // Trích xuất thủ công XSRF-TOKEN để đảm bảo Axios có token
    let csrfToken = null;
    const match = document.cookie.match(new RegExp('(^|;\\s*)(XSRF-TOKEN)=([^;]*)'));
    if (match && match[3]) {
        csrfToken = decodeURIComponent(match[3]);
    }

    // Gửi request tới [API-002] kèm header X-XSRF-TOKEN
    const response = await api.post('/guest/auth/login', credentials, {
        headers: csrfToken ? { 'X-XSRF-TOKEN': csrfToken } : {}
    });
    const userRole = response.data.data.user.role;
    
    // Gọi API Me tương ứng
    await fetchProfile(userRole);
  };

  const logout = async () => {
    if (!role.value) {
      clearAuth();
      return;
    }
    
    try {
      // Gọi API Logout [API-051]
      await api.post(`/${role.value.toLowerCase()}/auth/logout`);
    } finally {
      clearAuth();
    }
  };

  const clearAuth = () => {
    user.value = null;
    isAuthenticated.value = false;
    role.value = '';
    window.location.href = '/login';
  };

  return {
    user,
    isAuthenticated,
    role,
    login,
    fetchProfile,
    logout
  };
});
