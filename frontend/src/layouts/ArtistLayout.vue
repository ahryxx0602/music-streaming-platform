<template>
  <div class="min-h-screen bg-zinc-950 text-white flex">
    <aside class="w-64 bg-zinc-900 p-6 border-r border-zinc-800">
      <h2 class="text-xl font-bold text-emerald-400">Artist Studio</h2>
    </aside>
    <main class="flex-1 p-8 overflow-y-auto">
      <router-view />
    </main>
  </div>
</template>
