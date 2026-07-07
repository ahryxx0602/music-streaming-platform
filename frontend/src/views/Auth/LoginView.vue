<template>
  <div class="w-full max-w-md bg-zinc-900/50 backdrop-blur-xl p-8 rounded-2xl border border-zinc-800 shadow-2xl">
    <div class="text-center mb-8">
      <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent">MuStream</h1>
      <p class="text-zinc-400 mt-2">Đăng nhập để nghe nhạc</p>
    </div>
    <form @submit.prevent="handleLogin" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-zinc-400 mb-1">Email</label>
        <input type="email" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" placeholder="name@example.com" />
      </div>
      <div>
        <label class="block text-sm font-medium text-zinc-400 mb-1">Mật khẩu</label>
        <input type="password" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" placeholder="••••••••" />
      </div>
      <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-400 text-black font-bold py-3 rounded-lg transition mt-6">
        Đăng nhập
      </button>
    </form>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
const router = useRouter();

const handleLogin = () => {
  // Fake login
  router.push('/');
}
</script>
