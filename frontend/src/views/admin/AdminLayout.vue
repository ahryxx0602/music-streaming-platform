<script setup lang="ts">
import AdminSidebar from '@/components/admin/layout/AdminSidebar.vue';
import AdminHeader from '@/components/admin/layout/AdminHeader.vue';
</script>

<template>
  <div class="flex h-screen w-full bg-slate-50 admin-shell overflow-hidden text-slate-800 antialiased selection:bg-blue-100 selection:text-blue-900">
    <!-- Nền móng Sidebar: Giao diện tối, 260px -->
    <AdminSidebar />
    
    <!-- Cột Content chính -->
    <div class="flex-1 flex flex-col h-full overflow-hidden relative">
      <!-- Top Header Cố định -->
      <AdminHeader />
      
      <!-- Outlet - Vùng chứa nội dung trang. Áp dụng biến Token Padding -->
      <main class="flex-1 overflow-y-auto" style="padding: var(--admin-page-padding, 32px);">
        <div class="max-w-[1600px] mx-auto h-full">
          <router-view v-slot="{ Component }">
            <transition name="fade-page" mode="out-in">
              <component :is="Component" />
            </transition>
          </router-view>
        </div>
      </main>
    </div>
  </div>
</template>

<style scoped>
.fade-page-enter-active,
.fade-page-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.fade-page-enter-from {
  opacity: 0;
  transform: translateY(4px);
}
.fade-page-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
