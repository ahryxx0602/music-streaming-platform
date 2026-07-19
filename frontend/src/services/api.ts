import axios from 'axios';

const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1',
    withCredentials: true,
});

api.interceptors.request.use((config) => {
    const currentLocale = localStorage.getItem('app-locale') || 'vi';
    config.headers['Accept-Language'] = currentLocale;
    return config;
});

api.interceptors.response.use(
    (response) => response,
    async (error) => {
        if (error.response?.status === 401) {
            window.location.href = '/login';
        }
        if (error.response?.status === 403) {
            alert("Bạn không có quyền truy cập khu vực này!");
        }
        // HTTP 419: CSRF Token Mismatch (Đặc thù bảo mật của Laravel Sanctum SPA)
        if (error.response?.status === 419 && !(error.config as any)._retry) {
            (error.config as any)._retry = true;
            const backendUrl = import.meta.env.VITE_BACKEND_URL || 'http://localhost:8000';
            // Gọi lấy Cookie CSRF
            await axios.get(`${backendUrl}/sanctum/csrf-cookie`, { withCredentials: true });
            
            // Trích xuất XSRF-TOKEN từ cookie và gán vào header của request bị lỗi
            const match = document.cookie.match(new RegExp('(^|;\\s*)(XSRF-TOKEN)=([^;]*)'));
            if (match && match[3]) {
                error.config.headers['X-XSRF-TOKEN'] = decodeURIComponent(match[3]);
            }

            // Sau khi có cookie và token, thực hiện lại chính request
            return api.request(error.config);
        }
        return Promise.reject(error);
    }
);

export default api;
