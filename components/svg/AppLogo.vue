<!-- components/AppLogo.vue -->
<script setup lang="ts">
const props = defineProps({
  variant: {
    type: String,
    default: "default", // 'default' or 'light'
  },
});

const isDark = computed(() => props.variant === "light");
</script>

<template>
  <div class="flex items-center">
    <div class="logo-container">
      <svg viewBox="0 0 60 60" class="h-12 w-12 relative z-10">
        <defs>
          <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" :stop-color="isDark ? '#8B5CF6' : '#6366F1'" />
            <stop offset="100%" :stop-color="isDark ? '#C4B5FD' : '#A5B4FC'" />
          </linearGradient>
          <filter id="logoShadow" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow
              dx="0"
              dy="2"
              stdDeviation="3"
              :flood-color="isDark ? 'rgba(0,0,0,0.3)' : 'rgba(0,0,0,0.2)'"
            />
          </filter>
        </defs>
        <circle
          cx="30"
          cy="30"
          r="24"
          fill="url(#logoGradient)"
          filter="url(#logoShadow)"
        />
        <circle
          cx="30"
          cy="30"
          r="16"
          fill="transparent"
          :stroke="isDark ? 'white' : '#4F46E5'"
          stroke-width="3"
        />
        <path
          d="M30 14 L30 30 L42 30"
          fill="none"
          :stroke="isDark ? 'white' : '#4F46E5'"
          stroke-width="3"
          stroke-linecap="round"
        />
      </svg>
      <div class="logo-pulse" />
    </div>
    <span
      class="ml-3 text-2xl font-bold"
      :class="isDark ? 'text-white' : 'text-gray-800'"
      >Fusion<span class="text-indigo-600 font-extrabold">App</span></span
    >
  </div>
</template>

<style scoped>
.logo-container {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo-pulse {
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: rgba(167, 139, 250, 0.5);
  opacity: 0;
  animation: pulse 3s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite;
  z-index: 0;
}

@keyframes pulse {
  0% {
    transform: scale(0.7);
    opacity: 0;
  }
  50% {
    opacity: 0.3;
  }
  100% {
    transform: scale(1.2);
    opacity: 0;
  }
}
</style>
