import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useSettingStore = defineStore('setting', () => {
    const settings = ref<Record<string, any>>({});
    const loading = ref(false);
    const isSaving = ref(false);

    const fetchSettings = async () => {
        loading.value = true;
        try {
            const response = await axios.get('/api/v1/admin/settings');
            settings.value = response.data.data;
            
            // Convert string boolean back to boolean for toggle
            if (settings.value.maintenance_mode === 'true') settings.value.maintenance_mode = true;
            if (settings.value.maintenance_mode === 'false') settings.value.maintenance_mode = false;

        } catch (error) {
            console.error('Failed to fetch settings', error);
        } finally {
            loading.value = false;
        }
    };

    const updateSettings = async (payload: Record<string, any>) => {
        isSaving.value = true;
        try {
            const response = await axios.put('/api/v1/admin/settings', { settings: payload });
            settings.value = response.data.data;
            
            if (settings.value.maintenance_mode === 'true') settings.value.maintenance_mode = true;
            if (settings.value.maintenance_mode === 'false') settings.value.maintenance_mode = false;
            
            return true;
        } catch (error) {
            console.error('Failed to update settings', error);
            return false;
        } finally {
            isSaving.value = false;
        }
    };

    return {
        settings,
        loading,
        isSaving,
        fetchSettings,
        updateSettings
    };
});
