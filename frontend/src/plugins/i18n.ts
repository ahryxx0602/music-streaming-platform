import { createI18n } from 'vue-i18n';
import vi from '../locales/vi.json';
import en from '../locales/en.json';

const savedLocale = localStorage.getItem('app-locale') || 'vi';

export const i18n = createI18n({
  legacy: false,
  globalInjection: true,
  locale: savedLocale,
  fallbackLocale: 'vi',
  messages: {
    vi,
    en
  }
});
