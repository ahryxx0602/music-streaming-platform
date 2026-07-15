<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import api from '@/services/api';
import { IconSearch, IconFilter, IconDotsVertical, IconUserCheck, IconUserX, IconUserCancel } from '@tabler/icons-vue';

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  status: string;
  avatar_url?: string;
  created_at: string;
}

const users = ref<User[]>([]);
const loading = ref(false);

const meta = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
  per_page: 15
});

const filters = ref({
  search: '',
  role: '',
  status: ''
});

const fetchUsers = async (page = 1) => {
  loading.value = true;
  try {
    const params = new URLSearchParams();
    params.append('page', page.toString());
    
    if (filters.value.search) params.append('filter[search]', filters.value.search);
    if (filters.value.role) params.append('filter[role]', filters.value.role);
    if (filters.value.status) params.append('filter[status]', filters.value.status);
    
    const res = await api.get(`/admin/users?${params.toString()}`);
    users.value = res.data.data.items;
    meta.value = res.data.data.meta;
  } catch (error) {
    console.error("Error fetching users:", error);
  } finally {
    loading.value = false;
  }
};

const handleSearch = () => {
  fetchUsers(1);
};

onMounted(() => {
  fetchUsers(1);
});

const getRoleBadgeClass = (role: string) => {
  switch(role.toLowerCase()) {
    case 'admin': return 'bg-purple-100 text-purple-700 border-purple-200';
    case 'artist': return 'bg-blue-100 text-blue-700 border-blue-200';
    case 'listener': return 'bg-slate-100 text-slate-700 border-slate-200';
    default: return 'bg-gray-100 text-gray-700';
  }
};

const getStatusBadgeClass = (status: string) => {
  switch(status.toLowerCase()) {
    case 'active': return 'bg-emerald-100 text-emerald-700 border-emerald-200';
    case 'suspended': return 'bg-amber-100 text-amber-700 border-amber-200';
    case 'banned': return 'bg-rose-100 text-rose-700 border-rose-200';
    default: return 'bg-gray-100 text-gray-700';
  }
};

const formatDate = (dateStr: string) => {
  return new Date(dateStr).toLocaleDateString('vi-VN');
};
</script>

<template>
  <div class="users-management">
    
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Quản lý Tài khoản</h1>
        <p class="text-slate-500 mt-1">Quản trị và kiểm duyệt tất cả người dùng trong hệ thống (Tổng: {{ meta.total }})</p>
      </div>
      <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-0.5">
        + Tạo nhanh Artist
      </button>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 mb-6 flex flex-wrap gap-4 items-center justify-between">
      <div class="relative w-full md:w-96">
        <IconSearch size="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
        <input 
          v-model="filters.search" 
          @keyup.enter="handleSearch"
          type="text" 
          placeholder="Tìm theo tên hoặc email..." 
          class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition"
        />
      </div>
      
      <div class="flex items-center gap-3 w-full md:w-auto">
        <div class="flex items-center gap-2 text-sm font-medium text-slate-600">
          <IconFilter size="18" /> Lọc theo:
        </div>
        <select v-model="filters.role" @change="handleSearch" class="bg-slate-50 border border-slate-200 text-sm rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/50 cursor-pointer">
          <option value="">Tất cả Role</option>
          <option value="listener">Listener</option>
          <option value="artist">Artist</option>
          <option value="admin">Admin</option>
        </select>
        
        <select v-model="filters.status" @change="handleSearch" class="bg-slate-50 border border-slate-200 text-sm rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/50 cursor-pointer">
          <option value="">Tất cả Status</option>
          <option value="Active">Active</option>
          <option value="Suspended">Suspended</option>
          <option value="Banned">Banned</option>
        </select>
      </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
            <tr>
              <th class="px-6 py-4">Tài khoản</th>
              <th class="px-6 py-4">Email</th>
              <th class="px-6 py-4">Role</th>
              <th class="px-6 py-4">Trạng thái</th>
              <th class="px-6 py-4">Ngày tham gia</th>
              <th class="px-6 py-4 text-center">Hành động</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="loading" class="animate-pulse">
              <td colspan="6" class="px-6 py-10 text-center text-slate-400">Đang tải dữ liệu...</td>
            </tr>
            <tr v-else-if="users.length === 0">
              <td colspan="6" class="px-6 py-10 text-center text-slate-400">Không tìm thấy người dùng nào.</td>
            </tr>
            <tr v-for="user in users" :key="user.id" class="hover:bg-slate-50/80 transition">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <img v-if="user.avatar_url" :src="user.avatar_url" class="w-10 h-10 rounded-full object-cover border border-slate-200" />
                  <div v-else class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 font-bold flex items-center justify-center border border-slate-200">
                    {{ user.name.charAt(0) }}
                  </div>
                  <div>
                    <p class="font-bold text-slate-900">{{ user.name }}</p>
                    <p class="text-xs text-slate-500">ID: #{{ user.id }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 text-slate-600">{{ user.email }}</td>
              <td class="px-6 py-4">
                <span :class="['px-2.5 py-1 text-xs font-bold rounded-md border capitalize tracking-wide', getRoleBadgeClass(user.role)]">
                  {{ user.role }}
                </span>
              </td>
              <td class="px-6 py-4">
                <span :class="['px-2.5 py-1 text-xs font-bold rounded-md border flex w-max items-center gap-1.5 tracking-wide', getStatusBadgeClass(user.status)]">
                  <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                  {{ user.status }}
                </span>
              </td>
              <td class="px-6 py-4 text-slate-500">{{ formatDate(user.created_at) }}</td>
              <td class="px-6 py-4 text-center">
                <button class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                  <IconDotsVertical size="18" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
        <p class="text-sm text-slate-500">
          Hiển thị trang <span class="font-bold text-slate-700">{{ meta.current_page }}</span> / {{ meta.last_page }}
        </p>
        <div class="flex gap-2">
          <button 
            @click="fetchUsers(meta.current_page - 1)" 
            :disabled="meta.current_page === 1"
            class="px-4 py-2 bg-white border border-slate-200 text-sm font-medium rounded-lg hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition"
          >
            Trang trước
          </button>
          <button 
            @click="fetchUsers(meta.current_page + 1)" 
            :disabled="meta.current_page === meta.last_page"
            class="px-4 py-2 bg-white border border-slate-200 text-sm font-medium rounded-lg hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition"
          >
            Trang sau
          </button>
        </div>
      </div>
    </div>

  </div>
</template>
