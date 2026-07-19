<script setup lang="ts">
import { IconHeadphones, IconDisc, IconMicrophone2 } from '@tabler/icons-vue';
import LanguageSwitcher from '@/components/base/LanguageSwitcher.vue';
import ThemeToggle from '@/components/base/ThemeToggle.vue';
</script>

<template>
  <div class="auth-layout relative min-h-screen flex items-center justify-center overflow-hidden bg-theme-bg text-theme-text transition-colors duration-500">
    <!-- Utilities -->
    <div class="absolute top-6 right-6 z-50 flex items-center gap-2">
      <LanguageSwitcher />
      <ThemeToggle />
    </div>
    
    <!-- 1. Ambient Glow Lights -->
    <div class="ambient-lights">
      <div class="light light-1"></div>
      <div class="light light-2"></div>
    </div>

    <!-- 2. Audio Wave Spectrum -->
    <div class="audio-wave"></div>

    <!-- 3. Hero Illustrations (Đồng bộ phong cách) -->
    <div class="hero-illustrations">
      <IconHeadphones class="hero-icon icon-1" size="240" stroke-width="1" />
      <IconDisc class="hero-icon icon-2" size="200" stroke-width="1" />
      <IconMicrophone2 class="hero-icon icon-3" size="220" stroke-width="1" />
    </div>

    <!-- 4. Realistic Vinyl Record -->
    <div class="vinyl-record">
      <div class="vinyl-grooves"></div>
      <div class="vinyl-label">
        <div class="vinyl-hole"></div>
      </div>
      <!-- Vinyl Highlight Highlight -->
      <div class="vinyl-reflection"></div>
    </div>

    <!-- 5. Glassmorphism Form Container (Tăng chiều rộng form) -->
    <div class="auth-container z-10 w-full max-w-lg entry-animation">
      <div class="auth-content glass rounded-[var(--radius-lg,24px)] p-10 shadow-2xl relative overflow-hidden">
        <slot />
      </div>
    </div>
  </div>
</template>

<style scoped>
.auth-layout {
  position: relative; overflow: hidden;
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

/* Audio Wave Background */
.audio-wave {
  position: absolute; bottom: 0; left: 0; right: 0; height: 35%;
  background: repeating-linear-gradient(90deg, transparent, transparent 10px, rgba(59, 130, 246, 0.05) 10px, rgba(59, 130, 246, 0.05) 14px);
  mask-image: linear-gradient(to top, rgba(0,0,0,1), transparent);
  -webkit-mask-image: linear-gradient(to top, rgba(0,0,0,1), transparent);
  z-index: 0; opacity: 0.8;
}

/* Hero Illustrations */
.hero-illustrations { position: absolute; inset: 0; z-index: 1; pointer-events: none; }
.hero-icon {
  position: absolute; color: var(--color-primary); opacity: 0.12; filter: blur(1px);
  animation: floatIcon 30s infinite alternate ease-in-out;
}
.icon-1 { top: 15%; right: 10%; transform: rotate(15deg); }
.icon-2 { bottom: 20%; left: 8%; transform: rotate(-20deg); animation-delay: -10s; }
.icon-3 { top: 40%; right: -5%; transform: rotate(45deg); animation-delay: -20s; }

@keyframes floatIcon {
  0% { transform: translate(0, 0) rotate(0deg); }
  100% { transform: translate(-30px, 30px) rotate(15deg); }
}

/* Ambient Lights */
.ambient-lights { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
.light { position: absolute; border-radius: 50%; filter: blur(120px); opacity: 0.4; animation: floatLight 25s infinite alternate ease-in-out; }
.light-1 { width: 700px; height: 700px; background: var(--color-primary); top: -20%; left: -10%; }
.light-2 { width: 600px; height: 600px; background: var(--color-secondary); bottom: -20%; right: -10%; animation-delay: -5s; }

@keyframes floatLight {
  0% { transform: translate(0, 0) scale(1); }
  100% { transform: translate(80px, -80px) scale(1.1); }
}

/* Realistic Vinyl Record */
.vinyl-record {
  position: absolute; top: 50%; left: 50%;
  margin-top: -350px; margin-left: -350px;
  width: 700px; height: 700px; border-radius: 50%;
  background: #0B0E14; box-shadow: 0 15px 50px rgba(0, 0, 0, 0.9), inset 0 0 0 6px #1A2740;
  z-index: 2; display: flex; align-items: center; justify-content: center;
  overflow: hidden; animation: spinRecord 18s linear infinite;
}

.vinyl-grooves {
  position: absolute; inset: 6px; border-radius: 50%;
  background: repeating-radial-gradient(circle at center, #05070a 0px, #131B2F 3px, #05070a 6px);
  z-index: 2;
}

.vinyl-label {
  position: relative; width: 220px; height: 220px; border-radius: 50%;
  background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
  z-index: 4; display: flex; align-items: center; justify-content: center;
  box-shadow: 0 0 0 8px #0B0E14, inset 0 0 20px rgba(0,0,0,0.5);
}

.vinyl-hole {
  width: 16px; height: 16px; border-radius: 50%;
  background: #0A0F1F; box-shadow: inset 0 2px 5px rgba(0,0,0,0.9);
}

/* Nâng cấp Reflection: Vệt sáng quét mạnh hơn qua đĩa than */
.vinyl-reflection {
  position: absolute; inset: 0; border-radius: 50%;
  background: conic-gradient(
    transparent 0deg, 
    transparent 20deg, 
    rgba(255, 255, 255, 0.03) 30deg, 
    rgba(255, 255, 255, 0.25) 45deg, 
    rgba(255, 255, 255, 0.03) 60deg, 
    transparent 70deg, 
    transparent 200deg, 
    rgba(255, 255, 255, 0.03) 210deg, 
    rgba(255, 255, 255, 0.25) 225deg, 
    rgba(255, 255, 255, 0.03) 240deg, 
    transparent 250deg,
    transparent 360deg
  );
  z-index: 3;
}

@keyframes spinRecord { 100% { transform: rotate(360deg); } }

/* Form Container & Entry Animation */
.auth-container { width: 100%; max-width: 520px; padding: 1.5rem; z-index: 10; } /* Tăng chiều rộng lên 520px */

.entry-animation { animation: formEntry 400ms ease-out forwards; }

@keyframes formEntry {
  0% { transform: scale(0.98); filter: blur(8px); opacity: 0; }
  100% { transform: scale(1); filter: blur(0); opacity: 1; }
}

.auth-content {
  /* Using standard glass utility instead of hardcoded background */
  z-index: 10;
}
</style>
