<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useAuditLogStore } from '@/stores/auditLogStore';
import { 
  IconUsers, IconMusic, IconMicrophone2, IconCoin, 
  IconTrendingUp, IconHeadphones, IconArrowUpRight, IconArrowDownRight, IconEye
} from '@tabler/icons-vue';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  BarElement,
  PointElement,
  ArcElement,
  CategoryScale,
  LinearScale,
  Filler
} from 'chart.js';
import { Line, Doughnut, Bar } from 'vue-chartjs';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, BarElement, ArcElement, Title, Tooltip, Legend, Filler);

const auditStore = useAuditLogStore();

// Chart Data (Mock)
const revenueData = ref({
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
  datasets: [
    {
      label: 'Doanh thu ($)',
      data: [1200, 1900, 2500, 2200, 3100, 3800, 4200, 4800, 5100, 5900, 6800, 7500],
      borderColor: '#6366F1', // theme-primary
      backgroundColor: 'rgba(99, 102, 241, 0.1)',
      borderWidth: 2,
      fill: true,
      tension: 0.4
    }
  ]
});

const revenueOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: {
      mode: 'index' as const,
      intersect: false,
      backgroundColor: '#1E293B',
      titleColor: '#F8FAFC',
      bodyColor: '#94A3B8',
      borderColor: '#334155',
      borderWidth: 1,
      padding: 12
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: { color: 'rgba(148, 163, 184, 0.1)' },
      ticks: { color: '#64748B' }
    },
    x: {
      grid: { display: false },
      ticks: { color: '#64748B' }
    }
  }
};

const genreData = ref({
  labels: ['Pop', 'Hip-Hop', 'EDM', 'R&B', 'Rock', 'Others'],
  datasets: [
    {
      data: [35, 25, 15, 10, 8, 7],
      backgroundColor: [
        '#6366F1', // primary
        '#06B6D4', // accent
        '#10B981', // success
        '#F59E0B', // warning
        '#F87171', // danger
        '#94A3B8'  // text-sec
      ],
      borderWidth: 0,
      hoverOffset: 4
    }
  ]
});

const genreOptions = {
  responsive: true,
  maintainAspectRatio: false,
  cutout: '75%',
  plugins: {
    legend: {
      position: 'right' as const,
      labels: {
        color: '#64748B',
        usePointStyle: true,
        padding: 20,
        font: { family: 'Inter', size: 12 }
      }
    }
  }
};

const topArtists = ref([
  { id: 1, name: 'The Weeknd', streams: '2.5M', trend: '+15%', avatar: 'https://i.pravatar.cc/150?u=1' },
  { id: 2, name: 'Taylor Swift', streams: '2.1M', trend: '+8%', avatar: 'https://i.pravatar.cc/150?u=2' },
  { id: 3, name: 'Drake', streams: '1.8M', trend: '-3%', avatar: 'https://i.pravatar.cc/150?u=3' },
  { id: 4, name: 'Dua Lipa', streams: '1.5M', trend: '+12%', avatar: 'https://i.pravatar.cc/150?u=4' },
]);

onMounted(() => {
  auditStore.fetchRecentLogs();
});
</script>

