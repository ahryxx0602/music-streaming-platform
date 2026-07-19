<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import { 
  IconHome, IconSearch, IconLibrary, IconSettings, IconLogout, 
  IconPlayerPlay, IconPlayerSkipForward, IconPlayerSkipBack, IconVolume
} from '@tabler/icons-vue';
import LanguageSwitcher from '@/components/base/LanguageSwitcher.vue';
import ThemeToggle from '@/components/base/ThemeToggle.vue';

const authStore = useAuthStore();
const router = useRouter();
const user = computed(() => authStore.user);

const handleLogout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>

<template>
  <div class="h-screen w-full bg-theme-bg text-theme-text flex flex-col overflow-hidden font-sans">
    
    <!-- Top & Main Section -->
    <div class="flex-1 flex overflow-hidden">
      
      <!-- Sidebar Navigation -->
      <aside class="w-64 bg-theme-surface flex flex-col justify-between py-6 px-4 border-r border-theme-border hidden md:flex">
        
        <!-- Logo -->
        <div class="mb-8 px-2 flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-theme-primary flex items-center justify-center shadow-lg">
            <IconPlayerPlay class="text-white w-5 h-5 ml-1" />
          </div>
          <h1 class="text-xl font-black tracking-tight text-theme-text">Aurora<span class="text-theme-primary">Stream</span></h1>
        </div>

        <!-- Main Nav -->
        <nav class="space-y-2 flex-1">
          <router-link to="/" class="flex items-center gap-4 px-3 py-3 text-theme-text-sec hover:text-theme-text font-semibold rounded-lg hover:bg-theme-surface-hover transition" active-class="text-theme-primary bg-theme-surface-hover">
            <IconHome size="24" stroke="2" />
            {{ $t('client.nav.home') }}
          </router-link>
          <a href="#" class="flex items-center gap-4 px-3 py-3 text-theme-text-sec hover:text-theme-text font-semibold rounded-lg hover:bg-theme-surface-hover transition">
            <IconSearch size="24" stroke="2" />
            {{ $t('client.nav.explore') }}
          </a>
          <a href="#" class="flex items-center gap-4 px-3 py-3 text-theme-text-sec hover:text-theme-text font-semibold rounded-lg hover:bg-theme-surface-hover transition">
            <IconLibrary size="24" stroke="2" />
            {{ $t('client.nav.library') }}
          </a>
        </nav>

        <!-- Bottom User Menu -->
        <div class="mt-auto border-t border-theme-border pt-4 flex flex-col gap-3">
          <div class="flex items-center justify-between px-2">
            <LanguageSwitcher />
            <ThemeToggle />
          </div>
          <template v-if="authStore.isAuthenticated">
            <router-link to="/settings" class="flex items-center gap-3 p-2 rounded-lg hover:bg-theme-surface-hover transition mb-2">
              <img v-if="user?.avatar_url || user?.avatar" :src="user?.avatar_url || user?.avatar" class="w-10 h-10 rounded-full object-cover border border-theme-border" />
              <div v-else class="w-10 h-10 rounded-full bg-theme-bg flex items-center justify-center font-bold text-theme-text-sec">
                {{ user?.name?.charAt(0) }}
              </div>
              <div class="flex-1 truncate">
                <p class="text-sm font-bold truncate text-theme-text">{{ user?.name }}</p>
                <p class="text-xs text-theme-text-sec capitalize">{{ user?.role }}</p>
              </div>
            </router-link>
            <button @click="handleLogout" class="w-full flex items-center gap-3 px-3 py-2 text-theme-danger hover:opacity-80 hover:bg-theme-danger/10 rounded-lg transition text-sm font-semibold">
              <IconLogout size="20" />
              {{ $t('client.user_menu.logout') }}
            </button>
          </template>
          <template v-else>
            <div class="space-y-3">
              <router-link to="/login" class="block w-full text-center py-2 px-4 rounded-full bg-theme-primary text-white font-bold hover:opacity-90 hover:scale-105 transition">{{ $t('auth.login') }}</router-link>
              <router-link to="/register" class="block w-full text-center py-2 px-4 rounded-full bg-theme-surface-hover text-theme-text font-bold hover:bg-theme-border hover:scale-105 transition">{{ $t('auth.register') }}</router-link>
            </div>
          </template>
        </div>
      </aside>

      <!-- Main Scrollable Area -->
      <main class="flex-1 overflow-y-auto bg-theme-bg">
        <div class="p-6 md:p-8">
          <slot></slot>
        </div>
      </main>
    </div>

    <!-- Bottom Player Bar (Mock) -->
    <div class="h-20 bg-theme-surface border-t border-theme-border flex items-center justify-between px-4">
      <div class="flex items-center gap-4 w-1/4 min-w-[200px]">
        <div class="w-14 h-14 bg-slate-800 rounded flex-shrink-0 shadow-lg overflow-hidden group">
          <img src="https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?q=80&w=200&auto=format&fit=crop" class="w-full h-full object-cover group-hover:scale-110 transition duration-500" />
        </div>
        <div>
          <h4 class="text-sm font-bold text-theme-text cursor-pointer hover:underline">{{ $t('client.player.unknown_song') }}</h4>
          <p class="text-xs text-theme-text-sec cursor-pointer hover:underline">{{ $t('client.player.unknown_artist') }}</p>
        </div>
      </div>
      
      <div class="flex flex-col items-center max-w-[40%] w-full">
        <div class="flex items-center gap-6 mb-1">
          <button class="text-theme-text-sec hover:text-theme-text transition"><IconPlayerSkipBack size="20" fill="currentColor" /></button>
          <button class="w-8 h-8 flex items-center justify-center bg-theme-text text-theme-bg rounded-full hover:scale-105 transition">
            <IconPlayerPlay size="18" fill="currentColor" class="ml-0.5" />
          </button>
          <button class="text-theme-text-sec hover:text-theme-text transition"><IconPlayerSkipForward size="20" fill="currentColor" /></button>
        </div>
        <div class="w-full flex items-center gap-2 text-xs text-theme-text-sec">
          <span>1:23</span>
          <div class="flex-1 h-1 bg-theme-surface-hover rounded-full cursor-pointer group">
            <div class="w-1/3 h-full bg-theme-primary group-hover:brightness-110 rounded-full relative">
              <div class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3 bg-white rounded-full opacity-0 group-hover:opacity-100 shadow"></div>
            </div>
          </div>
          <span>4:05</span>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2 w-1/4 min-w-[150px] text-theme-text-sec">
        <IconVolume size="20" />
        <div class="w-24 h-1 bg-theme-surface-hover rounded-full cursor-pointer group">
          <div class="w-2/3 h-full bg-theme-primary group-hover:brightness-110 rounded-full"></div>
        </div>
      </div>
    </div>

  </div>
</template>
