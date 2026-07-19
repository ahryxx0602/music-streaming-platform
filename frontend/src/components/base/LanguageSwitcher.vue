<script setup lang="ts">
import { useI18n } from 'vue-i18n';

const { locale } = useI18n();

const switchLanguage = (lang: 'vi' | 'en') => {
  locale.value = lang;
  localStorage.setItem('app-locale', lang);
};
</script>

<template>
  <div class="relative group">
    <button class="flex items-center gap-2 h-10 px-3 rounded-lg hover:bg-theme-surface-hover transition-colors border border-theme-border bg-theme-surface">
      <span class="text-lg">{{ locale === 'vi' ? '🇻🇳' : '🇬🇧' }}</span>
      <span class="text-sm font-medium text-theme-text uppercase">{{ locale }}</span>
    </button>
    
    <!-- Dropdown -->
    <div class="absolute right-0 top-full mt-2 w-36 bg-theme-surface rounded-xl shadow-lg border border-theme-border py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
      <button 
        @click="switchLanguage('vi')" 
        class="w-full text-left px-4 py-2 text-sm text-theme-text hover:bg-theme-bg hover:text-admin-primary transition-colors flex items-center gap-3"
        :class="{ 'bg-theme-bg text-admin-primary font-medium': locale === 'vi' }"
      >
        <span class="text-lg">🇻🇳</span> Tiếng Việt
      </button>
      <button 
        @click="switchLanguage('en')" 
        class="w-full text-left px-4 py-2 text-sm text-theme-text hover:bg-theme-bg hover:text-admin-primary transition-colors flex items-center gap-3"
        :class="{ 'bg-theme-bg text-admin-primary font-medium': locale === 'en' }"
      >
        <span class="text-lg">🇬🇧</span> English
      </button>
    </div>
  </div>
</template>
