<!-- components/consultancy/ConsultancyProjectCard.vue -->
<script setup lang="ts">
import { ref, computed } from "vue";

interface StatItem {
  label: string;
  value: string;
}

interface Props {
  project: {
    id: number;
    title: string;
    client: string;
    description: string;
    image: string;
    stats: StatItem[];
    tags?: string[];
    featured?: boolean;
  };
  animated?: boolean;
  delay?: number;
}

// Props
const props = withDefaults(defineProps<Props>(), {
  animated: true,
  delay: 0,
});

// State
const isHovered = ref(false);

// If image is a placeholder, add a colorful gradient overlay
const isPlaceholder = computed(() => {
  return props.project.image.includes("/api/placeholder");
});

// Emit events
const emit = defineEmits(["view-project"]);

// Handle view project click
const handleViewProject = () => {
  emit("view-project", props.project.id);
};
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div
    class="group relative flex flex-col overflow-hidden rounded-xl bg-white border border-gray-200 shadow-lg transition-all duration-500 h-full"
    :class="[animated ? 'hover:shadow-xl' : '', isHovered ? 'shadow-xl' : '']"
    :style="animated ? `transition-delay: ${delay}ms;` : ''"
    @mouseenter="isHovered = true"
    @mouseleave="isHovered = false"
  >
    <!-- Project image with overlay -->
    <div class="relative h-56 overflow-hidden">
      <img
        :src="project.image"
        :alt="project.title"
        class="w-full h-full object-cover transition-transform duration-700"
        :class="isHovered ? 'scale-110' : 'scale-100'"
      />

      <!-- Gradient overlay -->
      <div
        class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent opacity-70 transition-opacity duration-300"
        :class="isHovered ? 'opacity-80' : ''"
      />

      <!-- Colorful overlay for placeholder images -->
      <div
        v-if="isPlaceholder"
        class="absolute inset-0 bg-gradient-to-br from-indigo-500 to-purple-600 opacity-40 mix-blend-multiply"
      />

      <!-- Project info overlay -->
      <div class="absolute bottom-0 left-0 p-6 text-white">
        <!-- Client badge -->
        <div
          class="inline-block mb-2 bg-white bg-opacity-20 backdrop-blur-sm rounded-full px-3 py-1 text-sm font-medium"
        >
          {{ project.client }}
        </div>

        <!-- Project title -->
        <h3
          class="text-xl font-bold mb-1 transition-all duration-500"
          :class="isHovered ? 'translate-y-0' : 'translate-y-0'"
        >
          {{ project.title }}
        </h3>

        <!-- Tags if available -->
        <div
          v-if="project.tags && project.tags.length"
          class="flex flex-wrap gap-2 mt-2"
        >
          <span
            v-for="tag in project.tags"
            :key="tag"
            class="text-xs bg-white bg-opacity-20 backdrop-blur-sm rounded-full px-2 py-1"
          >
            {{ tag }}
          </span>
        </div>
      </div>

      <!-- Featured badge -->
      <div
        v-if="project.featured"
        class="absolute top-4 right-4 bg-gradient-to-r from-yellow-500 to-amber-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg transform transition-transform duration-500"
        :class="isHovered ? 'scale-110' : ''"
      >
        Featured
      </div>
    </div>

    <!-- Project content -->
    <div class="p-6 flex-grow">
      <!-- Project description -->
      <p class="text-gray-600 mb-6">{{ project.description }}</p>

      <!-- Project stats -->
      <div class="grid grid-cols-3 gap-4 mb-4">
        <div
          v-for="(stat, index) in project.stats"
          :key="index"
          class="text-center transition-all duration-500"
          :class="isHovered ? 'transform scale-105' : ''"
          :style="`transition-delay: ${index * 100}ms`"
        >
          <div class="text-indigo-600 font-bold text-xl mb-1">
            {{ stat.value }}
          </div>
          <div class="text-gray-500 text-sm">{{ stat.label }}</div>
        </div>
      </div>
    </div>

    <!-- Call to action -->
    <div
      class="p-4 border-t border-gray-100 bg-gray-50 transition-all duration-500"
      :class="isHovered ? 'bg-gray-100' : ''"
    >
      <button
        class="w-full py-2 px-4 rounded-lg font-medium transition-all duration-300 flex items-center justify-center"
        :class="
          isHovered
            ? 'bg-indigo-600 text-white'
            : 'bg-white border border-indigo-600 text-indigo-600 hover:bg-indigo-50'
        "
        @click="handleViewProject"
      >
        <span>View Case Study</span>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 ml-2 transition-transform duration-500"
          :class="isHovered ? 'transform translate-x-1' : ''"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M14 5l7 7m0 0l-7 7m7-7H3"
          />
        </svg>
      </button>
    </div>
  </div>
</template>

<style scoped>
/* Add some additional hover animations */
.group:hover {
  transform: translateY(-5px);
}

@keyframes pulse-subtle {
  0%,
  100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}

.pulse-animation {
  animation: pulse-subtle 2s infinite;
}
</style>
