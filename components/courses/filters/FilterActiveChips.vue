<!-- components/courses/filters/FilterActiveChips.vue -->
<script setup lang="ts">
// Props
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

defineProps<Props>();

// Emits
const emit = defineEmits<{
  (e: "filter-change", type: string, value: string | number): void;
}>();

// Helper method to get price label
const getPriceLabel = (value: string): string => {
  switch (value) {
    case "free":
      return "Free";
    case "paid":
      return "Paid";
    case "under50":
      return "Under $50";
    case "under100":
      return "Under $100";
    case "over100":
      return "$100 & Above";
    default:
      return value;
  }
};

// Helper method to get duration label
const getDurationLabel = (value: string): string => {
  switch (value) {
    case "short":
      return "Short (0-3h)";
    case "medium":
      return "Medium (3-10h)";
    case "long":
      return "Long (10h+)";
    default:
      return value;
  }
};

// Handle removing a filter
const removeFilter = (type: string) => {
  // Reset to default value based on filter type
  switch (type) {
    case "category":
      emit("filter-change", type, "all");
      break;
    case "instructor":
      emit("filter-change", type, "all");
      break;
    case "price":
      emit("filter-change", type, "all");
      break;
    case "level":
      emit("filter-change", type, "All Levels");
      break;
    case "rating":
      emit("filter-change", type, 0);
      break;
    case "duration":
      emit("filter-change", type, "any");
      break;
  }
};
</script>

<template>
  <div class="flex flex-wrap gap-2 mb-4">
    <!-- Category chip -->
    <div
      v-if="filters.category !== 'all'"
      class="inline-flex items-center bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-medium transition-all hover:bg-indigo-200"
      :class="{ 'animate-bounce': isAnimating }"
    >
      <span class="mr-1">Category:</span>
      <span class="font-bold">{{ filters.category }}</span>
      <button
        class="ml-1 text-indigo-600 hover:text-indigo-800 focus:outline-none"
        aria-label="Remove category filter"
        @click="removeFilter('category')"
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

    <!-- Instructor chip -->
    <div
      v-if="filters.instructor !== 'all'"
      class="inline-flex items-center bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium transition-all hover:bg-green-200"
      :class="{ 'animate-bounce': isAnimating }"
    >
      <span class="mr-1">Instructor:</span>
      <span class="font-bold">{{ filters.instructor }}</span>
      <button
        class="ml-1 text-green-600 hover:text-green-800 focus:outline-none"
        aria-label="Remove instructor filter"
        @click="removeFilter('instructor')"
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

    <!-- Price chip -->
    <div
      v-if="filters.price !== 'all'"
      class="inline-flex items-center bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium transition-all hover:bg-purple-200"
      :class="{ 'animate-bounce': isAnimating }"
    >
      <span class="mr-1">Price:</span>
      <span class="font-bold">{{ getPriceLabel(filters.price) }}</span>
      <button
        class="ml-1 text-purple-600 hover:text-purple-800 focus:outline-none"
        aria-label="Remove price filter"
        @click="removeFilter('price')"
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

    <!-- Level chip -->
    <div
      v-if="filters.level !== 'All Levels'"
      class="inline-flex items-center bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium transition-all hover:bg-blue-200"
      :class="{ 'animate-bounce': isAnimating }"
    >
      <span class="mr-1">Level:</span>
      <span class="font-bold">{{ filters.level }}</span>
      <button
        class="ml-1 text-blue-600 hover:text-blue-800 focus:outline-none"
        aria-label="Remove level filter"
        @click="removeFilter('level')"
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

    <!-- Rating chip -->
    <div
      v-if="filters.rating > 0"
      class="inline-flex items-center bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium transition-all hover:bg-yellow-200"
      :class="{ 'animate-bounce': isAnimating }"
    >
      <span class="mr-1">Rating:</span>
      <div class="flex items-center">
        <div class="flex text-yellow-500">
          <svg
            v-for="i in Math.floor(filters.rating)"
            :key="i"
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
            />
          </svg>
        </div>
        <span class="ml-1 font-bold">& Up</span>
      </div>
      <button
        class="ml-1 text-yellow-600 hover:text-yellow-800 focus:outline-none"
        aria-label="Remove rating filter"
        @click="removeFilter('rating')"
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

    <!-- Duration chip -->
    <div
      v-if="filters.duration !== 'any'"
      class="inline-flex items-center bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium transition-all hover:bg-red-200"
      :class="{ 'animate-bounce': isAnimating }"
    >
      <span class="mr-1">Duration:</span>
      <span class="font-bold">{{ getDurationLabel(filters.duration) }}</span>
      <button
        class="ml-1 text-red-600 hover:text-red-800 focus:outline-none"
        aria-label="Remove duration filter"
        @click="removeFilter('duration')"
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
/* Bounce animation for clearing filters */
@keyframes bounce {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-5px);
  }
}

.animate-bounce {
  animation: bounce 0.5s ease-in-out;
}
</style>
