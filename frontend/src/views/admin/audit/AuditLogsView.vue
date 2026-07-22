<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useAuditLogStore } from '@/stores/auditLogStore';
import AdminDataTable from '@/components/admin/shared/AdminDataTable.vue';
import StatusBadge from '@/components/admin/ui/data-display/StatusBadge.vue';
import BaseAdminInput from '@/components/admin/ui/form/BaseAdminInput.vue';
import BaseAdminSelect from '@/components/admin/ui/form/BaseAdminSelect.vue';
import BaseModal from '@/components/admin/ui/modal/BaseModal.vue';
import { IconHistory, IconEye } from '@tabler/icons-vue';

const store = useAuditLogStore();
const selectedLog = ref<any>(null);
const isDiffModalOpen = ref(false);

const columns = [
  { key: 'user', label: 'Người thực hiện' },
  { key: 'action', label: 'Hành động' },
  { key: 'entity', label: 'Đối tượng' },
  { key: 'created_at', label: 'Thời gian' },
  { key: 'actions', label: '' }
];

const filters = ref({
  action: '',
  module: '',
  date_from: '',
  date_to: ''
});

const actionOptions = [
  { value: '', label: 'Tất cả hành động' },
  { value: 'created', label: 'Tạo mới (Created)' },
  { value: 'updated', label: 'Cập nhật (Updated)' },
  { value: 'deleted', label: 'Đã xóa (Deleted)' },
  { value: 'approved', label: 'Phê duyệt (Approved)' },
  { value: 'rejected', label: 'Từ chối (Rejected)' }
];

const moduleOptions = [
  { value: '', label: 'Tất cả module' },
  { value: 'Song', label: 'Bài hát (Song)' },
  { value: 'User', label: 'Người dùng (User)' },
  { value: 'Genre', label: 'Thể loại (Genre)' },
  { value: 'Banner', label: 'Banner' }
];

const loadData = () => {
  store.fetchLogs(filters.value);
};

const handleFilter = () => {
  loadData();
};

const getActionColor = (action: string) => {
  switch (action) {
    case 'created':
    case 'approved':
      return 'active'; // mapped to success
    case 'updated':
      return 'suspended'; // mapped to warning
    case 'deleted':
    case 'rejected':
      return 'banned'; // mapped to danger
    default:
      return 'pending'; // mapped to info
  }
};

const formatDate = (dateString: string) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleString('vi-VN');
};

const viewDiff = (log: any) => {
  selectedLog.value = log;
  isDiffModalOpen.value = true;
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
          <IconHistory class="text-theme-primary" />
          Nhật ký Hệ thống (Audit Logs)
        </h1>
        <p class="text-theme-text-sec">Theo dõi mọi hoạt động thay đổi dữ liệu trong hệ thống.</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 p-4 bg-theme-surface rounded-2xl border border-theme-border">
      <BaseAdminSelect
        v-model="filters.action"
        :options="actionOptions"
        label="Hành động"
        @change="handleFilter"
      />
      <BaseAdminSelect
        v-model="filters.module"
        :options="moduleOptions"
        label="Phân hệ (Module)"
        @change="handleFilter"
      />
      <BaseAdminInput
        v-model="filters.date_from"
        type="date"
        label="Từ ngày"
        @change="handleFilter"
      />
      <BaseAdminInput
        v-model="filters.date_to"
        type="date"
        label="Đến ngày"
        @change="handleFilter"
      />
    </div>

    <!-- Data Table -->
    <AdminDataTable
      :columns="columns"
      :data="store.logs"
      :loading="store.loading"
    >
      <template #cell-user="{ item }">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-theme-primary/10 flex items-center justify-center text-theme-primary font-bold">
            {{ item.user?.name?.charAt(0) || 'U' }}
          </div>
          <div>
            <p class="font-bold text-theme-text text-sm">{{ item.user?.name || 'System' }}</p>
            <p class="text-xs text-theme-text-sec">{{ item.user?.email || item.ip_address }}</p>
          </div>
        </div>
      </template>

      <template #cell-action="{ item }">
        <StatusBadge :status="getActionColor(item.action)">
          {{ item.action.toUpperCase() }}
        </StatusBadge>
      </template>

      <template #cell-entity="{ item }">
        <p class="font-medium text-theme-text text-sm">{{ item.entity_type?.split('\\').pop() || 'Unknown' }}</p>
        <p class="text-xs text-theme-text-sec">ID: {{ item.entity_id }}</p>
      </template>

      <template #cell-created_at="{ item }">
        <span class="text-sm text-theme-text-sec">{{ formatDate(item.created_at) }}</span>
      </template>

      <template #cell-actions="{ item }">
        <button 
          v-if="item.old_values || item.new_values"
          @click="viewDiff(item)"
          class="p-2 text-theme-text-sec hover:text-theme-primary transition-colors rounded-lg hover:bg-theme-bg"
          title="Xem chi tiết thay đổi"
        >
          <IconEye size="20" />
        </button>
      </template>
    </AdminDataTable>

    <!-- Pagination -->
    <div class="mt-4 flex justify-between items-center text-sm text-theme-text-sec">
      <div>Hiển thị trang {{ store.pagination.current_page }} / {{ store.pagination.last_page }}</div>
      <div class="flex gap-2">
        <button 
          :disabled="store.pagination.current_page === 1"
          @click="store.pagination.current_page--; loadData()"
          class="px-3 py-1 rounded-md border border-theme-border hover:bg-theme-bg disabled:opacity-50"
        >Trước</button>
        <button 
          :disabled="store.pagination.current_page === store.pagination.last_page"
          @click="store.pagination.current_page++; loadData()"
          class="px-3 py-1 rounded-md border border-theme-border hover:bg-theme-bg disabled:opacity-50"
        >Sau</button>
      </div>
    </div>

    <!-- Diff Modal -->
    <BaseModal
      v-model="isDiffModalOpen"
      title="Chi tiết Thay đổi"
      size="xl"
    >
      <div v-if="selectedLog" class="p-6">
        <div class="mb-4 text-sm text-theme-text-sec">
          <p><strong>Người thực hiện:</strong> {{ selectedLog.user?.name }} ({{ selectedLog.ip_address }})</p>
          <p><strong>Hành động:</strong> {{ selectedLog.action }} trên {{ selectedLog.entity_type }} (ID: {{ selectedLog.entity_id }})</p>
          <p><strong>Thời gian:</strong> {{ formatDate(selectedLog.created_at) }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Old Values -->
          <div class="bg-theme-danger/5 border border-theme-danger/20 rounded-xl overflow-hidden">
            <div class="px-4 py-2 bg-theme-danger/10 border-b border-theme-danger/20 text-theme-danger font-bold text-sm">
              Dữ liệu Cũ (Old Values)
            </div>
            <pre class="p-4 text-xs text-theme-text-sec overflow-auto max-h-96 whitespace-pre-wrap">{{ selectedLog.old_values || 'Không có' }}</pre>
          </div>

          <!-- New Values -->
          <div class="bg-theme-success/5 border border-theme-success/20 rounded-xl overflow-hidden">
            <div class="px-4 py-2 bg-theme-success/10 border-b border-theme-success/20 text-theme-success font-bold text-sm">
              Dữ liệu Mới (New Values)
            </div>
            <pre class="p-4 text-xs text-theme-text-sec overflow-auto max-h-96 whitespace-pre-wrap">{{ selectedLog.new_values || 'Không có' }}</pre>
          </div>
        </div>
      </div>
    </BaseModal>
  </div>
</template>
