<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import { 
  IconHome, IconSearch, IconLibrary, IconSettings, IconLogout, 
  IconPlayerPlay, IconPlayerSkipForward, IconPlayerSkipBack, IconVolume
} from '@tabler/icons-vue';

const authStore = useAuthStore();
const router = useRouter();
const user = computed(() => authStore.user);

const handleLogout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>

<template>
  <div class="h-screen w-full bg-black text-white flex flex-col overflow-hidden font-sans">
    
    <!-- Top & Main Section -->
    <div class="flex-1 flex overflow-hidden">
      
      <!-- Sidebar Navigation -->
      <aside class="w-64 bg-slate-950 flex flex-col justify-between py-6 px-4 border-r border-slate-900 hidden md:flex">
        
        <!-- Logo -->
        <div class="mb-8 px-2 flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
            <IconPlayerPlay class="text-white w-5 h-5 ml-1" />
          </div>
          <h1 class="text-xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-500">Aurora<span class="text-white">Stream</span></h1>
        </div>

        <!-- Main Nav -->
        <nav class="space-y-2 flex-1">
          <router-link to="/" class="flex items-center gap-4 px-3 py-3 text-slate-300 hover:text-white font-semibold rounded-lg hover:bg-slate-900 transition" active-class="text-white bg-slate-900">
            <IconHome size="24" stroke="2" />
            Trang chủ
          </router-link>
          <a href="#" class="flex items-center gap-4 px-3 py-3 text-slate-400 hover:text-white font-semibold rounded-lg hover:bg-slate-900 transition">
            <IconSearch size="24" stroke="2" />
            Khám phá
          </a>
          <a href="#" class="flex items-center gap-4 px-3 py-3 text-slate-400 hover:text-white font-semibold rounded-lg hover:bg-slate-900 transition">
            <IconLibrary size="24" stroke="2" />
            Thư viện
          </a>
        </nav>

        <!-- Bottom User Menu -->
        <div class="mt-auto border-t border-slate-800 pt-4">
          <template v-if="authStore.isAuthenticated">
            <router-link to="/settings" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-900 transition mb-2">
              <img v-if="user?.avatar_url || user?.avatar" :src="user?.avatar_url || user?.avatar" class="w-10 h-10 rounded-full object-cover border border-slate-700" />
              <div v-else class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center font-bold text-slate-400">
                {{ user?.name?.charAt(0) }}
              </div>
              <div class="flex-1 truncate">
                <p class="text-sm font-bold truncate">{{ user?.name }}</p>
                <p class="text-xs text-slate-500 capitalize">{{ user?.role }}</p>
              </div>
            </router-link>
            <button @click="handleLogout" class="w-full flex items-center gap-3 px-3 py-2 text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-lg transition text-sm font-semibold">
              <IconLogout size="20" />
              Đăng xuất
            </button>
          </template>
          <template v-else>
            <div class="space-y-3">
              <router-link to="/login" class="block w-full text-center py-2 px-4 rounded-full bg-white text-black font-bold hover:scale-105 transition">Đăng nhập</router-link>
              <router-link to="/register" class="block w-full text-center py-2 px-4 rounded-full bg-slate-800 text-white font-bold hover:bg-slate-700 hover:scale-105 transition">Đăng ký</router-link>
            </div>
          </template>
        </div>
      </aside>

      <!-- Main Scrollable Area -->
      <main class="flex-1 overflow-y-auto bg-gradient-to-b from-slate-900 to-black">
        <div class="p-6 md:p-8">
          <slot></slot>
        </div>
      </main>
    </div>

    <!-- Bottom Player Bar (Mock) -->
    <div class="h-20 bg-slate-950 border-t border-slate-900 flex items-center justify-between px-4">
      <div class="flex items-center gap-4 w-1/4 min-w-[200px]">
        <div class="w-14 h-14 bg-slate-800 rounded flex-shrink-0 shadow-lg overflow-hidden group">
          <img src="https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?q=80&w=200&auto=format&fit=crop" class="w-full h-full object-cover group-hover:scale-110 transition duration-500" />
        </div>
        <div>
          <h4 class="text-sm font-bold text-white cursor-pointer hover:underline">Night Drive</h4>
          <p class="text-xs text-slate-400 cursor-pointer hover:underline">Synthwave Artist</p>
        </div>
      </div>
      
      <div class="flex flex-col items-center max-w-[40%] w-full">
        <div class="flex items-center gap-6 mb-1">
          <button class="text-slate-400 hover:text-white transition"><IconPlayerSkipBack size="20" fill="currentColor" /></button>
          <button class="w-8 h-8 flex items-center justify-center bg-white text-black rounded-full hover:scale-105 transition">
            <IconPlayerPlay size="18" fill="currentColor" class="ml-0.5" />
          </button>
          <button class="text-slate-400 hover:text-white transition"><IconPlayerSkipForward size="20" fill="currentColor" /></button>
        </div>
        <div class="w-full flex items-center gap-2 text-xs text-slate-400">
          <span>1:23</span>
          <div class="flex-1 h-1 bg-slate-800 rounded-full cursor-pointer group">
            <div class="w-1/3 h-full bg-white group-hover:bg-blue-500 rounded-full relative">
              <div class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3 bg-white rounded-full opacity-0 group-hover:opacity-100 shadow"></div>
            </div>
          </div>
          <span>4:05</span>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2 w-1/4 min-w-[150px] text-slate-400">
        <IconVolume size="20" />
        <div class="w-24 h-1 bg-slate-800 rounded-full cursor-pointer group">
          <div class="w-2/3 h-full bg-white group-hover:bg-blue-500 rounded-full"></div>
        </div>
      </div>
    </div>

  </div>
</template>
