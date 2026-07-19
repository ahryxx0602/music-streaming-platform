<script setup lang="ts">
import { IconUsers, IconMusic, IconMicrophone2 } from '@tabler/icons-vue';
</script>

<template>
  <div class="admin-dashboard">
    <div class="mb-8">
      <h1 class="text-3xl font-black text-theme-text tracking-tight">{{ $t('admin.dashboard_stats.overview') }}</h1>
      <p class="text-theme-text-sec mt-1">{{ $t('admin.dashboard_stats.welcome') }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-theme-surface p-6 rounded-2xl shadow-sm border border-theme-border flex items-center gap-4 transition-colors hover:border-theme-primary/50">
        <div class="w-14 h-14 rounded-full bg-blue-100 dark:bg-blue-500/15 flex items-center justify-center text-blue-600 dark:text-blue-400">
          <IconUsers size="28" />
        </div>
        <div>
          <p class="text-theme-text-sec font-medium">{{ $t('admin.dashboard_stats.total_users') }}</p>
          <p class="text-3xl font-black text-theme-text">63</p>
        </div>
      </div>
      
      <div class="bg-theme-surface p-6 rounded-2xl shadow-sm border border-theme-border flex items-center gap-4 transition-colors hover:border-theme-primary/50">
        <div class="w-14 h-14 rounded-full bg-purple-100 dark:bg-purple-500/15 flex items-center justify-center text-purple-600 dark:text-purple-400">
          <IconMicrophone2 size="28" />
        </div>
        <div>
          <p class="text-theme-text-sec font-medium">{{ $t('admin.dashboard_stats.total_artists') }}</p>
          <p class="text-3xl font-black text-theme-text">20</p>
        </div>
      </div>
      
      <div class="bg-theme-surface p-6 rounded-2xl shadow-sm border border-theme-border flex items-center gap-4 transition-colors hover:border-theme-primary/50">
        <div class="w-14 h-14 rounded-full bg-emerald-100 dark:bg-emerald-500/15 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
          <IconMusic size="28" />
        </div>
        <div>
          <p class="text-theme-text-sec font-medium">{{ $t('admin.dashboard_stats.uploaded_tracks') }}</p>
          <p class="text-3xl font-black text-theme-text">10</p>
        </div>
      </div>
    </div>
  </div>
</template>