<template>
  <div class="h-full flex flex-col p-6 max-w-[1600px] mx-auto w-full">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-theme-text mb-1 flex items-center gap-2">
          <IconTrendingUp class="text-theme-primary" size="28" />
          Dashboard Analytics
        </h1>
        <p class="text-sm text-theme-text-sec">
          Tổng quan số liệu thống kê hệ thống âm nhạc Aurora
        </p>
      </div>
      
      <div class="flex gap-2">
        <select class="px-4 py-2 bg-theme-surface border border-theme-border rounded-lg text-sm text-theme-text font-medium outline-none focus:border-theme-primary transition-colors">
          <option value="7">7 ngày qua</option>
          <option value="30" selected>30 ngày qua</option>
          <option value="90">3 tháng qua</option>
          <option value="365">1 năm qua</option>
        </select>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
      <!-- Total Users -->
      <div class="bg-theme-surface p-6 rounded-2xl shadow-[var(--shadow-glow)] border border-theme-border relative overflow-hidden group">
        <div class="flex justify-between items-start mb-4 relative z-10">
          <div>
            <p class="text-sm font-medium text-theme-text-sec mb-1">Tổng Người Dùng</p>
            <h3 class="text-3xl font-black text-theme-text">12,450</h3>
          </div>
          <div class="w-12 h-12 rounded-xl bg-theme-info/10 flex items-center justify-center text-theme-info transition-transform group-hover:scale-110">
            <IconUsers size="24" />
          </div>
        </div>
        <div class="flex items-center gap-2 text-sm relative z-10">
          <span class="flex items-center text-theme-success font-semibold bg-theme-success/10 px-2 py-0.5 rounded-md">
            <IconArrowUpRight size="16" /> 12%
          </span>
          <span class="text-theme-text-sec">so với tháng trước</span>
        </div>
        <div class="absolute -bottom-6 -right-6 text-theme-info/5 rotate-[-15deg] pointer-events-none transition-transform group-hover:scale-110">
          <IconUsers size="120" />
        </div>
      </div>

      <!-- Total Streams -->
      <div class="bg-theme-surface p-6 rounded-2xl shadow-[var(--shadow-glow)] border border-theme-border relative overflow-hidden group">
        <div class="flex justify-between items-start mb-4 relative z-10">
          <div>
            <p class="text-sm font-medium text-theme-text-sec mb-1">Lượt Nghe (Streams)</p>
            <h3 class="text-3xl font-black text-theme-text">8.2M</h3>
          </div>
          <div class="w-12 h-12 rounded-xl bg-theme-accent/10 flex items-center justify-center text-theme-accent transition-transform group-hover:scale-110">
            <IconHeadphones size="24" />
          </div>
        </div>
        <div class="flex items-center gap-2 text-sm relative z-10">
          <span class="flex items-center text-theme-success font-semibold bg-theme-success/10 px-2 py-0.5 rounded-md">
            <IconArrowUpRight size="16" /> 24%
          </span>
          <span class="text-theme-text-sec">so với tháng trước</span>
        </div>
        <div class="absolute -bottom-6 -right-6 text-theme-accent/5 rotate-[-15deg] pointer-events-none transition-transform group-hover:scale-110">
          <IconHeadphones size="120" />
        </div>
      </div>

      <!-- Revenue -->
      <div class="bg-theme-surface p-6 rounded-2xl shadow-[var(--shadow-glow)] border border-theme-border relative overflow-hidden group">
        <div class="flex justify-between items-start mb-4 relative z-10">
          <div>
            <p class="text-sm font-medium text-theme-text-sec mb-1">Doanh Thu</p>
            <h3 class="text-3xl font-black text-theme-text">$45,200</h3>
          </div>
          <div class="w-12 h-12 rounded-xl bg-theme-success/10 flex items-center justify-center text-theme-success transition-transform group-hover:scale-110">
            <IconCoin size="24" />
          </div>
        </div>
        <div class="flex items-center gap-2 text-sm relative z-10">
          <span class="flex items-center text-theme-success font-semibold bg-theme-success/10 px-2 py-0.5 rounded-md">
            <IconArrowUpRight size="16" /> 18%
          </span>
          <span class="text-theme-text-sec">so với tháng trước</span>
        </div>
        <div class="absolute -bottom-6 -right-6 text-theme-success/5 rotate-[-15deg] pointer-events-none transition-transform group-hover:scale-110">
          <IconCoin size="120" />
        </div>
      </div>

      <!-- Total Artists -->
      <div class="bg-theme-surface p-6 rounded-2xl shadow-[var(--shadow-glow)] border border-theme-border relative overflow-hidden group">
        <div class="flex justify-between items-start mb-4 relative z-10">
          <div>
            <p class="text-sm font-medium text-theme-text-sec mb-1">Nghệ Sĩ Active</p>
            <h3 class="text-3xl font-black text-theme-text">1,204</h3>
          </div>
          <div class="w-12 h-12 rounded-xl bg-theme-primary/10 flex items-center justify-center text-theme-primary transition-transform group-hover:scale-110">
            <IconMicrophone2 size="24" />
          </div>
        </div>
        <div class="flex items-center gap-2 text-sm relative z-10">
          <span class="flex items-center text-theme-danger font-semibold bg-theme-danger/10 px-2 py-0.5 rounded-md">
            <IconArrowDownRight size="16" /> 2%
          </span>
          <span class="text-theme-text-sec">so với tháng trước</span>
        </div>
        <div class="absolute -bottom-6 -right-6 text-theme-primary/5 rotate-[-15deg] pointer-events-none transition-transform group-hover:scale-110">
          <IconMicrophone2 size="120" />
        </div>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
      
      <!-- Main Chart (Revenue/Streams) -->
      <div class="xl:col-span-2 bg-theme-surface p-6 rounded-2xl shadow-[var(--shadow-glow)] border border-theme-border flex flex-col">
        <div class="flex items-center justify-between mb-6">
          <h3 class="font-bold text-theme-text">Tăng trưởng Doanh thu</h3>
          <button class="text-sm text-theme-primary hover:underline">Xem chi tiết</button>
        </div>
        <div class="flex-1 min-h-[300px]">
          <Line v-if="revenueData" :data="revenueData" :options="revenueOptions" />
        </div>
      </div>

      <!-- Secondary Chart (Genres) -->
      <div class="bg-theme-surface p-6 rounded-2xl shadow-[var(--shadow-glow)] border border-theme-border flex flex-col">
        <div class="flex items-center justify-between mb-6">
          <h3 class="font-bold text-theme-text">Thể loại thịnh hành</h3>
        </div>
        <div class="flex-1 min-h-[300px] flex items-center justify-center relative">
          <Doughnut v-if="genreData" :data="genreData" :options="genreOptions" />
          <!-- Center Text -->
          <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none" style="left: -20%;">
            <span class="text-2xl font-black text-theme-text">Pop</span>
            <span class="text-xs text-theme-text-sec uppercase tracking-wider">Top 1</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Third Row: Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-6">
      <!-- Top Artists -->
      <div class="bg-theme-surface rounded-2xl shadow-[var(--shadow-glow)] border border-theme-border overflow-hidden flex flex-col">
        <div class="p-6 border-b border-theme-border flex items-center justify-between">
          <h3 class="font-bold text-theme-text">Top Nghệ Sĩ (Streams)</h3>
          <button class="text-sm text-theme-primary hover:underline">Tất cả</button>
        </div>
        <div class="p-0">
          <table class="w-full text-left">
            <thead class="bg-theme-surface-hover/50">
              <tr>
                <th class="px-6 py-3 text-xs font-semibold text-theme-text-sec uppercase">Nghệ Sĩ</th>
                <th class="px-6 py-3 text-xs font-semibold text-theme-text-sec uppercase">Lượt Nghe</th>
                <th class="px-6 py-3 text-xs font-semibold text-theme-text-sec uppercase text-right">Tăng trưởng</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-theme-border">
              <tr v-for="artist in topArtists" :key="artist.id" class="hover:bg-theme-surface-hover transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <img :src="artist.avatar" class="w-10 h-10 rounded-full object-cover border border-theme-border" />
                    <span class="font-bold text-theme-text">{{ artist.name }}</span>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span class="font-medium text-theme-text">{{ artist.streams }}</span>
                </td>
                <td class="px-6 py-4 text-right">
                  <span 
                    class="text-sm font-semibold flex items-center justify-end gap-1"
                    :class="artist.trend.startsWith('+') ? 'text-theme-success' : 'text-theme-danger'"
                  >
                    <IconArrowUpRight v-if="artist.trend.startsWith('+')" size="16" />
                    <IconArrowDownRight v-else size="16" />
                    {{ artist.trend }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Recent System Activity -->
      <div class="bg-theme-surface rounded-2xl shadow-[var(--shadow-glow)] border border-theme-border overflow-hidden flex flex-col">
        <div class="p-6 border-b border-theme-border flex items-center justify-between">
          <h3 class="font-bold text-theme-text">Hoạt động gần đây</h3>
          <router-link to="/admin/audit-logs" class="text-sm text-theme-primary hover:underline">Tất cả nhật ký</router-link>
        </div>
        <div class="p-0 flex-1 overflow-auto">
          <div v-if="auditStore.loading" class="p-6 flex justify-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-theme-primary"></div>
          </div>
          <div v-else-if="auditStore.recentLogs.length === 0" class="p-6 text-center text-theme-text-sec">
            Không có hoạt động nào gần đây
          </div>
          <div v-else class="divide-y divide-theme-border">
            <div v-for="log in auditStore.recentLogs" :key="log.id" class="p-4 hover:bg-theme-surface-hover transition-colors flex items-start gap-4">
              <div class="w-10 h-10 rounded-full bg-theme-bg flex items-center justify-center text-theme-text-sec shrink-0">
                {{ log.user?.name?.charAt(0) || 'S' }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm text-theme-text">
                  <span class="font-bold">{{ log.user?.name || 'Hệ thống' }}</span>
                  đã <span class="font-medium text-theme-primary">{{ log.action }}</span> 
                  một {{ log.entity_type?.split('\\').pop() }}
                </p>
                <p class="text-xs text-theme-text-sec mt-1">{{ new Date(log.created_at).toLocaleString('vi-VN') }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
