<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import api from '@/services/api';
import { 
 IconSearch, 
 IconLock,
 IconLockOpen,
 IconShield,
 IconCheck,
 IconDotsVertical,
 IconHeadphones,
 IconFilter,
 IconTrash,
 IconEdit,
 IconPlus
} from '@tabler/icons-vue';
import EmptyState from '@/components/admin/ui/feedback/EmptyState.vue';
import BaseAdminButton from '@/components/admin/ui/button/BaseAdminButton.vue';
import ListenerDrawer from '@/components/admin/features/users/drawers/ListenerDrawer.vue';

const { t } = useI18n();
const users = ref<any[]>([]);
const loading = ref(true);
const meta = ref({ current_page: 1, last_page: 1, total: 0 });

const searchQuery = ref('');
const statusFilter = ref('');
const subscriptionFilter = ref('');

const showDrawer = ref(false);
const selectedListener = ref<any>(null);

const toastMessage = ref('');
const showToast = (msg: string) => {
 toastMessage.value = msg;
 setTimeout(() => toastMessage.value = '', 3000);
};

const fetchListeners = async (page = 1) => {
 loading.value = true;
 try {
 // Gọi API thật. Tạm filter[role]=listener
 const res = await api.get('/admin/users', {
 params: { 
 'filter[role]': 'listener', 
 search: searchQuery.value, 
 status: statusFilter.value,
 subscription: subscriptionFilter.value,
 page 
 }
 });
 users.value = res.data.data.items || [];
 meta.value = res.data.data.meta || { current_page: 1, last_page: 1, total: 0 };
 } catch (error) {
 console.error("Failed to fetch listeners", error);
 } finally {
 loading.value = false;
 }
};

onMounted(() => fetchListeners());
const handleSearch = () => fetchListeners(1);

const changeStatus = async (user: any, newStatus: string) => {
 if (!confirm(`${t('admin.users_page.confirm_status')} ${newStatus}?`)) return;
 const originalStatus = user.status;
 user.status = newStatus;
 try {
 await api.put(`/admin/users/${user.id}/status`, { status: newStatus });
 showToast(`${t('admin.users_page.status_changed')} ${newStatus}`);
 } catch (error) {
 user.status = originalStatus;
 showToast(t('admin.users_page.action_failed'));
 }
};

const deleteUser = async (user: any) => {
 if (!confirm(t('admin.users_page.confirm_delete'))) return;
 try {
 await api.delete(`/admin/users/${user.id}`);
 showToast(t('admin.users_page.delete_success'));
 fetchListeners(meta.value.current_page);
 } catch (e: any) {
 alert(e.response?.data?.message || t('admin.users_page.action_failed'));
 }
};

const openEdit = (user: any) => {
 selectedListener.value = user;
 showDrawer.value = true;
};

const openCreate = () => {
 selectedListener.value = null;
 showDrawer.value = true;
};

const formatDate = (dateString: string) => {
 if (!dateString) return '';
 return new Date(dateString).toLocaleDateString('en-GB'); 
};

const getStatusBadgeClass = (status: string) => {
 switch (status?.toLowerCase()) {
 case 'active': return 'bg-theme-success/10 dark:bg-theme-success/15 text-theme-success border-theme-success/20/60 ';
 case 'suspended': return 'bg-theme-warning/10 text-theme-warning border-theme-warning/20/60 ';
 case 'banned': return 'bg-theme-danger/10 text-theme-danger border-theme-danger/20/60 ';
 default: return 'bg-theme-bg text-theme-text border-theme-border';
 }
};

const formatStatus = (status: string) => {
 if (!status) return status;
 return t(`admin.status.${status.toLowerCase()}`);
};
</script>

