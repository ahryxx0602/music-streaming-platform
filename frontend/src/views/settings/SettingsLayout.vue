<script setup lang="ts">
import { useAuthStore } from '@/stores/authStore';
import { IconUser, IconShieldLock } from '@tabler/icons-vue';

const authStore = useAuthStore();
</script>

<template>
  <div class="settings-container max-w-6xl mx-auto py-10 px-4 flex flex-col md:flex-row gap-8">
    <!-- Sidebar Menu -->
    <aside class="w-full md:w-64 flex-shrink-0">
      <div class="sidebar-nav bg-theme-surface rounded-2xl p-4 shadow-lg border border-theme-border">
        <h3 class="text-xs font-bold text-theme-text-sec uppercase tracking-wider mb-4 px-3">Cài đặt tài khoản</h3>
        <nav class="space-y-1">
          <router-link to="/settings/profile" class="nav-item flex items-center px-3 py-3 text-sm font-medium rounded-xl text-theme-text-sec hover:text-theme-text hover:bg-theme-surface-hover transition-colors" active-class="bg-blue-500/10 text-theme-primary">
            <IconUser size="18" class="mr-3" />
            Hồ sơ cá nhân
          </router-link>
          <router-link to="/settings/security" class="nav-item flex items-center px-3 py-3 text-sm font-medium rounded-xl text-theme-text-sec hover:text-theme-text hover:bg-theme-surface-hover transition-colors" active-class="bg-blue-500/10 text-theme-primary">
            <IconShieldLock size="18" class="mr-3" />
            Bảo mật & Đăng nhập
          </router-link>
        </nav>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 bg-theme-surface rounded-2xl p-6 shadow-lg border border-theme-border">
      <router-view></router-view>
    </main>
  </div>
</template>

<style scoped>
/* Settings Layout custom overrides if needed */
</style>
