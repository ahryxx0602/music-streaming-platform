<script setup lang="ts">
// Auth Layout - Dark mode & Glassmorphism
</script>

<template>
  <div class="auth-layout">
    <div class="auth-container">
      <div class="auth-content">
        <slot />
      </div>
    </div>
    
    <!-- Background elements for visual flair -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
  </div>
</template>

<style scoped>
.auth-layout {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: var(--color-bg-dark);
  background-image: 
    radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
    radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
    radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
  position: relative;
  overflow: hidden;
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

.blob {
  position: absolute;
  filter: blur(80px);
  z-index: 0;
  border-radius: 50%;
  animation: float 10s infinite ease-in-out alternate;
}

.blob-1 {
  width: 400px;
  height: 400px;
  background: rgba(139, 92, 246, 0.4);
  top: -100px;
  left: -100px;
}

.blob-2 {
  width: 500px;
  height: 500px;
  background: rgba(236, 72, 153, 0.3);
  bottom: -150px;
  right: -150px;
  animation-delay: -5s;
}

@keyframes float {
  0% { transform: translate(0, 0) scale(1); }
  100% { transform: translate(30px, 50px) scale(1.1); }
}

.auth-container {
  width: 100%;
  max-width: 480px;
  padding: 2rem;
  z-index: 10;
}

.auth-content {
  background: var(--color-glass-bg);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid var(--color-glass-border);
  border-radius: var(--radius-lg);
  padding: 2.5rem;
  box-shadow: var(--shadow-glass);
  color: #f8fafc;
}
</style>