<template>
 <div class="h-full flex flex-col relative bg-theme-surface">
 <!-- Mini Toast -->
 <transition name="fade-up">
 <div v-if="toastMessage" class="absolute top-4 right-1/2 translate-x-1/2 bg-slate-900 text-white px-4 py-2.5 rounded-lg shadow-lg flex items-center gap-2 z-50 text-sm font-medium">
 <IconCheck size="16" class="text-emerald-400" />
 {{ toastMessage }}
 </div>
 </transition>

 <!-- Toolbar (Filter & Actions) -->
 <div class="px-6 py-4 border-b border-theme-border flex items-center justify-between gap-4 shrink-0">
 <div class="flex items-center gap-3 flex-1">
 
 <!-- Search Input -->
 <div class="relative w-full max-w-[340px] group">
 <IconSearch size="16" class="absolute left-3 top-1/2 -translate-y-1/2 text-theme-text-sec group-focus-within:text-admin-primary transition-colors" />
 <input 
 v-model="searchQuery" 
 @keyup.enter="handleSearch"
 type="text" 
 class="w-full h-9 pl-9 pr-3 border border-theme-border rounded-[8px] text-sm focus:outline-none focus:ring-1 focus:ring-admin-primary focus:border-admin-primary transition-colors shadow-sm bg-theme-surface" 
 :placeholder="$t('admin.users_page.search_listeners')" 
 />
 </div>
 
 <!-- Filter Status -->
 <div class="relative group">
 <IconFilter size="16" class="absolute left-3 top-1/2 -translate-y-1/2 text-theme-text-sec group-focus-within:text-admin-primary transition-colors pointer-events-none" />
 <select 
 v-model="statusFilter" 
 @change="handleSearch"
 class="h-9 border border-theme-border rounded-[8px] pl-9 pr-8 text-sm focus:outline-none focus:ring-1 focus:ring-admin-primary focus:border-admin-primary text-theme-text bg-theme-surface transition-colors shadow-sm font-medium appearance-none cursor-pointer"
 >
 <option value="">{{ $t('admin.users_page.all_status') }}</option>
 <option value="Active">{{ $t('admin.status.active') }}</option>
 <option value="Suspended">{{ $t('admin.status.suspended') }}</option>
 <option value="Banned">{{ $t('admin.status.banned') }}</option>
 </select>
 <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
 <svg class="h-4 w-4 text-theme-text-sec" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
 </div>
 </div>

 </div>

 <BaseAdminButton variant="primary" :icon="IconPlus" @click="openCreate">
 {{ $t('admin.users_page.new_listener') }}
 </BaseAdminButton>
 </div>

 <!-- Data Table Area -->
 <div class="flex-1 flex flex-col min-h-0 overflow-hidden bg-theme-surface">
 <div class="overflow-x-auto overflow-y-auto flex-1 custom-scrollbar">
 <table class="w-full text-left text-sm whitespace-nowrap">
 <thead class="bg-theme-bg border-b border-theme-border sticky top-0 z-10 shadow-sm">
 <tr class="text-theme-text-sec font-semibold text-[13px]">
 <th class="px-6 py-3.5 w-[30%]">{{ $t('admin.users_page.col_user') }}</th>
 <th class="px-6 py-3.5 w-[25%]">{{ $t('admin.users_page.col_email') }}</th>
 <th class="px-6 py-3.5 w-[15%]">{{ $t('admin.users_page.col_plan') }}</th>
 <th class="px-6 py-3.5 w-[15%]">{{ $t('admin.users_page.col_status') }}</th>
 <th class="px-6 py-3.5 w-[10%]">{{ $t('admin.users_page.col_joined') }}</th>
 <th class="px-6 py-3.5 w-[5%] text-right"></th>
 </tr>
 </thead>
 <tbody class="divide-y divide-theme-border">
 <!-- Loading Skeleton -->
 <template v-if="loading">
 <tr v-for="i in 5" :key="i" class="animate-pulse">
 <td class="px-6 py-4"><div class="flex items-center gap-3"><div class="w-10 h-10 bg-slate-200 rounded-full"></div><div class="h-4 bg-slate-200 rounded w-24"></div></div></td>
 <td class="px-6 py-4"><div class="h-4 bg-slate-200 rounded w-32"></div></td>
 <td class="px-6 py-4"><div class="h-4 bg-slate-200 rounded w-16"></div></td>
 <td class="px-6 py-4"><div class="h-5 bg-slate-200 rounded-full w-20"></div></td>
 <td class="px-6 py-4"><div class="h-4 bg-slate-200 rounded w-24"></div></td>
 <td class="px-6 py-4"></td>
 </tr>
 </template>

 <!-- Empty State -->
 <tr v-else-if="users.length === 0">
 <td colspan="6" class="p-0">
 <EmptyState 
 :title="$t('admin.users_page.no_listeners_found')" 
 :description="$t('admin.users_page.no_listeners_desc')" 
 :icon="IconHeadphones"
 />
 </td>
 </tr>

 <!-- Data Rows -->
 <template v-else>
 <tr v-for="user in users" :key="user.id" class="hover:bg-theme-bg transition-colors group">
 <td class="px-6 py-3">
 <div class="flex items-center gap-3">
 <img v-if="user.avatar" :src="user.avatar" class="w-10 h-10 rounded-full object-cover border border-theme-border shadow-sm" />
 <div v-else class="w-10 h-10 rounded-full bg-theme-info/10 text-theme-info border border-blue-100 flex items-center justify-center font-bold text-sm shadow-sm">
 {{ user.name?.charAt(0) || 'U' }}
 </div>
 <div>
 <div class="font-semibold text-theme-text">{{ user.name }}</div>
 <div class="text-xs text-theme-text-sec font-medium mt-0.5">@{{ user.username || user.name.toLowerCase().replace(/\s/g, '') }}</div>
 </div>
 </div>
 </td>
 <td class="px-6 py-3 text-theme-text-sec font-medium">{{ user.email }}</td>
 <td class="px-6 py-3">
 <span v-if="user.is_premium" class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-theme-warning/15 text-theme-warning border border-theme-warning/20 ">
 Premium
 </span>
 <span v-else class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-theme-bg text-theme-text-sec border border-theme-border">
 Free
 </span>
 </td>
 <td class="px-6 py-3">
 <span :class="['px-2.5 py-1 text-[11px] font-bold rounded-full border tracking-wide uppercase', getStatusBadgeClass(user.status)]">
 {{ formatStatus(user.status) }}
 </span>
 </td>
 <td class="px-6 py-3 text-theme-text-sec">{{ formatDate(user.created_at) }}</td>
 <td class="px-6 py-3 text-right">
 <div class="flex items-center justify-end opacity-0 group-hover:opacity-100 transition-opacity gap-1">
 <button v-if="user.status === 'Active'" @click="changeStatus(user, 'Suspended')" class="p-1.5 text-theme-warning hover:bg-theme-warning/10 dark:hover:bg-amber-500/15 dark:hover:bg-amber-500/15 rounded-md transition" :title="$t('admin.users_page.action_suspend')">
 <IconLock size="18" />
 </button>
 <button v-else @click="changeStatus(user, 'Active')" class="p-1.5 text-theme-success hover:bg-theme-success/10 dark:hover:bg-theme-success/15 dark:hover:bg-theme-success/15 rounded-md transition" :title="$t('admin.users_page.action_activate')">
 <IconLockOpen size="18" />
 </button>
 <button v-if="user.status !== 'Banned'" @click="changeStatus(user, 'Banned')" class="p-1.5 text-theme-danger hover:bg-theme-danger/10 dark:hover:bg-rose-500/15 dark:hover:bg-rose-500/15 rounded-md transition" :title="$t('admin.users_page.action_ban')">
 <IconShield size="18" />
 </button>
 <!-- Edit -->
 <button @click="openEdit(user)" class="p-1.5 text-admin-primary hover:bg-theme-info/10 dark:hover:bg-blue-500/15 dark:hover:bg-blue-500/15 rounded-md transition ml-1" :title="$t('admin.users_page.action_edit')">
 <IconEdit size="18" />
 </button>
 <!-- Context menu -->
 <button @click="deleteUser(user)" class="p-1.5 text-theme-danger hover:bg-theme-danger/10 dark:hover:bg-rose-500/15 dark:hover:bg-rose-500/15 rounded-md transition" :title="$t('admin.users_page.action_delete')">
 <IconTrash size="18" />
 </button>
 </div>
 </td>
 </tr>
 </template>
 </tbody>
 </table>
 </div>

 <!-- Pagination Footer -->
 <div class="bg-theme-surface px-6 py-3 flex items-center justify-between border-t border-theme-border shrink-0">
 <div class="text-[13px] font-medium text-theme-text-sec">
 {{ $t('admin.users_page.showing') }} <span class="text-theme-text font-semibold">{{ meta.current_page }}</span> {{ $t('admin.users_page.of') }} {{ meta.last_page || 1 }} {{ $t('admin.users_page.pages') }}
 </div>
 <div class="flex items-center gap-2">
 <button 
 @click="fetchListeners(meta.current_page - 1)" 
 :disabled="meta.current_page === 1"
 class="px-3 py-1.5 rounded-md border border-theme-border text-theme-text-sec text-[13px] font-medium hover:bg-theme-bg hover:border-theme-border disabled:opacity-40 disabled:bg-transparent disabled:cursor-not-allowed transition shadow-sm bg-theme-surface"
 >
 {{ $t('admin.users_page.prev') }}
 </button>
 <button 
 @click="fetchListeners(meta.current_page + 1)" 
 :disabled="meta.current_page === meta.last_page || meta.last_page === 0"
 class="px-3 py-1.5 rounded-md border border-theme-border text-theme-text-sec text-[13px] font-medium hover:bg-theme-bg hover:border-theme-border disabled:opacity-40 disabled:bg-transparent disabled:cursor-not-allowed transition shadow-sm bg-theme-surface"
 >
 {{ $t('admin.users_page.next') }}
 </button>
 </div>
 </div>
 </div>

 <!-- Right Drawer -->
 <ListenerDrawer 
 v-model="showDrawer" 
 :listener-data="selectedListener"
 @saved="fetchListeners(meta.current_page); showToast(selectedListener ? $t('admin.users_page.update_success') : $t('admin.users_page.create_success'))"
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
