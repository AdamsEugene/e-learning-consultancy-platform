<!-- components/dashboard/settings/StatusMessage.vue -->
<script setup lang="ts">
import { defineProps } from "vue";

defineProps<{
  type: string;
  message: string;
}>();
</script>

<template>
  <div
    class="px-6 py-4 rounded-lg border transition-all duration-300 transform"
    :class="{
      'bg-green-50 text-green-700 border-green-200': type === 'success',
      'bg-red-50 text-red-700 border-red-200': type === 'error',
      'bg-blue-50 text-blue-700 border-blue-200': type === 'info',
      'animate-pulse': true,
    }"
  >
    <div class="flex items-center">
      <!-- Success icon -->
      <div v-if="type === 'success'" class="flex-shrink-0">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 text-green-500"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
            clip-rule="evenodd"
          />
        </svg>
      </div>

      <!-- Error icon -->
      <div v-else-if="type === 'error'" class="flex-shrink-0">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 text-red-500"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
            clip-rule="evenodd"
          />
        </svg>
      </div>

      <!-- Info icon -->
      <div v-else-if="type === 'info'" class="flex-shrink-0">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 text-blue-500"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zm-1 7a1 1 0 100 2h.01a1 1 0 100-2H10z"
            clip-rule="evenodd"
          />
        </svg>
      </div>

      <div class="ml-3">
        <p class="text-sm font-medium">{{ message }}</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.85;
  }
}

.animate-pulse {
  animation: pulse 2s ease-in-out infinite;
}
</style>
