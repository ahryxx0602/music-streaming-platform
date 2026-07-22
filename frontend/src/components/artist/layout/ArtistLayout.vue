<script setup lang="ts">
import ArtistSidebar from './ArtistSidebar.vue';
import ArtistHeader from './ArtistHeader.vue';
</script>

<template>
  <div class="flex h-screen bg-theme-bg overflow-hidden theme-artist">
    <!-- Sidebar -->
    <ArtistSidebar />
    
    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col relative z-0">
      
      <!-- Background Ambient Glow -->
      <div class="absolute top-0 left-1/4 w-[600px] h-[600px] bg-theme-primary/10 rounded-full blur-[120px] -z-10 pointer-events-none"></div>
      <div class="absolute bottom-0 right-1/4 w-[400px] h-[400px] bg-theme-accent/5 rounded-full blur-[100px] -z-10 pointer-events-none"></div>

      <!-- Header -->
      <ArtistHeader />
      
      <!-- Scrollable Main View -->
      <main class="flex-1 overflow-auto custom-scrollbar">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>
    </div>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
