import axios from 'axios';
import { useAuthStore } from '../stores/authStore';

const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1',
    withCredentials: true,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
});

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            const authStore = useAuthStore();
            authStore.clearAuth();
            window.location.href = '/login';
        }
        if (error.response?.status === 403) {
            alert("Bạn không có quyền truy cập khu vực này!");
        }
        return Promise.reject(error);
    }
);

export default api;
