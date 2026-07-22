import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export interface AuditLog {
    id: number;
    user_id: number;
    action: string;
    entity_type: string;
    entity_id: number;
    old_values: any;
    new_values: any;
    ip_address: string;
    created_at: string;
    user: {
        id: number;
        name: string;
        email: string;
    };
}

export const useAuditLogStore = defineStore('auditLog', () => {
    const logs = ref<AuditLog[]>([]);
    const recentLogs = ref<AuditLog[]>([]);
    const loading = ref(false);
    const pagination = ref({
        current_page: 1,
        last_page: 1,
        total: 0,
        per_page: 15
    });

    const fetchLogs = async (params: any = {}) => {
        loading.value = true;
        try {
            const response = await axios.get('/api/v1/admin/audit-logs', { params });
            logs.value = response.data.data.data;
            pagination.value = {
                current_page: response.data.data.current_page,
                last_page: response.data.data.last_page,
                total: response.data.data.total,
                per_page: response.data.data.per_page
            };
        } catch (error) {
            console.error('Failed to fetch audit logs', error);
        } finally {
            loading.value = false;
        }
    };

    const fetchRecentLogs = async () => {
        loading.value = true;
        try {
            const response = await axios.get('/api/v1/admin/dashboard/recent-activities');
            recentLogs.value = response.data.data;
        } catch (error) {
            console.error('Failed to fetch recent activities', error);
        } finally {
            loading.value = false;
        }
    };

    return {
        logs,
        recentLogs,
        loading,
        pagination,
        fetchLogs,
        fetchRecentLogs
    };
});
