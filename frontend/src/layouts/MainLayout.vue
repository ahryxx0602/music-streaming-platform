<template>
  <div class="h-screen w-screen bg-black text-white flex flex-col font-sans overflow-hidden">
    <!-- Main Content Area -->
    <div class="flex flex-1 overflow-hidden">
      <!-- Sidebar (Temporary placeholder) -->
      <aside class="w-64 bg-zinc-950 border-r border-zinc-900 p-6 hidden md:block">
        <h1 class="text-2xl font-bold bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent">MuStream</h1>
        <nav class="mt-8 space-y-4 text-zinc-400 font-medium">
          <router-link to="/" class="block hover:text-white transition">Home</router-link>
          <router-link to="/explore" class="block hover:text-white transition">Explore</router-link>
          <router-link to="/library" class="block hover:text-white transition">Library</router-link>
        </nav>
      </aside>

      <!-- Router View for pages -->
      <main class="flex-1 overflow-y-auto bg-gradient-to-b from-zinc-900 to-black">
        <router-view />
      </main>
    </div>

    <!-- Global Audio Player -->
    <GlobalPlayer />
  </div>
</template>

<script setup>
import GlobalPlayer from '../components/player/GlobalPlayer.vue';
</script>
