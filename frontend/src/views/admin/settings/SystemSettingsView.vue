<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useSettingStore } from '@/stores/settingStore';
import BaseAdminInput from '@/components/admin/ui/form/BaseAdminInput.vue';
import BaseAdminButton from '@/components/admin/ui/button/BaseAdminButton.vue';
import { 
  IconSettings, IconAdjustments, IconCoin, IconServerCog, IconDeviceFloppy
} from '@tabler/icons-vue';

const store = useSettingStore();
const activeTab = ref('general');
const toastMessage = ref('');

const tabs = [
  { id: 'general', name: 'Thông tin chung', icon: IconAdjustments },
  { id: 'monetization', name: 'Tài chính & Nghệ sĩ', icon: IconCoin },
  { id: 'system', name: 'Hệ thống', icon: IconServerCog },
];

const loadData = async () => {
  await store.fetchSettings();
};

const handleSave = async () => {
  const success = await store.updateSettings(store.settings);
  if (success) {
    toastMessage.value = 'Cập nhật cài đặt thành công!';
    setTimeout(() => { toastMessage.value = ''; }, 3000);
  }
};

onMounted(() => {
  loadData();
});
</script>

<template>
  <div class="p-6">
    <div class="mb-6 flex justify-between items-end">
      <div>
        <h1 class="text-2xl font-bold text-theme-text mb-2 flex items-center gap-2">
          <IconSettings class="text-theme-primary" />
          Cài đặt Hệ thống
        </h1>
        <p class="text-theme-text-sec">Quản lý các thông số cấu hình chung cho toàn bộ nền tảng.</p>
      </div>
      
      <BaseAdminButton 
        variant="primary" 
        :loading="store.isSaving"
        @click="handleSave"
      >
        <template #icon><IconDeviceFloppy /></template>
        Lưu thay đổi
      </BaseAdminButton>
    </div>

    <!-- Toast Success -->
    <div v-if="toastMessage" class="fixed top-4 right-4 z-50 bg-theme-success/10 border border-theme-success text-theme-success px-4 py-3 rounded-xl shadow-lg flex items-center gap-2">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
      {{ toastMessage }}
    </div>

    <div v-if="store.loading" class="flex justify-center p-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-theme-primary"></div>
    </div>

    <div v-else class="flex flex-col md:flex-row gap-6">
      <!-- Sidebar Tabs -->
      <div class="w-full md:w-64 shrink-0 flex flex-col gap-2">
        <button 
          v-for="tab in tabs" :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'flex items-center gap-3 px-4 py-3 rounded-xl transition-colors font-medium text-left',
            activeTab === tab.id 
              ? 'bg-theme-primary/10 text-theme-primary border border-theme-primary/20' 
              : 'text-theme-text-sec hover:bg-theme-surface-hover border border-transparent'
          ]"
        >
          <component :is="tab.icon" size="20" />
          {{ tab.name }}
        </button>
      </div>

      <!-- Main Content -->
      <div class="flex-1 bg-theme-surface rounded-2xl border border-theme-border p-6 shadow-[var(--shadow-glow)]">
        <!-- General Tab -->
        <div v-show="activeTab === 'general'" class="space-y-6 max-w-2xl">
          <h3 class="text-lg font-bold text-theme-text border-b border-theme-border pb-4 mb-6">Thông tin chung</h3>
          <BaseAdminInput 
            v-model="store.settings.site_name" 
            label="Tên nền tảng (Site Name)" 
            placeholder="VD: Harmonia"
          />
          <BaseAdminInput 
            v-model="store.settings.support_email" 
            type="email"
            label="Email chăm sóc khách hàng" 
            placeholder="support@example.com"
          />
        </div>

        <!-- Monetization Tab -->
        <div v-show="activeTab === 'monetization'" class="space-y-6 max-w-2xl">
          <h3 class="text-lg font-bold text-theme-text border-b border-theme-border pb-4 mb-6">Tài chính & Nghệ sĩ</h3>
          <BaseAdminInput 
            v-model="store.settings.artist_revenue_share" 
            type="number"
            label="Tỉ lệ chia sẻ doanh thu cho Nghệ sĩ (%)" 
            placeholder="VD: 70"
          />
          <p class="text-xs text-theme-text-sec -mt-4 mb-4">Nền tảng sẽ giữ lại phần còn lại ({{ 100 - (Number(store.settings.artist_revenue_share) || 0) }}%)</p>

          <BaseAdminInput 
            v-model="store.settings.payout_threshold" 
            type="number"
            label="Ngưỡng rút tiền tối thiểu ($)" 
            placeholder="VD: 50"
          />
        </div>

        <!-- System Tab -->
        <div v-show="activeTab === 'system'" class="space-y-6 max-w-2xl">
          <h3 class="text-lg font-bold text-theme-text border-b border-theme-border pb-4 mb-6">Hệ thống & Kỹ thuật</h3>
          
          <div class="flex items-center justify-between p-4 bg-theme-bg rounded-xl border border-theme-border">
            <div>
              <p class="font-bold text-theme-text">Chế độ Bảo trì (Maintenance Mode)</p>
              <p class="text-sm text-theme-text-sec">Ngăn chặn người dùng truy cập Frontend khi bảo trì server.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" v-model="store.settings.maintenance_mode" class="sr-only peer">
              <div class="w-11 h-6 bg-theme-border rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-theme-danger"></div>
            </label>
          </div>

          <BaseAdminInput 
            v-model="store.settings.max_upload_size" 
            type="number"
            label="Kích thước tệp tải lên tối đa (MB)" 
            placeholder="VD: 20"
          />
        </div>

      </div>
    </div>
  </div>
</template>
