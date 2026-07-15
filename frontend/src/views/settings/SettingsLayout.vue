<script setup lang="ts">
import { useAuthStore } from '@/stores/authStore';
import { IconUser, IconShieldLock } from '@tabler/icons-vue';

const authStore = useAuthStore();
</script>

<template>
  <div class="settings-container max-w-6xl mx-auto py-10 px-4 flex flex-col md:flex-row gap-8">
    <!-- Sidebar Menu -->
    <aside class="w-full md:w-64 flex-shrink-0">
      <div class="sidebar-nav theme-surface rounded-2xl p-4 shadow-lg border theme-border">
        <h3 class="text-xs font-bold text-secondary uppercase tracking-wider mb-4 px-3">Cài đặt tài khoản</h3>
        <nav class="space-y-1">
          <router-link to="/settings/profile" class="nav-item flex items-center px-3 py-3 text-sm font-medium rounded-xl text-secondary hover:text-white hover-surface transition-colors" active-class="active-nav-item">
            <IconUser size="18" class="mr-3" />
            Hồ sơ cá nhân
          </router-link>
          <router-link to="/settings/security" class="nav-item flex items-center px-3 py-3 text-sm font-medium rounded-xl text-secondary hover:text-white hover-surface transition-colors" active-class="active-nav-item">
            <IconShieldLock size="18" class="mr-3" />
            Bảo mật & Đăng nhập
          </router-link>
        </nav>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 theme-surface rounded-2xl p-6 shadow-lg border theme-border">
      <router-view></router-view>
    </main>
  </div>
</template>

<style scoped>
.theme-surface { background-color: var(--color-surface, #131B2F); }
.theme-border { border-color: var(--color-input-border, #2A3B57); }
.text-secondary { color: var(--color-text-secondary, #CBD5E1); }
.hover-surface:hover { background-color: rgba(255, 255, 255, 0.05); }

.active-nav-item {
  background-color: rgba(59, 130, 246, 0.1); /* blue-500/10 */
  color: var(--color-primary, #3B82F6);
}
.active-nav-item svg {
  color: var(--color-primary, #3B82F6);
}
</style>
