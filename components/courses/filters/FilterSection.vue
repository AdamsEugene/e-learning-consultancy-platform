<!-- components/courses/filters/FilterSection.vue -->
<script setup lang="ts">
import { ref, computed } from "vue";

// Props
interface Props {
  title: string;
  iconType: string;
  color: string;
  selectedValue: string | number;
  hasSelection: boolean;
  filterType: string;
  maxHeight?: boolean;
  class?: string;
}

const props = withDefaults(defineProps<Props>(), {
  maxHeight: false,
  class: "",
});

// Emits
const emit = defineEmits<{
  (e: "change", type: string, value: string | number): void;
}>();

// Local state
const isOpen = ref(props.hasSelection);

// Toggle section visibility
const toggleSection = () => {
  isOpen.value = !isOpen.value;
};

// Clear this filter section
const clearSection = () => {
  // Determine the default value for different filter types
  let defaultValue: string | number = "all";

  switch (props.filterType) {
    case "level":
      defaultValue = "All Levels";
      break;
    case "rating":
      defaultValue = 0;
      break;
    case "duration":
      defaultValue = "any";
      break;
  }

  emit("change", props.filterType, defaultValue);
};

// Get icon component based on iconType
const getIconComponent = computed(() => {
  switch (props.iconType) {
    case "category":
      return `
        <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
      `;
    case "instructor":
      return `
        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
      `;
    case "price":
      return `
        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
      `;
    case "level":
      return `
        <path d="M11 17a1 1 0 001.447.894l4-2A1 1 0 0017 15V9.236a1 1 0 00-1.447-.894l-4 2a1 1 0 00-.553.894V17zM15.211 6.276a1 1 0 000-1.788l-4.764-2.382a1 1 0 00-.894 0L4.789 4.488a1 1 0 000 1.788l4.764 2.382a1 1 0 00.894 0l4.764-2.382zM4.447 8.342A1 1 0 003 9.236V15a1 1 0 00.553.894l4 2A1 1 0 009 17v-5.764a1 1 0 00-.553-.894l-4-2z" />
      `;
    case "rating":
      return `
        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
      `;
    case "duration":
      return `
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
      `;
    default:
      return `
        <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
      `;
  }
});

// Color variants for different filter types
const getColorClasses = computed(() => {
  const colorMap = {
    indigo: {
      text: "text-indigo-600",
      bg: "bg-indigo-100",
      hover: "hover:bg-indigo-50",
      active: "bg-indigo-100",
      border: "border-indigo-200",
      icon: "text-indigo-500",
    },
    green: {
      text: "text-green-600",
      bg: "bg-green-100",
      hover: "hover:bg-green-50",
      active: "bg-green-100",
      border: "border-green-200",
      icon: "text-green-500",
    },
    purple: {
      text: "text-purple-600",
      bg: "bg-purple-100",
      hover: "hover:bg-purple-50",
      active: "bg-purple-100",
      border: "border-purple-200",
      icon: "text-purple-500",
    },
    blue: {
      text: "text-blue-600",
      bg: "bg-blue-100",
      hover: "hover:bg-blue-50",
      active: "bg-blue-100",
      border: "border-blue-200",
      icon: "text-blue-500",
    },
    yellow: {
      text: "text-yellow-600",
      bg: "bg-yellow-100",
      hover: "hover:bg-yellow-50",
      active: "bg-yellow-100",
      border: "border-yellow-200",
      icon: "text-yellow-500",
    },
    red: {
      text: "text-red-600",
      bg: "bg-red-100",
      hover: "hover:bg-red-50",
      active: "bg-red-100",
      border: "border-red-200",
      icon: "text-red-500",
    },
  };

  return colorMap[props.color as keyof typeof colorMap] || colorMap.indigo;
});
</script>

<!-- eslint-disable vue/no-v-html -->
<template>
  <div class="mb-6" :class="[props.class]">
    <!-- Section header with toggle and clear controls -->
    <div class="flex items-center justify-between mb-2">
      <div class="flex items-center">
        <!-- Icon based on filter type -->
        <div
          class="mr-3 p-1.5 rounded-md"
          :class="[getColorClasses.bg, getColorClasses.text]"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            viewBox="0 0 20 20"
            fill="currentColor"
            v-html="getIconComponent"
          />
        </div>

        <!-- Section title -->
        <h4 class="font-medium text-gray-900">{{ title }}</h4>

        <!-- Badge for active filter indicator -->
        <span
          v-if="hasSelection"
          class="ml-2 w-2 h-2 rounded-full"
          :class="getColorClasses.bg"
        />
      </div>

      <!-- Toggle/Clear actions -->
      <div class="flex items-center space-x-1">
        <!-- Clear button (only shown when filter is active) -->
        <button
          v-if="hasSelection"
          class="p-1 hover:bg-gray-100 rounded-md transition-colors"
          :class="getColorClasses.text"
          aria-label="Clear filter"
          @click.stop="clearSection"
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

        <!-- Toggle button -->
        <button
          class="p-1 hover:bg-gray-100 rounded-md transition-colors text-gray-500"
          :aria-expanded="isOpen"
          :aria-controls="`filter-section-${filterType}`"
          @click="toggleSection"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 transform transition-transform"
            :class="{ 'rotate-180': isOpen }"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>
    </div>

    <!-- Filter options -->
    <div
      v-show="isOpen"
      :id="`filter-section-${filterType}`"
      class="ml-2 border-l-2 pl-4 transition-all duration-300"
      :class="[
        getColorClasses.border,
        maxHeight ? 'max-h-48 overflow-y-auto' : '',
      ]"
    >
      <div
        class="space-y-1 py-2"
        :class="hasSelection ? 'animate-highlight' : ''"
      >
        <slot name="options" />
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes highlight {
  0% {
    background-color: rgba(99, 102, 241, 0.1);
  }
  100% {
    background-color: transparent;
  }
}

.animate-highlight {
  animation: highlight 1.5s ease-out;
}

/* Custom scrollbar for max-height sections */
.max-h-48::-webkit-scrollbar {
  width: 4px;
}

.max-h-48::-webkit-scrollbar-track {
  background: rgba(243, 244, 246, 0.5);
  border-radius: 10px;
}

.max-h-48::-webkit-scrollbar-thumb {
  background: rgba(156, 163, 175, 0.5);
  border-radius: 10px;
}

.max-h-48::-webkit-scrollbar-thumb:hover {
  background: rgba(107, 114, 128, 0.5);
}
</style>
