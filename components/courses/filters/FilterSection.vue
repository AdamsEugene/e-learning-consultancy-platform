<!-- components/courses/filters/FilterSection.vue -->
<script setup lang="ts">
import { ref, computed } from "vue";

interface Props {
  title: string;
  iconType: string;
  color: "indigo" | "green" | "purple" | "blue" | "yellow" | "red";
  selectedValue: string;
  hasSelection: boolean;
  filterType: string;
  maxHeight?: boolean;
  class?: string;
}

const props = defineProps<Props>();

defineEmits<{
  (e: "change", type: string, value: string | number): void;
}>();

// For animation
const isExpanded = ref(false);
const isMouseOver = ref(false);
const isTouched = ref(false);

// Get icon based on filter type
const icon = computed(() => {
  switch (props.iconType) {
    case "category":
      return `<path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />`;
    case "instructor":
      return `<path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />`;
    case "price":
      return `<path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />`;
    case "level":
      return `<path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z" />`;
    case "rating":
      return `<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />`;
    case "duration":
      return `<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />`;
    default:
      return `<path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />`;
  }
});

// Toggle expansion state on mobile accordions
const toggleExpand = () => {
  isExpanded.value = !isExpanded.value;
  isTouched.value = true;
};

// Handle hover state
const onMouseEnter = () => {
  isMouseOver.value = true;
};

const onMouseLeave = () => {
  isMouseOver.value = false;
};

// For optional CSS classes
const sectionClasses = computed(() => {
  return {
    "mb-6": props.class === undefined || !props.class.includes("mb-0"),
    [props.class || ""]: !!props.class,
  };
});

// Calculate color classes
const colorClasses = computed(() => {
  const baseColor = props.color;

  // Direct classes without computed string interpolation
  // This avoids the tailwind compilation issues
  if (baseColor === "indigo") {
    return {
      icon: "text-indigo-500 group-hover:text-indigo-600",
      badge: "bg-indigo-100 text-indigo-800",
      heading: "group-hover:text-indigo-600",
    };
  } else if (baseColor === "green") {
    return {
      icon: "text-green-500 group-hover:text-green-600",
      badge: "bg-green-100 text-green-800",
      heading: "group-hover:text-green-600",
    };
  } else if (baseColor === "purple") {
    return {
      icon: "text-purple-500 group-hover:text-purple-600",
      badge: "bg-purple-100 text-purple-800",
      heading: "group-hover:text-purple-600",
    };
  } else if (baseColor === "blue") {
    return {
      icon: "text-blue-500 group-hover:text-blue-600",
      badge: "bg-blue-100 text-blue-800",
      heading: "group-hover:text-blue-600",
    };
  } else if (baseColor === "yellow") {
    return {
      icon: "text-yellow-500 group-hover:text-yellow-600",
      badge: "bg-yellow-100 text-yellow-800",
      heading: "group-hover:text-yellow-600",
    };
  } else if (baseColor === "red") {
    return {
      icon: "text-red-500 group-hover:text-red-600",
      badge: "bg-red-100 text-red-800",
      heading: "group-hover:text-red-600",
    };
  } else {
    // Default fallback
    return {
      icon: "text-gray-500 group-hover:text-gray-600",
      badge: "bg-gray-100 text-gray-800",
      heading: "group-hover:text-gray-600",
    };
  }
});
</script>

<!-- eslint-disable vue/no-v-html -->
<template>
  <div
    :class="sectionClasses"
    class="filter-section group"
    @mouseenter="onMouseEnter"
    @mouseleave="onMouseLeave"
  >
    <div class="header-wrapper">
      <h4
        class="font-medium text-gray-900 mb-3 transition-colors flex items-center"
        :class="colorClasses.heading"
        @click="toggleExpand"
      >
        <span class="icon-wrapper">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 mr-2 transition-all"
            :class="colorClasses.icon"
            viewBox="0 0 20 20"
            fill="currentColor"
            v-html="icon"
          />
        </span>
        <span class="filter-title">{{ title }}</span>
        <span
          v-if="hasSelection"
          class="filter-badge ml-auto text-xs font-medium px-2 py-0.5 rounded-full"
          :class="colorClasses.badge"
          >1</span
        >
      </h4>
    </div>

    <div
      class="options-container transition-all duration-300"
      :class="{ 'max-h-48 overflow-y-auto custom-scrollbar pr-2': maxHeight }"
    >
      <slot name="options" />
    </div>
  </div>
</template>

<style scoped>
.filter-section {
  position: relative;
  transition: all 0.3s ease;
}

.header-wrapper {
  position: relative;
  cursor: pointer;
}

.icon-wrapper {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.filter-title {
  position: relative;
  overflow: hidden;
}

.filter-title::after {
  content: "";
  position: absolute;
  bottom: -3px;
  left: 0;
  width: 0;
  height: 1px;
  transition: width 0.3s ease;
}

.group:hover .filter-title::after {
  width: 100%;
  background-color: currentColor;
}

.filter-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  transform-origin: center;
}

.group:hover .filter-badge {
  transform: scale(1.05);
}

.options-container {
  opacity: 0.95;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.group:hover .options-container {
  opacity: 1;
}

.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: rgba(156, 163, 175, 0.5) rgba(229, 231, 235, 0.5);
}

.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(229, 231, 235, 0.5);
  border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(156, 163, 175, 0.5);
  border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: rgba(156, 163, 175, 0.7);
}

@media (max-width: 768px) {
  .filter-section {
    border-radius: 0.5rem;
    overflow: hidden;
  }

  .header-wrapper {
    padding: 0.75rem 1rem;
    margin-bottom: 0;
    background-color: rgba(249, 250, 251, 0.5);
  }

  .options-container {
    max-height: 0;
    overflow: hidden;
    padding: 0 1rem;
  }

  .options-container.expanded {
    max-height: 300px;
    padding: 0.5rem 1rem 1rem;
    overflow-y: auto;
  }
}
</style>
