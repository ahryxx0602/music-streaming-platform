import { defineStore } from 'pinia';
import api from '../services/api';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        isAuthenticated: false,
    }),
    
    actions: {
        async login(credentials) {
            // Get CSRF cookie first (Sanctum SPA requirement)
            await api.get('http://localhost:8000/sanctum/csrf-cookie', { baseURL: '' });
            
            // Send login request
            const response = await api.post('/auth/login', credentials);
            
            // Assuming successful login returns user info
            this.user = response.data.data.user;
            this.isAuthenticated = true;
            
            return response;
        },
        
        async fetchUser() {
            try {
                const response = await api.get('/auth/me');
                this.user = response.data.data;
                this.isAuthenticated = true;
            } catch (error) {
                this.clearAuth();
            }
        },
        
        async logout() {
            try {
                await api.post('/auth/logout');
            } finally {
                this.clearAuth();
            }
        },
        
        clearAuth() {
            this.user = null;
            this.isAuthenticated = false;
        }
    },
    
    persist: true // Require pinia-plugin-persistedstate
});
