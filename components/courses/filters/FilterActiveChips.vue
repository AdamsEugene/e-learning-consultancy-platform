<!-- components/courses/filters/FilterActiveChips.vue -->
<script setup lang="ts">
interface Props {
  filters: {
    category: string;
    instructor: string;
    price: string;
    level: string;
    rating: number;
    duration: string;
  };
  isAnimating: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: "filter-change", type: string, value: string | number): void;
}>();

// Clear a specific filter
const clearFilter = (type: string, defaultValue: string | number) => {
  emit("filter-change", type, defaultValue);
};

// For animation classes
const animationClass = computed(() => {
  return props.isAnimating ? "animate-bounce-once" : "";
});
</script>

<template>
  <div class="mb-6 border-b border-gray-200 pb-6">
    <div
      class="flex flex-wrap gap-2 filter-chips-container"
      :class="animationClass"
    >
      <!-- Category chip -->
      <div
        v-if="filters.category !== 'all'"
        class="filter-chip bg-indigo-100 text-indigo-800"
      >
        <span class="chip-text">{{ filters.category }}</span>
        <button
          class="chip-close-btn text-indigo-600 hover:text-indigo-800"
          aria-label="Clear category filter"
          @click="clearFilter('category', 'all')"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>

      <!-- Instructor chip -->
      <div
        v-if="filters.instructor !== 'all'"
        class="filter-chip bg-green-100 text-green-800"
      >
        <span class="chip-text">{{ filters.instructor }}</span>
        <button
          class="chip-close-btn text-green-600 hover:text-green-800"
          aria-label="Clear instructor filter"
          @click="clearFilter('instructor', 'all')"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>

      <!-- Price chip -->
      <div
        v-if="filters.price !== 'all'"
        class="filter-chip bg-purple-100 text-purple-800"
      >
        <span class="chip-text">{{ filters.price }}</span>
        <button
          class="chip-close-btn text-purple-600 hover:text-purple-800"
          aria-label="Clear price filter"
          @click="clearFilter('price', 'all')"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>

      <!-- Level chip -->
      <div
        v-if="filters.level !== 'All Levels'"
        class="filter-chip bg-blue-100 text-blue-800"
      >
        <span class="chip-text">{{ filters.level }}</span>
        <button
          class="chip-close-btn text-blue-600 hover:text-blue-800"
          aria-label="Clear level filter"
          @click="clearFilter('level', 'All Levels')"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>

      <!-- Rating chip -->
      <div
        v-if="filters.rating !== 0"
        class="filter-chip bg-yellow-100 text-yellow-800"
      >
        <span class="chip-text flex items-center">
          {{ filters.rating }}+
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-3.5 w-3.5 ml-1 text-yellow-500"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
            />
          </svg>
        </span>
        <button
          class="chip-close-btn text-yellow-600 hover:text-yellow-800"
          aria-label="Clear rating filter"
          @click="clearFilter('rating', 0)"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>

      <!-- Duration chip -->
      <div
        v-if="filters.duration !== 'any'"
        class="filter-chip bg-red-100 text-red-800"
      >
        <span class="chip-text">{{ filters.duration }}</span>
        <button
          class="chip-close-btn text-red-600 hover:text-red-800"
          aria-label="Clear duration filter"
          @click="clearFilter('duration', 'any')"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.filter-chips-container {
  position: relative;
  z-index: 1;
}

.filter-chip {
  display: flex;
  align-items: center;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.875rem;
  position: relative;
  overflow: hidden;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.filter-chip:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.filter-chip:active {
  transform: translateY(0);
}

.chip-text {
  position: relative;
  z-index: 1;
}

.chip-close-btn {
  margin-left: 0.25rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 9999px;
  transition: all 0.2s;
  z-index: 1;
}

.chip-close-btn:hover {
  transform: scale(1.1);
}

.chip-close-btn:active {
  transform: scale(0.95);
}

/* Animation for clearing filters */
@keyframes bounceOnce {
  0%,
  100% {
    transform: translateY(0);
  }
  25% {
    transform: translateY(-5px);
  }
  50% {
    transform: translateY(0);
  }
  75% {
    transform: translateY(-2px);
  }
}

.animate-bounce-once {
  animation: bounceOnce 0.5s ease-in-out;
}

/* Gradient shine effect */
.filter-chip::after {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    to right,
    transparent 0%,
    rgba(255, 255, 255, 0.2) 50%,
    transparent 100%
  );
  animation: shine 3s infinite;
}

@keyframes shine {
  0% {
    left: -100%;
  }
  20%,
  100% {
    left: 100%;
  }
}
</style>
