<script setup lang="ts">
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import { 
  IconSearch, 
  IconPlus, 
  IconLock,
  IconLockOpen,
  IconShield,
  IconCheck,
  IconDotsVertical,
  IconMusic,
  IconFilter,
  IconEdit,
  IconTrash
} from '@tabler/icons-vue';
import ArtistDrawer from '@/components/admin/features/users/drawers/ArtistDrawer.vue';
import BaseAdminButton from '@/components/admin/ui/button/BaseAdminButton.vue';
import EmptyState from '@/components/admin/ui/feedback/EmptyState.vue';

const users = ref<any[]>([]);
const loading = ref(true);
const meta = ref({ current_page: 1, last_page: 1, total: 0 });

const searchQuery = ref('');
const statusFilter = ref('');
const showDrawer = ref(false);
const selectedArtist = ref<any>(null);

const toastMessage = ref('');
const showToast = (msg: string) => {
  toastMessage.value = msg;
  setTimeout(() => toastMessage.value = '', 3000);
};

const fetchArtists = async (page = 1) => {
  loading.value = true;
  try {
    const res = await api.get('/admin/users', {
      params: { 'filter[role]': 'artist', search: searchQuery.value, status: statusFilter.value, page }
    });
    users.value = res.data.data.items || [];
    meta.value = res.data.data.meta || { current_page: 1, last_page: 1, total: 0 };
  } catch (error) {
    console.error("Failed to fetch artists", error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => fetchArtists());
const handleSearch = () => fetchArtists(1);

const openCreate = () => {
  selectedArtist.value = null;
  showDrawer.value = true;
};

const openEdit = (user: any) => {
  selectedArtist.value = user;
  showDrawer.value = true;
};

const deleteUser = async (user: any) => {
  if (!confirm(`Bạn có chắc chắn muốn xóa nghệ sĩ ${user.name}? Hành động này sẽ chuyển họ vào trạng thái Đã Xóa.`)) return;
  try {
    await api.delete(`/admin/users/${user.id}`);
    showToast('Đã xóa người dùng thành công.');
    fetchArtists(meta.value.current_page);
  } catch (e: any) {
    alert(e.response?.data?.message || 'Có lỗi khi xóa.');
  }
};

const changeStatus = async (user: any, newStatus: string) => {
  if (!confirm(`Xác nhận chuyển trạng thái nghệ sĩ này thành: ${newStatus}?`)) return;
  const originalStatus = user.status;
  user.status = newStatus;
  try {
    await api.put(`/admin/users/${user.id}/status`, { status: newStatus });
    showToast(`Đã chuyển trạng thái thành ${newStatus}`);
  } catch (error) {
    user.status = originalStatus;
    showToast("Thao tác thất bại.");
  }
};

const formatDate = (dateString: string) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('en-GB');
};

const getStatusBadgeClass = (status: string) => {
  switch (status?.toLowerCase()) {
    case 'active': return 'bg-emerald-50 text-emerald-700 border-emerald-200/60';
    case 'suspended': return 'bg-amber-50 text-amber-700 border-amber-200/60';
    case 'banned': return 'bg-rose-50 text-rose-700 border-rose-200/60';
    default: return 'bg-slate-50 text-slate-700 border-slate-200';
  }
};
</script>

<template>
  <div class="h-full flex flex-col relative bg-white">
    <transition name="fade-up">
      <div v-if="toastMessage" class="absolute top-4 right-1/2 translate-x-1/2 bg-slate-900 text-white px-4 py-2.5 rounded-lg shadow-lg flex items-center gap-2 z-50 text-sm font-medium">
        <IconCheck size="16" class="text-emerald-400" />
        {{ toastMessage }}
      </div>
    </transition>

    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-4 shrink-0">
      <div class="flex items-center gap-3 flex-1">
        <div class="relative w-full max-w-[340px] group">
          <IconSearch size="16" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-admin-primary transition-colors" />
          <input 
            v-model="searchQuery" 
            @keyup.enter="handleSearch"
            type="text" 
            class="w-full h-9 pl-9 pr-3 border border-slate-300 rounded-[8px] text-sm focus:outline-none focus:ring-1 focus:ring-admin-primary focus:border-admin-primary transition-colors shadow-sm bg-white" 
            placeholder="Search artists..." 
          />
        </div>
        
        <div class="relative group">
          <IconFilter size="16" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-admin-primary transition-colors pointer-events-none" />
          <select 
            v-model="statusFilter" 
            @change="handleSearch"
            class="h-9 border border-slate-300 rounded-[8px] pl-9 pr-8 text-sm focus:outline-none focus:ring-1 focus:ring-admin-primary focus:border-admin-primary text-slate-700 bg-white transition-colors shadow-sm font-medium appearance-none cursor-pointer"
          >
            <option value="">All Status</option>
            <option value="Active">Active</option>
            <option value="Suspended">Suspended</option>
            <option value="Banned">Banned</option>
          </select>
          <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </div>
        </div>
      </div>

      <BaseAdminButton variant="primary" :icon="IconPlus" @click="openCreate">
        New Artist
      </BaseAdminButton>
    </div>

    <div class="flex-1 flex flex-col min-h-0 overflow-hidden bg-white">
      <div class="overflow-x-auto overflow-y-auto flex-1 custom-scrollbar">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead class="bg-slate-50 border-b border-slate-200 sticky top-0 z-10 shadow-sm">
            <tr class="text-slate-500 font-semibold text-[13px]">
              <th class="px-6 py-3.5 w-[30%]">Artist</th>
              <th class="px-6 py-3.5 w-[20%]">Email</th>
              <th class="px-6 py-3.5 w-[15%]">Songs</th>
              <th class="px-6 py-3.5 w-[15%]">Status</th>
              <th class="px-6 py-3.5 w-[15%]">Joined Date</th>
              <th class="px-6 py-3.5 w-[5%] text-right"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <template v-if="loading">
              <tr v-for="i in 5" :key="i" class="animate-pulse">
                <td class="px-6 py-4"><div class="flex items-center gap-3"><div class="w-10 h-10 bg-slate-200 rounded-full"></div><div class="h-4 bg-slate-200 rounded w-24"></div></div></td>
                <td class="px-6 py-4"><div class="h-4 bg-slate-200 rounded w-32"></div></td>
                <td class="px-6 py-4"><div class="h-4 bg-slate-200 rounded w-10"></div></td>
                <td class="px-6 py-4"><div class="h-5 bg-slate-200 rounded-full w-20"></div></td>
                <td class="px-6 py-4"><div class="h-4 bg-slate-200 rounded w-24"></div></td>
                <td class="px-6 py-4"></td>
              </tr>
            </template>

            <tr v-else-if="users.length === 0">
              <td colspan="6" class="p-0">
                <EmptyState title="No artists found" description="Get started by adding a new artist account to the platform." :icon="IconMusic">
                  <template #action>
                    <BaseAdminButton variant="secondary" :icon="IconPlus" @click="openCreate">Add first artist</BaseAdminButton>
                  </template>
                </EmptyState>
              </td>
            </tr>

            <template v-else>
              <tr v-for="user in users" :key="user.id" class="hover:bg-slate-50 transition-colors group relative">
                <td class="px-6 py-3">
                  <div class="flex items-center gap-3">
                    <img v-if="user.avatar" :src="user.avatar" class="w-10 h-10 rounded-full object-cover border border-slate-200 shadow-sm" />
                    <div v-else class="w-10 h-10 rounded-full bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center font-bold text-sm shadow-sm">
                      {{ user.artist_profile?.stage_name?.charAt(0) || user.name?.charAt(0) }}
                    </div>
                    <div>
                      <div class="font-semibold text-slate-900">{{ user.artist_profile?.stage_name || user.name }}</div>
                      <div class="text-xs text-slate-500 font-medium mt-0.5">{{ user.name }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-3 text-slate-600">{{ user.email }}</td>
                <td class="px-6 py-3"><span class="inline-flex items-center text-slate-700 font-medium">{{ user.songs_count || 0 }}</span></td>
                <td class="px-6 py-3"><span :class="['px-2.5 py-1 text-[11px] font-bold rounded-full border tracking-wide uppercase', getStatusBadgeClass(user.status)]">{{ user.status }}</span></td>
                <td class="px-6 py-3 text-slate-500">{{ formatDate(user.created_at) }}</td>
                <td class="px-6 py-3 text-right">
                  <div class="flex items-center justify-end opacity-0 group-hover:opacity-100 transition-opacity gap-1">
                    <button v-if="user.status === 'Active'" @click="changeStatus(user, 'Suspended')" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-md transition" title="Đình chỉ"><IconLock size="18" /></button>
                    <button v-else @click="changeStatus(user, 'Active')" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-md transition" title="Kích hoạt"><IconLockOpen size="18" /></button>
                    <button v-if="user.status !== 'Banned'" @click="changeStatus(user, 'Banned')" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-md transition" title="Cấm vĩnh viễn"><IconShield size="18" /></button>
                    <button @click="openEdit(user)" class="p-1.5 text-admin-primary hover:bg-blue-50 rounded-md transition ml-1" title="Sửa"><IconEdit size="18" /></button>
                    <button @click="deleteUser(user)" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-md transition" title="Xóa"><IconTrash size="18" /></button>
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

      <div class="bg-white px-6 py-3 flex items-center justify-between border-t border-slate-200 shrink-0">
        <div class="text-[13px] font-medium text-slate-500">Showing <span class="text-slate-900 font-semibold">{{ meta.current_page }}</span> of {{ meta.last_page || 1 }} pages</div>
        <div class="flex items-center gap-2">
          <button @click="fetchArtists(meta.current_page - 1)" :disabled="meta.current_page === 1" class="px-3 py-1.5 rounded-md border border-slate-200 text-slate-600 text-[13px] font-medium hover:bg-slate-50 disabled:opacity-40 shadow-sm bg-white">Previous</button>
          <button @click="fetchArtists(meta.current_page + 1)" :disabled="meta.current_page === meta.last_page || meta.last_page === 0" class="px-3 py-1.5 rounded-md border border-slate-200 text-slate-600 text-[13px] font-medium hover:bg-slate-50 disabled:opacity-40 shadow-sm bg-white">Next</button>
        </div>
      </div>
    </div>

    <ArtistDrawer 
      v-model="showDrawer" 
      :artist-data="selectedArtist"
      @saved="fetchArtists(meta.current_page); showToast(selectedArtist ? 'Cập nhật thành công!' : 'Khởi tạo thành công!')"
    />
  </div>
</template>

<style scoped>
.fade-up-enter-active, .fade-up-leave-active { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.fade-up-enter-from, .fade-up-leave-to { opacity: 0; transform: translate(50%, -10px); }
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 6px; }
.custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #94a3b8; }
</style>
