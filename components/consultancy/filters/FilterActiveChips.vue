<!-- components/consultancy/filters/FilterActiveChips.vue -->
<script setup lang="ts">
import { computed } from "vue";

interface Filters {
  industry: string;
  service: string;
  year: number | null;
  search: string;
}

interface Props {
  filters: Filters;
  isAnimating?: boolean;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  (e: "filter-change", type: string, value: string | number | null): void;
}>();

// Helper to clear a specific filter
const clearFilter = (type: string) => {
  switch (type) {
    case "industry":
      emit("filter-change", "industry", "");
      break;
    case "service":
      emit("filter-change", "service", "");
      break;
    case "year":
      emit("filter-change", "year", null);
      break;
    case "search":
      emit("filter-change", "search", "");
      break;
  }
};

// Filter active chips
const activeFilters = computed(() => {
  const active = [];

  if (props.filters.industry) {
    active.push({
      type: "industry",
      label: props.filters.industry,
      color: "indigo",
    });
  }

  if (props.filters.service) {
    active.push({
      type: "service",
      label: props.filters.service,
      color: "purple",
    });
  }

  if (props.filters.year) {
    active.push({
      type: "year",
      label: props.filters.year.toString(),
      color: "blue",
    });
  }

  if (props.filters.search) {
    active.push({
      type: "search",
      label: `"${props.filters.search}"`,
      color: "green",
    });
  }

  return active;
});
</script>

<template>
  <div class="mb-5 flex flex-wrap gap-2">
    <div
      v-for="(filter, index) in activeFilters"
      :key="`${filter.type}-${filter.label}`"
      class="active-filter-chip inline-flex items-center py-1 pl-3 pr-1 rounded-full text-sm transition-all duration-300"
      :class="[
        `bg-${filter.color}-100 text-${filter.color}-800`,
        isAnimating ? 'animate-bounce-once' : '',
      ]"
      :style="`animation-delay: ${index * 100}ms`"
    >
      <span class="mr-1">{{ filter.label }}</span>
      <button
        class="w-6 h-6 rounded-full inline-flex items-center justify-center hover:bg-white transition-colors"
        :class="`hover:text-${filter.color}-600`"
        @click="clearFilter(filter.type)"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-4 w-4"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
            clip-rule="evenodd"
          />
        </svg>
      </button>
    </div>
  </div>
</template>

<style scoped>
@keyframes bounce-once {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-5px);
  }
}

.animate-bounce-once {
  animation: bounce-once 0.5s ease;
}

.active-filter-chip {
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}
</style>
