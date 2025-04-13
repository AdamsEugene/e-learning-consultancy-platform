<!-- components/courses/FloatingNotesButton.vue -->
<script setup lang="ts">
interface Props {
  isOpen?: boolean;
  hasNotes?: boolean;
}

// Props
withDefaults(defineProps<Props>(), {
  isOpen: false,
  hasNotes: false,
});

const emit = defineEmits(["toggle"]);

const toggleNotes = () => {
  emit("toggle");
};
</script>

<template>
  <button
    class="fixed bottom-[84px] right-6 z-30 p-3 rounded-full shadow-lg transition-all duration-300 transform hover:scale-110 focus:outline-none flex items-center justify-center"
    :class="[
      isOpen
        ? 'bg-red-500 hover:bg-red-600 rotate-45'
        : hasNotes
        ? 'bg-green-500 hover:bg-green-600'
        : 'bg-indigo-600 hover:bg-indigo-700',
    ]"
    aria-label="Toggle notes panel"
    @click="toggleNotes"
  >
    <svg
      v-if="isOpen"
      xmlns="http://www.w3.org/2000/svg"
      class="h-6 w-6 text-white"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M6 18L18 6M6 6l12 12"
      />
    </svg>
    <svg
      v-else
      xmlns="http://www.w3.org/2000/svg"
      class="h-6 w-6 text-white"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
      />
    </svg>
    <span
      v-if="hasNotes && !isOpen"
      class="absolute -top-2 -right-2 flex items-center justify-center w-5 h-5 bg-white text-green-600 text-xs font-bold rounded-full shadow animate-pulse"
    >
      ✓
    </span>
  </button>
</template>

<style scoped>
@keyframes pulse {
  0%,
  100% {
    transform: scale(1);
    opacity: 1;
  }
  50% {
    transform: scale(1.1);
    opacity: 0.8;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
