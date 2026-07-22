<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { 
  IconHeadphones, 
  IconUsers, 
  IconCoin, 
  IconDisc,
  IconPlayerPlay,
  IconTrendingUp
} from '@tabler/icons-vue';
import { useAuthStore } from '@/stores/authStore';
import { artistDashboardService, type TopTrack } from '@/services/artistDashboardService';
import { Line } from 'vue-chartjs';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js';

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
);

const authStore = useAuthStore();

const stats = ref([
  { id: 'streams', title: 'Tổng lượt nghe', value: '...', trend: '+0%', isUp: true, icon: IconHeadphones, color: 'text-theme-accent', bg: 'bg-theme-accent/10' },
  { id: 'followers', title: 'Người theo dõi', value: '...', trend: '+0%', isUp: true, icon: IconUsers, color: 'text-theme-info', bg: 'bg-theme-info/10' },
  { id: 'revenue', title: 'Doanh thu', value: '...', trend: '+0%', isUp: true, icon: IconCoin, color: 'text-theme-warning', bg: 'bg-theme-warning/10' },
  { id: 'tracks', title: 'Bài hát', value: '...', trend: '0%', isUp: true, icon: IconDisc, color: 'text-theme-primary', bg: 'bg-theme-primary/10' },
]);

const topTracks = ref<TopTrack[]>([]);

// Chart Data
const chartData = ref({
  labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30'],
  datasets: [
    {
      label: 'Lượt nghe',
      backgroundColor: 'rgba(34, 197, 94, 0.1)',
      borderColor: '#22C55E',
      borderWidth: 2,
      pointBackgroundColor: '#22C55E',
      pointBorderColor: '#fff',
      pointHoverBackgroundColor: '#fff',
      pointHoverBorderColor: '#22C55E',
      pointRadius: 0,
      pointHoverRadius: 6,
      fill: true,
      tension: 0.4,
      data: []
    }
  ]
});

const loadData = async () => {
  try {
    const [statsData, tracksData, chart] = await Promise.all([
      artistDashboardService.getStats(),
      artistDashboardService.getTopTracks(),
      artistDashboardService.getStreamsChart()
    ]);
    
    stats.value[0].value = statsData.total_streams.toLocaleString();
    stats.value[1].value = statsData.total_followers.toLocaleString();
    stats.value[2].value = '$' + statsData.balance_usd.toLocaleString();
    stats.value[3].value = statsData.total_tracks.toString();
    
    topTracks.value = tracksData;
    
    chartData.value.datasets[0].data = chart;
  } catch (error) {
    console.error('Failed to load dashboard data', error);
  }
};

onMounted(() => {
  loadData();
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  interaction: {
    mode: 'index',
    intersect: false,
  },
  plugins: {
    legend: {
      display: false
    },
    tooltip: {
      backgroundColor: 'rgba(15, 15, 35, 0.9)',
      titleColor: '#F8FAFC',
      bodyColor: '#22C55E',
      borderColor: '#312E81',
      borderWidth: 1,
      padding: 12,
      displayColors: false,
      callbacks: {
        label: function(context: any) {
          return context.parsed.y.toLocaleString() + ' streams';
        }
      }
    }
  },
  scales: {
    x: {
      display: false,
      grid: {
        display: false
      }
    },
    y: {
      display: true,
      position: 'right',
      grid: {
        color: 'rgba(49, 46, 129, 0.3)',
        drawBorder: false,
      },
      ticks: {
        color: '#94A3B8',
        maxTicksLimit: 5,
        callback: function(value: any) {
          return value >= 1000 ? (value / 1000) + 'k' : value;
        }
      }
    }
  }
};
</script>

<template>
  <div class="p-8 max-w-7xl mx-auto w-full space-y-8 pb-24">
    
    <!-- Welcome Header -->
    <div>
      <h1 class="text-3xl font-heading font-bold text-white mb-2 tracking-wide">
        Welcome back, <span class="text-transparent bg-clip-text bg-gradient-to-r from-theme-primary to-theme-accent">{{ authStore.user?.name || 'Artist' }}</span>!
      </h1>
      <p class="text-theme-text-sec text-sm">Here's what's happening with your music today.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div 
        v-for="stat in stats" 
        :key="stat.title"
        class="bg-theme-surface/40 backdrop-blur-md border border-theme-border/50 rounded-2xl p-6 hover:shadow-[var(--shadow-glow-purple)] transition-all duration-300 group"
      >
        <div class="flex justify-between items-start mb-4">
          <div :class="['p-3 rounded-xl', stat.bg, stat.color]">
            <component :is="stat.icon" size="24" stroke-width="2" />
          </div>
          <span 
            class="flex items-center gap-1 text-xs font-bold px-2 py-1 rounded-full"
            :class="stat.isUp ? 'text-theme-success bg-theme-success/10' : 'text-theme-danger bg-theme-danger/10'"
          >
            <IconTrendingUp v-if="stat.isUp" size="14" />
            {{ stat.trend }}
          </span>
        </div>
        <div>
          <h3 class="text-sm font-medium text-theme-text-sec mb-1">{{ stat.title }}</h3>
          <p class="text-3xl font-bold text-white font-heading tracking-wide">{{ stat.value }}</p>
        </div>
      </div>
    </div>

    <!-- Charts and Top Tracks -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <!-- Main Chart -->
      <div class="lg:col-span-2 bg-theme-surface/40 backdrop-blur-md border border-theme-border/50 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h2 class="text-lg font-bold text-white tracking-wide">Hiệu suất Lượt nghe</h2>
            <p class="text-xs text-theme-text-sec mt-1">Trong 30 ngày qua</p>
          </div>
          <select class="bg-theme-surface border border-theme-border text-xs text-white rounded-lg px-3 py-1.5 outline-none focus:border-theme-accent">
            <option>30 ngày</option>
            <option>7 ngày</option>
            <option>Tất cả</option>
          </select>
        </div>
        
        <div class="h-[300px] w-full">
          <Line :data="chartData" :options="chartOptions" />
        </div>
      </div>

      <!-- Top Tracks -->
      <div class="bg-theme-surface/40 backdrop-blur-md border border-theme-border/50 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-lg font-bold text-white tracking-wide">Bài hát thịnh hành</h2>
          <button class="text-xs text-theme-accent font-medium hover:underline">Xem tất cả</button>
        </div>

        <div class="space-y-4">
          <div 
            v-for="(track, index) in topTracks" 
            :key="track.id"
            class="flex items-center gap-4 group cursor-pointer"
          >
            <div class="text-xs font-bold text-theme-text-sec w-4 text-center group-hover:text-theme-accent">{{ index + 1 }}</div>
            <div class="relative w-12 h-12 rounded-lg overflow-hidden shrink-0">
              <img :src="track.cover" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
              <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                <IconPlayerPlay class="text-white" size="20" fill="currentColor" />
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-bold text-white truncate group-hover:text-theme-accent transition-colors">{{ track.title }}</h4>
              <p class="text-xs text-theme-text-sec truncate">{{ track.streams }} lượt nghe</p>
            </div>
            <div class="text-xs font-bold text-theme-success">{{ track.trend }}</div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>
