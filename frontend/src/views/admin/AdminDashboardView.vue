<script setup lang="ts">
import { IconUsers, IconMusic, IconMicrophone2 } from '@tabler/icons-vue';
</script>

<template>
  <div class="admin-dashboard">
    <div class="mb-8">
      <h1 class="text-3xl font-black text-slate-900 tracking-tight">Tổng quan Hệ thống</h1>
      <p class="text-slate-500 mt-1">Chào mừng quay trở lại, Admin!</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
        <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
          <IconUsers size="28" />
        </div>
        <div>
          <p class="text-slate-500 font-medium">Tổng Users</p>
          <p class="text-3xl font-black text-slate-800">63</p>
        </div>
      </div>
      
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
        <div class="w-14 h-14 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
          <IconMicrophone2 size="28" />
        </div>
        <div>
          <p class="text-slate-500 font-medium">Nghệ sĩ (Artists)</p>
          <p class="text-3xl font-black text-slate-800">20</p>
        </div>
      </div>
      
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
        <div class="w-14 h-14 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
          <IconMusic size="28" />
        </div>
        <div>
          <p class="text-slate-500 font-medium">Bài hát tải lên</p>
          <p class="text-3xl font-black text-slate-800">10</p>
        </div>
      </div>
    </div>
  </div>
</template>
