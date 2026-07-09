import axios from 'axios';

const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1',
    withCredentials: true,
});

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            window.location.href = '/login';
        }
        if (error.response?.status === 403) {
            alert("Bạn không có quyền truy cập khu vực này!");
        }
        return Promise.reject(error);
    }
);

export default api;
