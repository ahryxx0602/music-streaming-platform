<script setup lang="ts">
import { useAuthStore } from '@/stores/authStore';
import { useRouter } from 'vue-router';
import { 
  IconBell, 
  IconSearch,
  IconLogout,
  IconSettings
} from '@tabler/icons-vue';
import { ref } from 'vue';

const authStore = useAuthStore();
const router = useRouter();

const showDropdown = ref(false);

const handleLogout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>

<template>
  <header class="h-[80px] bg-theme-background/80 backdrop-blur-xl border-b border-theme-border/30 flex items-center justify-between px-8 z-10 sticky top-0">
    
    <!-- Search Bar -->
    <div class="flex-1 max-w-md">
      <div class="relative group">
        <IconSearch class="absolute left-4 top-1/2 -translate-y-1/2 text-theme-text-sec group-focus-within:text-theme-accent transition-colors" size="20" />
        <input 
          type="text" 
          :placeholder="$t('common.search')"
          class="w-full bg-theme-surface/50 border border-theme-border/50 text-theme-text text-sm rounded-full pl-11 pr-4 py-3 focus:outline-none focus:ring-1 focus:ring-theme-accent focus:border-theme-accent focus:bg-theme-surface transition-all placeholder:text-theme-text-sec/70"
        >
      </div>
    </div>

    <!-- Right Actions -->
    <div class="flex items-center gap-6">
      <!-- Notifications -->
      <button class="relative p-2 text-theme-text-sec hover:text-white transition-colors">
        <IconBell size="24" />
        <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-theme-destructive rounded-full border-2 border-theme-bg"></span>
      </button>

      <!-- Profile Dropdown -->
      <div class="relative">
        <button 
          @click="showDropdown = !showDropdown"
          class="flex items-center gap-3 pl-2 pr-4 py-1.5 rounded-full bg-theme-surface/30 border border-theme-border/30 hover:border-theme-border hover:bg-theme-surface/50 transition-all"
        >
          <img 
            :src="authStore.user?.avatar_url || `https://ui-avatars.com/api/?name=${authStore.user?.name}&background=1E1B4B&color=fff`" 
            class="w-8 h-8 rounded-full object-cover border border-theme-border"
          />
          <div class="text-left flex flex-col justify-center">
            <span class="text-sm font-semibold text-white leading-none">{{ authStore.user?.name }}</span>
            <span class="text-[11px] text-theme-accent font-medium mt-0.5">Verified Artist</span>
          </div>
        </button>

        <!-- Dropdown Menu -->
        <div v-if="showDropdown" class="absolute right-0 mt-2 w-56 bg-theme-surface/95 backdrop-blur-xl border border-theme-border/50 rounded-2xl shadow-xl overflow-hidden py-2 transform opacity-100 scale-100 transition-all origin-top-right">
          <div class="px-4 py-3 border-b border-theme-border/30 mb-2">
            <p class="text-sm font-medium text-white">{{ authStore.user?.email }}</p>
          </div>
          
          <router-link to="/artist/profile" class="flex items-center gap-3 px-4 py-2.5 text-sm text-theme-text hover:text-white hover:bg-white/5 transition-colors">
            <IconSettings size="18" class="text-theme-text-sec" />
            Cài đặt tài khoản
          </router-link>
          
          <div class="h-px bg-theme-border/30 my-2"></div>
          
          <button @click="handleLogout" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-theme-danger hover:bg-theme-danger/10 transition-colors">
            <IconLogout size="18" />
            Đăng xuất
          </button>
        </div>
      </div>
    </div>
  </header>
</template>
